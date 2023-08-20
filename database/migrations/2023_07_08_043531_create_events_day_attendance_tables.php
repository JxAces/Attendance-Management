<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('id_no')->unique()->change();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('days');
            // Add your event table columns here
            $table->timestamps();
        });

        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->timestamp('sign_in_morning')->nullable();
            $table->timestamp('sign_out_morning')->nullable();
            $table->timestamp('sign_in_afternoon')->nullable();
            $table->timestamp('sign_out_afternoon')->nullable();
            $table->timestamp('date')->nullable();
            // Add your day table columns here

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('day_id');
            $table->unsignedBigInteger('student_id');
            // Add your ev_stu_attendance table columns here

            $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events,days,attendances');
    }
};
