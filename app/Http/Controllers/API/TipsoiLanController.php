<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceTask;
use Illuminate\Support\Facades\Log;

/**
 * LAN callbacks for FastFace: Heartbeat -> (optional) tasks -> task result.
 * Device hits these endpoints after you set the heartbeat URL on device.
 */
class TipsoiLanController extends Controller
{
    // Device POSTs heartbeat payload regularly; we return { "result": true|false }.
    public function heartBeatCallback(Request $r){
        // Save last seen?
        Log::info('LAN Heartbeat', ['ip'=>$r->ip(), 'payload'=>$r->all()]);
        $hasTask = DeviceTask::where('device_ip', $r->ip())->where('status','queued')->exists();
        return response()->json(['result' => $hasTask]); // true -> device calls /tasks next. :contentReference[oaicite:26]{index=26}
    }

    // Device polls tasks only if heartbeat returned true
    public function tasks(Request $r){
        $tasks = DeviceTask::where('device_ip',$r->ip())->where('status','queued')->take(5)->get();
        foreach ($tasks as $t){ $t->status='sent'; $t->sent_at=now(); $t->save(); }
        return response()->json($tasks->map(function($t){
            return ['id'=>$t->id,'action'=>$t->action,'payload'=>$t->payload];
        }));
    }

    // Device posts task processing result
    public function taskResult(Request $r){
        $data = $r->validate(['id'=>'required|integer','success'=>'required|boolean','message'=>'nullable|string']);
        $task = DeviceTask::find($data['id']);
        if (!$task) return response()->json(['ok'=>false], 404);
        $task->status = $data['success'] ? 'done':'failed';
        $task->last_error = $data['success'] ? null : ($data['message'] ?? 'failed');
        $task->done_at = now();
        $task->save();
        return response()->json(['ok'=>true]);
    }

    // Fingerprint registration callback
    public function fingerRegCallback(Request $r){
        Log::info('LAN FingerReg CB', $r->all()); // contains deviceKey, personId, time, feature etc. :contentReference[oaicite:27]{index=27}
        return response()->json(['ok'=>true]);
    }
}
