<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Services\InovaceApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpsertPersonChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $runId;
    public array $people;

    public function __construct(int $runId, array $people)
    {
        $this->runId  = $runId;
        $this->people = $people;
        $this->onQueue('long');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::find($this->runId); if(!$run) return;
        $result = $run->result ?? ['upserted'=>[], 'revoked'=>0, 'errors'=>[], 'allocated'=>['ok'=>0,'total'=>0]];

        foreach($this->people as $p){
            $payload = [
                'identifier'=>$p['identifier'],
                'name'=>$p['name'],
                'person_type'=>'employee',
                'primary_display_text'=>$p['pdt'],
                'secondary_display_text'=>$p['sdt'],
            ];
            if (!empty($p['rfid'])) $payload['rfid']=$p['rfid'];

            $res = $api->upsertPersonSafe($payload, $p['img'] ?? null);
            if (!($res['ok'] ?? false)) {
                $result['errors'][]=['who'=>$p['identifier'],'err'=>$res['message'] ?? 'upsert failed'];
            } else {
                $result['upserted'][]=$p['identifier'];
            }
        }

        $result['upserted']=array_values(array_unique($result['upserted']));
        $run->update(['result'=>$result,'done_steps'=>$run->done_steps+1]);
    }
}
