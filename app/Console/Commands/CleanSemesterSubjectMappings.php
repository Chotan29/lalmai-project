<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanSemesterSubjectMappings extends Command
{
    protected $signature = 'integrity:clean-semester-subject
                            {--semester_id=* : Limit cleanup to one or more semester IDs}
                            {--remove-inactive : Also remove mappings to inactive subjects}
                            {--dry-run : Show counts only without deleting rows}';

    protected $description = 'Remove orphan semester_subject mappings that reference missing or inactive subjects';

    public function handle()
    {
        $semesterIds = array_filter((array) $this->option('semester_id'), static function ($value) {
            return $value !== null && $value !== '';
        });

        $removeInactive = (bool) $this->option('remove-inactive');
        $dryRun = (bool) $this->option('dry-run');

        $missingQuery = DB::table('semester_subject as ss')
            ->leftJoin('subjects as sub', 'sub.id', '=', 'ss.subject_id')
            ->whereNull('sub.id');

        if (!empty($semesterIds)) {
            $missingQuery->whereIn('ss.semester_id', $semesterIds);
        }

        $inactiveQuery = DB::table('semester_subject as ss')
            ->join('subjects as sub', 'sub.id', '=', 'ss.subject_id')
            ->where('sub.status', 0);

        if (!empty($semesterIds)) {
            $inactiveQuery->whereIn('ss.semester_id', $semesterIds);
        }

        $missingCount = (clone $missingQuery)->count('ss.id');
        $inactiveCount = $removeInactive ? (clone $inactiveQuery)->count('ss.id') : 0;

        if ($dryRun) {
            $this->info('Dry run summary:');
            $this->line('Missing subject mappings: ' . $missingCount);
            $this->line('Inactive subject mappings to remove: ' . $inactiveCount);
            return 0;
        }

        $deletedMissing = DB::table('semester_subject')
            ->whereIn('id', (clone $missingQuery)->pluck('ss.id'))
            ->delete();

        $deletedInactive = 0;
        if ($removeInactive) {
            $deletedInactive = DB::table('semester_subject')
                ->whereIn('id', (clone $inactiveQuery)->pluck('ss.id'))
                ->delete();
        }

        $this->info('Cleanup finished.');
        $this->line('Deleted missing subject mappings: ' . $deletedMissing);
        $this->line('Deleted inactive subject mappings: ' . $deletedInactive);

        return 0;
    }
}
