<?php
// app/Http/Controllers/Attendance/AttendanceActionController.php
namespace App\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Services\AttendanceService;

class AttendanceActionController extends Controller
{
    public function __construct(){ $this->middleware('auth'); }

    public function mark($id, Request $r, AttendanceService $svc){
        $row = Attendance::with('master')->findOrFail($id);
        if ($row->master->is_locked) return response()->json(['ok'=>false,'message'=>'Locked'], 422);
        $data = $r->validate(['code'=>'required|string']);
        $row = $svc->mark($row, $data['code'], ['by'=>auth()->id(),'via'=>'manual']);
        return response()->json(['ok'=>true,'row'=>$row]);
    }

    public function check($id, Request $r, AttendanceService $svc){
        $row = Attendance::with('master')->findOrFail($id);
        if ($row->master->is_locked) return response()->json(['ok'=>false,'message'=>'Locked'], 422);
        $row = $svc->toggleCheckInOut($row);
        return response()->json(['ok'=>true,'row'=>$row]);
    }
}
