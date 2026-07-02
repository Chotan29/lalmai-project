<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Services\InovaceApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AllocationChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $runId;
    public array $personIdentifiers;
    public array $deviceIdsOrIdentifiers;

    public function __construct(int $runId, array $personIdentifiers, array $deviceIdsOrIdentifiers)
    {
        $this->runId = $runId;
        $this->personIdentifiers = $personIdentifiers;
        $this->deviceIdsOrIdentifiers = $deviceIdsOrIdentifiers;
        $this->onQueue('long');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::find($this->runId); if(!$run) return;
        $result = $run->result ?? ['upserted'=>[], 'revoked'=>0, 'errors'=>[], 'allocated'=>['ok'=>0,'total'=>0]];

        $alloc = $api->batchAllocations('allocate', $this->personIdentifiers, $this->deviceIdsOrIdentifiers);

        $ok=0; $total = count($this->personIdentifiers) * max(1, count($this->deviceIdsOrIdentifiers));
        if (isset($alloc['data']['by_device']) && is_array($alloc['data']['by_device'])) {
            foreach ($alloc['data']['by_device'] as $dv) $ok += (int)($dv['ok'] ?? 0);
        } elseif (isset($alloc['ok'])) {
            $ok = (int)$alloc['ok'];
        }

        $result['allocated']['ok']    = (int)($result['allocated']['ok'] ?? 0) + $ok;
        $result['allocated']['total'] = (int)($result['allocated']['total'] ?? 0) + $total;

        $run->update(['result'=>$result,'done_steps'=>$run->done_steps+1]);
    }
}
