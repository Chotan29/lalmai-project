<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ID card validity per department, because one rule cannot serve all ten.
 *
 * The card has always worked the expiry out one way: 31 December of the year after the session
 * ends. That is right for HSC and wrong for everyone else. A student admitted to a four year
 * honours course in 2025-2026 was being given a card that expired in 2027, two years before they
 * finish. Seven of the ten departments were printing the wrong date.
 *
 * The rule the college actually uses is: session start year + the length of the course, and then
 * the last day of the month the course ends in. HSC ends in June, degree and honours in December.
 * Both are month ends, so only the month has to be stored - the card fills in 30 or 31 itself.
 *
 *   Science / Business / Humanities   2 years, June      2025-2026 -> 30 June 2027
 *   B.A / BBS / BSS Degree            3 years, December  2025-2026 -> 31 December 2028
 *   Accounting / Management /
 *   English / Marketing               4 years, December  2025-2026 -> 31 December 2029
 *
 * Two new columns rather than reading the existing duration column, which is free text and holds
 * '1', '1 year', '4 Year', '4 YEAR', '3' and '3 Years'. That is fine for a human reading a form
 * and no basis for arithmetic that dates a student's identity card. The values are seeded FROM
 * duration where a number can be read out of it, so the office starts from something sensible
 * rather than ten empty boxes, but from then on the two are independent.
 *
 * Both nullable. A department with these left empty falls back to the global setting and then to
 * the old rule, so no card is ever printed without a date.
 */
class AddIdCardValidityToFacultiesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('faculties', 'id_card_valid_years')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->smallInteger('id_card_valid_years')->unsigned()->nullable()->after('registration_validate');
            });
        }

        if (!Schema::hasColumn('faculties', 'id_card_expiry_month')) {
            Schema::table('faculties', function (Blueprint $table) {
                $table->tinyInteger('id_card_expiry_month')->unsigned()->nullable()->after('id_card_valid_years');
            });
        }

        $this->seedFromDuration();
    }

    /**
     * Read a course length out of the free text duration column, where one can be read.
     *
     * Only fills rows that are still empty, so running this again cannot overwrite a value the
     * office has since corrected by hand.
     */
    private function seedFromDuration()
    {
        $rows = DB::table('faculties')
            ->select('id', 'faculty', 'duration')
            ->whereNull('id_card_valid_years')
            ->get();

        foreach ($rows as $row) {
            /* '4 Year', '3 Years', '1 year', '3' all yield their number. Anything with no number
               in it is left alone for the office to fill in. */
            if (!preg_match('/(\d+)/', (string) $row->duration, $m)) {
                continue;
            }
            $years = (int) $m[1];
            if ($years < 1 || $years > 10) {
                continue;
            }

            /* Science, Business and Humanities carry duration '1' - one year per class, two
               classes to an HSC - so the course is twice the stored figure. Recognised by the
               class names the semesters use, not by department name, so a renamed department
               still lands in the right place. */
            $isHsc = DB::table('faculty_semester as fs')
                ->join('semesters as s', 's.id', '=', 'fs.semester_id')
                ->where('fs.faculty_id', $row->id)
                ->where(function ($q) {
                    $q->where('s.semester', 'like', '%Eleven%')->orWhere('s.semester', 'like', '%Twelve%');
                })
                ->exists();

            if ($isHsc) {
                /* An HSC is two years however the duration column happens to be worded. */
                $years = 2;
                $month = 6;
            } else {
                $month = 12;
            }

            DB::table('faculties')->where('id', $row->id)->update([
                'id_card_valid_years'  => $years,
                'id_card_expiry_month' => $month,
            ]);
        }
    }

    public function down()
    {
        foreach (['id_card_valid_years', 'id_card_expiry_month'] as $column) {
            if (Schema::hasColumn('faculties', $column)) {
                Schema::table('faculties', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
