<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Indexes for the columns these screens filter and join on, which had none.
 *
 * A where or a join on an unindexed column reads the whole table. audits is 89,000 rows and
 * two thirds of the entire database, and the User Activity screen looks a record up in it by
 * auditable_type and auditable_id - so every visit to that screen reads all of it.
 *
 * The online payment columns matter less today (230 rows) and more every term: the report
 * filters on the gateway and the verify state and joins on students_id.
 *
 * Each index is added only if it is not already there, so this can be run on a database that
 * has had some of them added by hand without failing half way.
 */
class AddIndexesForAuditAndPaymentReports extends Migration
{
    /**
     * table => [index name => columns]
     */
    private function wanted()
    {
        return [
            'audits' => [
                /* The pair, in the order the lookup uses them: type narrows first. */
                'audits_auditable_type_id_index' => ['auditable_type', 'auditable_id'],
                'audits_created_at_index'        => ['created_at'],
            ],
            'online_payments' => [
                'online_payments_students_id_index'     => ['students_id'],
                'online_payments_status_index'          => ['status'],
                'online_payments_payment_gateway_index' => ['payment_gateway'],
                'online_payments_date_index'            => ['date'],
            ],
            'students' => [
                'students_reg_date_index' => ['reg_date'],
            ],
            'fee_collections' => [
                'fee_collections_date_index'   => ['date'],
                'fee_collections_status_index' => ['status'],
            ],
        ];
    }

    /**
     * Is there already an index over exactly these columns, whatever it is called?
     *
     * Asking by name is not enough. audits already carries an index over
     * (auditable_type, auditable_id) created by the audit package under its own name, and a
     * name check would happily add a second one over the same pair - dead weight that is
     * written on every insert and read by nothing.
     */
    private function coveredAlready($table, $columns)
    {
        $byName = [];
        foreach (DB::select("SHOW INDEX FROM `{$table}`") as $ix) {
            $byName[$ix->Key_name][(int) $ix->Seq_in_index] = $ix->Column_name;
        }

        $wanted = implode(',', $columns);
        foreach ($byName as $cols) {
            ksort($cols);
            if (implode(',', $cols) === $wanted) {
                return true;
            }
        }

        return false;
    }

    public function up()
    {
        foreach ($this->wanted() as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                foreach ($columns as $c) {
                    if (!Schema::hasColumn($table, $c)) {
                        continue 2;
                    }
                }
                if ($this->coveredAlready($table, $columns)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                    $t->index($columns, $name);
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->wanted() as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                /* By name here, on purpose: only the indexes this migration created itself are
                   dropped, never one that was already on the table under another name. */
                if (!DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name])) {
                    continue;
                }

                Schema::table($table, function (Blueprint $t) use ($name) {
                    $t->dropIndex($name);
                });
            }
        }
    }
}
