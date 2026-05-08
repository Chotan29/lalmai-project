<?php
// database/seeds/AttendanceStatusSeeder.php
use Illuminate\Database\Seeder;
use App\Models\AttendanceStatus;

class AttendanceStatusSeeder extends Seeder
{
    public function run()
    {
        // Canonical set used across the UI
        $rows = [
            ['code' => 'P',  'label' => 'Present',    'color' => '#10B981', 'order' => 1],
            ['code' => 'A',  'label' => 'Absent',     'color' => '#EF4444', 'order' => 2],
            ['code' => 'L',  'label' => 'Late',       'color' => '#F59E0B', 'order' => 3],
            ['code' => 'E',  'label' => 'Excused',    'color' => '#3B82F6', 'order' => 4], // was "Early Leave" before
            ['code' => 'HL', 'label' => 'Half-Leave', 'color' => '#7C3AED', 'order' => 5], // purple to match UI
        ];

        foreach ($rows as $row) {
            // Preserve created_at if exists; upsert everything else
            $status = AttendanceStatus::firstOrNew(['code' => strtoupper($row['code'])]);
            $status->label = $row['label'];
            $status->color = $row['color'];
            $status->order = $row['order'];
            if (!$status->exists) {
                $status->created_at = now();
            }
            $status->updated_at = now();
            $status->save();
        }
    }
}
