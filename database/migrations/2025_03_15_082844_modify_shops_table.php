<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            // Drop the `opening_hours` column
            $table->dropColumn('opening_hours');

            // Add `open_time` and `close_time` columns
            $table->string('open_time')->nullable();  // Stores the opening time (e.g., "9:00 AM")
            $table->string('close_time')->nullable(); // Stores the closing time (e.g., "6:00 PM")
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            // Revert the changes by adding back `opening_hours` and dropping `open_time` and `close_time`
            $table->json('opening_hours')->nullable();
            $table->dropColumn('open_time');
            $table->dropColumn('close_time');
        });
    }
}
