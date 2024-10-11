<?php

use App\Core\DB;
use Illuminate\Database\Schema\Blueprint;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::schema()->drop('users');
    }
};
