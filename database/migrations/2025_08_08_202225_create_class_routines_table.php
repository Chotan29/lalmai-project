<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassRoutinesTable extends Migration
{
    
    public function up()
{
    if (!Schema::hasTable('class_routines')) {
        Schema::create('class_routines', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            // Match parent PK types exactly
            $table->unsignedBigInteger('department_id');   // departments.id = BIGINT UNSIGNED (likely)
            $table->unsignedInteger('faculty_id');         // faculties.id   = INT UNSIGNED (legacy)
            $table->unsignedInteger('semester_id');        // semesters.id   = INT UNSIGNED (legacy)
            $table->unsignedInteger('student_batch_id');   // student_batches.id = INT UNSIGNED
            $table->unsignedInteger('subject_id');         // subjects.id    = INT UNSIGNED (adjust if needed)
            $table->unsignedInteger('teacher_id');         // staff.id       = INT UNSIGNED (adjust if needed)

            $table->string('day_of_week');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('room_number');
            $table->string('period')->nullable();

            $table->unsignedInteger('created_by');
            $table->unsignedInteger('last_updated_by')->nullable();
            $table->boolean('status')->default(1);

            $table->index(['department_id','faculty_id','semester_id','student_batch_id'], 'cr_dept_fac_sem_batch_idx');
            $table->index(['subject_id','teacher_id'], 'cr_subject_teacher_idx');

            // FKs
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('student_batch_id')->references('id')->on('student_batches')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('staff')->onDelete('cascade'); // ensure table is 'staff'
        });
    }
}

public function down()
{
    Schema::dropIfExists('class_routines');
}



    //     if (!Schema::hasTable('class_routines')) {
    //         Schema::create('class_routines', function (Blueprint $table) {
    //             $table->engine = 'InnoDB'; // ensure engine

    //             $table->bigIncrements('id');
    //             $table->timestamps();

    //             // Use BIGINT if referenced IDs are bigIncrements
    //             $table->unsignedBigInteger('department_id');
    //             $table->unsignedBigInteger('faculty_id');
    //             $table->unsignedBigInteger('semester_id');
    //             $table->unsignedBigInteger('student_batch_id');
    //             $table->unsignedBigInteger('subject_id');
    //             $table->unsignedBigInteger('teacher_id'); // table 'staff' (singular?) confirm name

    //             $table->string('day_of_week');
    //             $table->string('start_time');
    //             $table->string('end_time');
    //             $table->string('room_number');
    //             $table->string('period')->nullable();

    //             $table->unsignedBigInteger('created_by');
    //             $table->unsignedBigInteger('last_updated_by')->nullable();
    //             $table->boolean('status')->default(1);

    //             // Short index names
    //             $table->index(['department_id','faculty_id','semester_id','student_batch_id'], 'cr_dept_fac_sem_batch_idx');
    //             $table->index(['subject_id','teacher_id'], 'cr_subject_teacher_idx');

    //             // Name FKs explicitly (optional) and ensure correct table names
    //             $table->foreign('department_id', 'cr_dept_fk')->references('id')->on('departments')->onDelete('cascade');
    //             $table->foreign('faculty_id', 'cr_fac_fk')->references('id')->on('faculties')->onDelete('cascade');
    //             $table->foreign('semester_id', 'cr_sem_fk')->references('id')->on('semesters')->onDelete('cascade');
    //             $table->foreign('student_batch_id', 'cr_batch_fk')->references('id')->on('student_batches')->onDelete('cascade');
    //             $table->foreign('subject_id', 'cr_subject_fk')->references('id')->on('subjects')->onDelete('cascade');
    //             $table->foreign('teacher_id', 'cr_teacher_fk')->references('id')->on('staff')->onDelete('cascade'); // change to 'staffs' if needed
    //         });
    //     }
    // }

    // public function down()
    // {
    //     Schema::dropIfExists('class_routines');
    // }


    // public function down()
    // {
    //     if (Schema::hasTable('class_routines')) {
    //         Schema::table('class_routines', function (Blueprint $table) {
    //             // Drop FKs by column (lets Laravel resolve the actual FK name)
    //             $table->dropForeign(['department_id']);
    //             $table->dropForeign(['faculty_id']);
    //             $table->dropForeign(['semester_id']);
    //             $table->dropForeign(['student_batch_id']);
    //             $table->dropForeign(['subject_id']);
    //             $table->dropForeign(['teacher_id']);

    //             // If you *must* drop indexes too:
    //             // Use the custom names you actually created.
    //             $table->dropIndex('cr_dept_fac_sem_batch_idx');
    //             $table->dropIndex('cr_subject_teacher_idx');
    //         });
    //     }

    //     Schema::dropIfExists('class_routines');
    // }


    // public function down()
    // {
    //     if (Schema::hasTable('class_routines')) {
    //         Schema::table('class_routines', function (Blueprint $table) {
    //             // Drop FKs by name
    //             $table->dropForeign('cr_dept_fk');
    //             $table->dropForeign('cr_fac_fk');
    //             $table->dropForeign('cr_sem_fk');
    //             $table->dropForeign('cr_batch_fk');
    //             $table->dropForeign('cr_subject_fk');
    //             $table->dropForeign('cr_teacher_fk');

    //             // Drop indexes by name
    //             $table->dropIndex('cr_dept_fac_sem_batch_idx');
    //             $table->dropIndex('cr_subject_teacher_idx');
    //         });
    //     }
    //     Schema::dropIfExists('class_routines');
    // }
}
