<?php
if (($_GET['token'] ?? '') !== 'ACC3_SYNC_20260519') {
    die('Unauthorized');
}

require __DIR__ . '/index.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

$semester_id = 164;
$titles = [
    'Advanced Accounting-I',
    'Audit and Assurance',
    'Banking and Insurance Theories, Laws and Accounts',
    'Business and Commercial Laws',
    'Cost Accounting',
    'Entrepreneurship',
    'Financial Management',
    'Management Accounting'
];

$created_subject_count = 0;
$mapped_count = 0;
$now = Carbon::now();

foreach ($titles as $title) {
    $subject = DB::table('subjects')->where('title', $title)->first();
    if (!$subject) {
        $code = 'ACC3-' . rand(1000, 9999) . '-' . Str::random(3);
        $subject_id = DB::table('subjects')->insertGetId([
            'title' => $title,
            'code' => $code,
            'status' => 1,
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        $created_subject_count++;
    } else {
        $subject_id = $subject->id;
    }

    $affected = DB::table('semester_subject')->updateOrInsert(
        ['semester_id' => $semester_id, 'subject_id' => $subject_id],
        [
            'status' => 1,
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]
    );
    $mapped_count++;
}

$final_titles = DB::table('semester_subject')
    ->where('semester_id', $semester_id)
    ->join('subjects', 'subjects.id', '=', 'semester_subject.subject_id')
    ->orderBy('subjects.title')
    ->pluck('subjects.title')
    ->toArray();

echo json_encode([
    'created_subject_count' => $created_subject_count,
    'mapped_count' => $mapped_count,
    'final_titles' => $final_titles
]);
