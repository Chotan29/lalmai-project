<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationRun extends Model
{
    protected $table = 'integration_runs';

    protected $fillable = [
        'type', 'status', 'total_steps', 'done_steps',
        'payload', 'result', 'error', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result'  => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function getPercentAttribute(): int
    {
        $tot = (int) ($this->total_steps ?? 0);
        $done = (int) ($this->done_steps ?? 0);
        if ($tot <= 0) return 0;
        return (int) round(($done / $tot) * 100);
    }

    public function toArray()
    {
        $arr = parent::toArray();
        $arr['percent'] = $this->percent;
        return $arr;
    }
}
