<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDaysTableTimeColumns extends Migration
{
    public function up()
    {
        Schema::table('days', function (Blueprint $table) {
            $table->time('sign_in_morning')->nullable()->change();
            $table->time('sign_out_morning')->nullable()->change();
            $table->time('sign_in_afternoon')->nullable()->change();
            $table->time('sign_out_afternoon')->nullable()->change();
        });
    }

    public function down()
    {
        // Revert the changes if needed
        Schema::table('days', function (Blueprint $table) {
            $table->timestamp('sign_in_morning')->nullable()->change();
            $table->timestamp('sign_out_morning')->nullable()->change();
            $table->timestamp('sign_in_afternoon')->nullable()->change();
            $table->timestamp('sign_out_afternoon')->nullable()->change();
        });
    }
}


