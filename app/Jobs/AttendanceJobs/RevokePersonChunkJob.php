<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Services\InovaceApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RevokePersonChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $runId;
    public array $identifiers;

    public function __construct(int $runId, array $identifiers)
    {
        $this->runId = $runId;
        $this->identifiers = $identifiers;
        $this->onQueue('long');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::find($this->runId); if(!$run) return;
        $result = $run->result ?? ['upserted'=>[], 'revoked'=>0, 'errors'=>[], 'allocated'=>['ok'=>0,'total'=>0]];

        foreach($this->identifiers as $id){
            $rv=$api->revokePerson($id,null);
            if (!empty($rv['error'])) $result['errors'][]=['who'=>$id,'err'=>$rv['message'] ?? 'revoke failed'];
            else $result['revoked'] = (int)($result['revoked'] ?? 0) + 1;
        }

        $run->update(['result'=>$result,'done_steps'=>$run->done_steps+1]);
    }
}
