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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->after('email');
            $table->string('gender')->after('phone_number')->nullable();
            $table->integer('age')->after('gender')->nullable();
            $table->unsignedBigInteger('role_id')->after('age');
            $table->unsignedBigInteger('salary_id')->after('role_id');

            // Foreign key constraints
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('salary_id')->references('id')->on('salaries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('gender');
            $table->dropColumn('age');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropForeign(['salary_id']);
            $table->dropColumn('salary_id');
        });
    }
};
