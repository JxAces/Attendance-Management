<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('dept_no');
            $table->string('id_no');
            $table->string('full_name');
            $table->string('year_level');
            $table->string('major');
            $table->string('department_program');
            $table->string('gender');
            $table->date('registration_date');
            $table->string('scholarship_status');
            $table->string('address');
            $table->float('gpa')->nullable();
            $table->integer('total_units')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}

