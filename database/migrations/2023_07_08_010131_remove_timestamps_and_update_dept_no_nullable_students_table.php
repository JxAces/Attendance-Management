<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTimestampsAndUpdateDeptNoNullableStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Update 'dept_no' column to nullable
            $table->unsignedBigInteger('dept_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add 'created_at' and 'updated_at' columns
            $table->timestamps();

            // Update 'dept_no' column to non-nullable
            $table->unsignedBigInteger('dept_no')->nullable(false)->change();
        });
    }
}
