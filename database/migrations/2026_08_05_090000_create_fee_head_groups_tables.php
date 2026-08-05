<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * A fee the student sees as one line, made of the heads the accounts need.
 *
 * Admission is 7,400 taka to a student and 26 heads to the office: 23 college heads totalling
 * 4,600 and 3 department heads totalling 2,800. Exam, transport and hostel fees have the same
 * shape. Until now the whole payment landed in a single "ADMISSION FEE" head, so there was no
 * head-wise figure, no treasury share, and no way to tell whether a given payment included the
 * department's part.
 *
 * fee_heads stays exactly as it is - the permanent, reusable list. A head like লাইব্রেরি is
 * created once and attached wherever it is needed, at whatever amount that fee calls for, so
 * "how much came in under লাইব্রেরি this year" stays a single question with a single answer.
 * The amount therefore belongs on the join, not on the head.
 *
 * Money is still written against ordinary fee_heads rows, exactly as today. This layer only
 * describes how a fee is composed, so every existing collection screen and report keeps working
 * untouched.
 */
class CreateFeeHeadGroupsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_head_groups')) {
            Schema::create('fee_head_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('title', 150);
                $table->string('session', 30)->nullable();   // "2025-2026"
                $table->text('description')->nullable();

                /* What the student is asked to pay. The items must add up to exactly this, and
                   the builder screen refuses to save while they do not - which is what keeps the
                   payment code free of any question about proportional shares or rounding. */
                $table->decimal('total_amount', 11, 2)->default(0);

                /* Set the moment money is first taken against this group. Editing it afterwards
                   would rewrite what students have already paid, so a new session is a duplicate
                   rather than an edit. */
                $table->boolean('is_locked')->default(0);

                $table->boolean('status')->default(1);
            });
        }

        if (!Schema::hasTable('fee_head_group_items')) {
            Schema::create('fee_head_group_items', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('fee_head_group_id');
                $table->unsignedInteger('fee_head_id');

                $table->decimal('amount', 11, 2)->default(0);

                /* Drives the order money fills these heads in, and it carries real meaning here:
                   the college heads come first and the department heads last, so a student who
                   pays only the college's 4,600 leaves the department heads at zero rather than
                   part-filling them in whatever order the database happened to return. */
                $table->unsignedSmallInteger('sort_order')->default(0);

                $table->foreign('fee_head_group_id')
                    ->references('id')->on('fee_head_groups')
                    ->onDelete('cascade');

                /* Restrict, not cascade: a head that has been billed must not be deletable out
                   from under the groups and collections that refer to it. Deactivate instead. */
                $table->foreign('fee_head_id')
                    ->references('id')->on('fee_heads')
                    ->onDelete('restrict');

                $table->unique(['fee_head_group_id', 'fee_head_id']);
            });
        }

        if (Schema::hasTable('fee_heads')) {
            Schema::table('fee_heads', function (Blueprint $table) {
                /* Who actually receives this money. The 2,800 marked 'department' is collected
                   by the college but belongs to the departments, and has to be handed over. */
                if (!Schema::hasColumn('fee_heads', 'collected_by')) {
                    $table->enum('collected_by', ['college', 'department'])
                        ->default('college')->after('fee_head_amount');
                }

                /* বিদ্যুৎ 10, ভর্তি 25, টিউশন 300 go to the government treasury and need their own
                   challan. A flag turns a memorised list into a query. */
                if (!Schema::hasColumn('fee_heads', 'is_treasury')) {
                    $table->boolean('is_treasury')->default(0)->after('collected_by');
                }

                /* Replaces the hard-coded [61, 74, 75] that is currently written twice - in
                   config/api.php and FeeMaster::$excludedFeeHeads. Two copies of one truth drift
                   apart eventually; this puts it on the head itself. */
                if (!Schema::hasColumn('fee_heads', 'hide_from_student')) {
                    $table->boolean('hide_from_student')->default(0)->after('is_treasury');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_head_group_items');
        Schema::dropIfExists('fee_head_groups');

        if (Schema::hasTable('fee_heads')) {
            Schema::table('fee_heads', function (Blueprint $table) {
                foreach (['hide_from_student', 'is_treasury', 'collected_by'] as $col) {
                    if (Schema::hasColumn('fee_heads', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
}
