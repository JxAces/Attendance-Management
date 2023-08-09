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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('m_in')->default(false); // Morning In
            $table->boolean('m_out')->default(false); // Morning Out
            $table->boolean('af_in')->default(false); // Afternoon In
            $table->boolean('af_out')->default(false); // Afternoon Out
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
