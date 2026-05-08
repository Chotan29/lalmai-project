<?php
// app/Models/AttendanceMaster.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AttendanceMaster extends Model
{
    protected $fillable = [
        'date', 'type', 'is_locked',
        'department_id', 'faculty_id', 'semester_id', 'batch_id',
        'start_time', 'end_time', 'shift'
    ];

    protected $dates = ['date'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'attendance_master_id');
    }

    // "today" finder (hidden)
    public static function forToday(string $type): self
    {
        $today = Carbon::today();
        return static::firstOrCreate(['date' => $today, 'type' => $type], ['is_locked' => 0]);
    }
}
