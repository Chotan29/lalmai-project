<?php
if (($_GET['token'] ?? '') !== 'ACC3_SYNC_20260519') {
    die('Unauthorized');
}

require __DIR__ . '/index.php';

use Illuminate\Support\Facades\DB;

$semester_id = 164;
$final_titles = DB::table('semester_subject')
    ->where('semester_id', $semester_id)
    ->join('subjects', 'subjects.id', '=', 'semester_subject.subject_id')
    ->orderBy('subjects.title')
    ->pluck('subjects.title')
    ->toArray();

echo "TITLES_START\n";
foreach($final_titles as $title) {
    echo $title . "\n";
}
echo "TITLES_END\n";
