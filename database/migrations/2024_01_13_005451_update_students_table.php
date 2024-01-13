<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Make 'department_program' nullable
            $table->string('department_program')->nullable()->change();

            // Make 'gender' nullable
            $table->string('gender')->nullable()->change();

            // Make 'registration_date' nullable
            $table->date('registration_date')->nullable()->change();

            // Make 'scholarship_status' nullable
            $table->string('scholarship_status')->nullable()->change();

            // Make 'address' nullable
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Laravel will handle reversing the changes automatically.
    }
}
