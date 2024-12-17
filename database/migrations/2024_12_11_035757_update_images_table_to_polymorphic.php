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
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('link_id'); // Remove the old column
            $table->morphs('imageable');  // Add polymorphic columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropMorphs('imageable'); // Remove polymorphic columns
            $table->integer('link_id');    // Restore the old column
        });
    }
};
