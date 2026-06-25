<?php
require_once 'bootstrap/autoload.php';
$app = require_once 'bootstrap/app.php';
$db = $app->make('db');

echo "=== SEMESTER CHECK ===\n\n";

// Science semesters
echo "Science Program (Faculty ID: 32):\n";
$science = $db->table('faculties')
    ->join('faculty_semester', 'faculties.id', '=', 'faculty_semester.faculty_id')
    ->join('semesters', 'faculty_semester.semester_id', '=', 'semesters.id')
    ->where('faculties.id', 32)
    ->select('semesters.id', 'semesters.semester', 'semesters.slug')
    ->orderBy('semesters.semester')
    ->get();

foreach ($science as $sem) {
    $subjects = $db->table('semester_subject')->where('semester_id', $sem->id)->count();
    echo "  - ID: {$sem->id}, Semester: {$sem->semester}, Slug: {$sem->slug}, Subjects: $subjects\n";
}

// Account semesters
echo "\nAccount/Accounting Department:\n";
$accounting = $db->table('faculties')
    ->where('name', 'like', '%Account%')
    ->select('id', 'name')
    ->first();

if ($accounting) {
    $accSem = $db->table('faculties')
        ->join('faculty_semester', 'faculties.id', '=', 'faculty_semester.faculty_id')
        ->join('semesters', 'faculty_semester.semester_id', '=', 'semesters.id')
        ->where('faculties.id', $accounting->id)
        ->select('semesters.id', 'semesters.semester', 'semesters.slug')
        ->orderBy('semesters.semester')
        ->get();
    
    echo "  Faculty: {$accounting->name} (ID: {$accounting->id})\n";
    foreach ($accSem as $sem) {
        $subjects = $db->table('semester_subject')->where('semester_id', $sem->id)->count();
        echo "    - ID: {$sem->id}, Semester: {$sem->semester}, Slug: {$sem->slug}, Subjects: $subjects\n";
    }
}

echo "\n";
?>
