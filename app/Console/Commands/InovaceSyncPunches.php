<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Attendance\Device\TipsoiUnifiedController;
use App\Services\InovaceApi;

class InovaceSyncPunches extends Command
{
    protected $signature = 'inovace:sync-punches {--start=} {--end=}';
    protected $description = 'Pull punch logs from Inovace/TIPSOI and merge into local Attendance';

    public function handle(InovaceApi $api)
    {
        $ctrl = new TipsoiUnifiedController($api);

        // build a fake request to reuse controller method
        $req = Request::create('', 'POST', [
            'start' => $this->option('start'),
            'end'   => $this->option('end'),
        ]);

        $res = $ctrl->storeAttendanceLogs($req);
        $data = $res->getData(true);

        $this->info('Synced: ' . ($data['synced'] ?? 0) . ' rows');
        $this->info('Cursor: ' . ($data['cursor'] ?? 'null'));

        return 0;
    }
}
