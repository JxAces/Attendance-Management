<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\AttendanceLevel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('m_in')->default(AttendanceLevel::Absent->value); // Morning In
            $table->integer('m_out')->default(AttendanceLevel::Absent->value); // Morning Out
            $table->integer('af_in')->default(AttendanceLevel::Absent->value); // Afternoon In
            $table->integer('af_out')->default(AttendanceLevel::Absent->value); // Afternoon Out
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('m_in'); // Morning In
            $table->dropColumn('m_out'); // Morning Out
            $table->dropColumn('af_in'); // Afternoon In
            $table->dropColumn('af_out'); // Afternoon Out
        });
    }
};
