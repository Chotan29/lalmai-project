<?php
/* One-time: move ledger rows of Optional-mapped students from the main subject's
   schedule to the Optional twin's schedule. ?key=x29tmp&apply=yes to write. */
if (($_GET['key'] ?? '') !== 'x29tmp') { http_response_code(403); exit('Forbidden'); }
$base = dirname(__DIR__);
require $base.'/vendor/autoload.php';
$app = require $base.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
header('Content-Type: text/plain');
$apply = ($_GET['apply'] ?? '') === 'yes';
echo $apply ? "=== APPLY MODE ===\n" : "=== DRY RUN (no change) ===\n";

$norm = function ($c) { return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $c)); };

$optionals = \App\Models\Subject::where('sub_type', 'like', '%option%')->get(['id', 'code', 'title']);
$movedTotal = 0;
foreach ($optionals as $opt) {
    $nc = $norm($opt->code);
    if (strpos($nc, 'O') !== 0) continue;
    $mainCode = substr($nc, 1);
    if ($mainCode === '') continue;
    $main = \App\Models\Subject::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(code,'-',''),' ',''),'_','')) = ?", [$mainCode])
        ->where('id', '!=', $opt->id)
        ->where(function ($q) { $q->whereNull('sub_type')->orWhere('sub_type', 'not like', '%option%'); })
        ->first(['id', 'code', 'title']);
    if (!$main) continue;
    $optStudentIds = \App\Models\StudentSubject::where('subjects_id', $opt->id)->pluck('students_id')->all();
    if (!$optStudentIds) continue;

    $mainSchedules = \App\Models\ExamSchedule::where('subjects_id', $main->id)->get();
    foreach ($mainSchedules as $ms) {
        $rows = \App\Models\ExamMarkLedger::where('exam_schedule_id', $ms->id)
            ->whereIn('students_id', $optStudentIds)->get(['id', 'students_id']);
        if ($rows->isEmpty()) continue;

        $os = \App\Models\ExamSchedule::where([
            ['years_id', '=', $ms->years_id], ['months_id', '=', $ms->months_id],
            ['exams_id', '=', $ms->exams_id], ['faculty_id', '=', $ms->faculty_id],
            ['semesters_id', '=', $ms->semesters_id], ['subjects_id', '=', $opt->id],
        ])->first();

        echo sprintf("%s [%s] sched#%d -> %s [%s] %s : %d row(s)\n",
            trim($main->title), $main->code, $ms->id, trim($opt->title), $opt->code,
            $os ? 'sched#'.$os->id : 'SCHEDULE MISSING (will create)', $rows->count());

        if (!$apply) { $movedTotal += $rows->count(); continue; }

        if (!$os) {
            $os = \App\Models\ExamSchedule::create([
                'years_id' => $ms->years_id, 'months_id' => $ms->months_id,
                'exams_id' => $ms->exams_id, 'faculty_id' => $ms->faculty_id,
                'semesters_id' => $ms->semesters_id, 'subjects_id' => $opt->id,
                'exam_date' => $ms->exam_date, 'start_time' => $ms->start_time, 'end_time' => $ms->end_time,
                'full_mark_theory' => $ms->full_mark_theory, 'pass_mark_theory' => $ms->pass_mark_theory,
                'full_mark_practical' => $ms->full_mark_practical, 'pass_mark_practical' => $ms->pass_mark_practical,
                'status' => $ms->status ?? 'active', 'created_by' => 1,
            ]);
            echo "  created schedule #".$os->id."\n";
        }
        foreach ($rows as $r) {
            $dup = \App\Models\ExamMarkLedger::where('exam_schedule_id', $os->id)
                ->where('students_id', $r->students_id)->exists();
            if ($dup) {
                \App\Models\ExamMarkLedger::where('id', $r->id)->delete();
                echo "  student ".$r->students_id.": duplicate in optional, main row deleted\n";
            } else {
                \App\Models\ExamMarkLedger::where('id', $r->id)->update(['exam_schedule_id' => $os->id]);
                $movedTotal++;
            }
        }
    }
}
echo ($apply ? "MOVED: " : "WOULD MOVE: ").$movedTotal." row(s)\n";
