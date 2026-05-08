<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReportSemesterSubjectIntegrity extends Command
{
    protected $signature = 'integrity:semester-subject
                            {--semester_id=* : Limit the report to one or more semester IDs}
                            {--log : Write any detected issues to the Laravel log}
                            {--fail-on-issues : Return a non-zero exit code when issues are found}';

    protected $description = 'Report missing or inactive subject records referenced by semester_subject mappings';

    public function handle()
    {
        $shouldLog = (bool) $this->option('log');
        $shouldFailOnIssues = (bool) $this->option('fail-on-issues');
        $semesterIds = array_filter((array) $this->option('semester_id'), static function ($value) {
            return $value !== null && $value !== '';
        });

        $query = DB::table('semester_subject as ss')
            ->leftJoin('semesters as sem', 'sem.id', '=', 'ss.semester_id')
            ->leftJoin('subjects as sub', 'sub.id', '=', 'ss.subject_id')
            ->select([
                'ss.semester_id',
                'ss.subject_id',
                'sem.semester as semester_name',
                'sem.slug as semester_slug',
                'sub.title as subject_title',
                'sub.status as subject_status',
            ])
            ->orderBy('ss.semester_id')
            ->orderBy('ss.subject_id');

        if (!empty($semesterIds)) {
            $query->whereIn('ss.semester_id', $semesterIds);
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $this->warn('No semester_subject mappings found for the requested scope.');
            return 0;
        }

        $issues = $rows->filter(static function ($row) {
            return $row->subject_title === null || (string) $row->subject_status === '0';
        })->values();

        if ($issues->isEmpty()) {
            $this->info('No missing or inactive subject mappings were found.');
            return 0;
        }

        $summary = $issues
            ->groupBy('semester_id')
            ->map(function ($group) {
                return [
                    'semester' => trim(($group->first()->semester_name ?? 'Unknown') . ' ' . (($group->first()->semester_slug ?? '') ? '(' . $group->first()->semester_slug . ')' : '')),
                    'issue_count' => $group->count(),
                    'missing_count' => $group->where('subject_title', null)->count(),
                    'inactive_count' => $group->filter(static function ($row) {
                        return $row->subject_title !== null && (string) $row->subject_status === '0';
                    })->count(),
                ];
            });

        $this->info('Semester-subject integrity issues found:');
        foreach ($summary as $semesterId => $info) {
            $this->line('');
            $this->line('Semester ID ' . $semesterId . ' - ' . $info['semester']);
            $this->line('Total issues: ' . $info['issue_count'] . ', Missing subjects: ' . $info['missing_count'] . ', Inactive subjects: ' . $info['inactive_count']);

            foreach ($issues->where('semester_id', $semesterId) as $issue) {
                if ($issue->subject_title === null) {
                    $this->line('  - subject_id ' . $issue->subject_id . ': missing subject row');
                } else {
                    $this->line('  - subject_id ' . $issue->subject_id . ': inactive subject (' . $issue->subject_title . ')');
                }
            }
        }

        if ($shouldLog) {
            foreach ($summary as $semesterId => $info) {
                $details = $issues->where('semester_id', $semesterId)->map(static function ($issue) {
                    return [
                        'subject_id' => $issue->subject_id,
                        'problem' => $issue->subject_title === null ? 'missing' : 'inactive',
                        'subject_title' => $issue->subject_title,
                    ];
                })->values()->all();

                $payload = [
                    'semester_id' => $semesterId,
                    'semester' => $info['semester'],
                    'issue_count' => $info['issue_count'],
                    'missing_count' => $info['missing_count'],
                    'inactive_count' => $info['inactive_count'],
                    'details' => $details,
                ];

                $logLine = sprintf(
                    "[%s] local.WARNING: Semester-subject integrity issue detected. %s%s",
                    now()->format('Y-m-d H:i:s'),
                    json_encode($payload),
                    PHP_EOL
                );

                file_put_contents(storage_path('logs/laravel.log'), $logLine, FILE_APPEND);
            }
        }

        return $shouldFailOnIssues ? 1 : 0;
    }
}