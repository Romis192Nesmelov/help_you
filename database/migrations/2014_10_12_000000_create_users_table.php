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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('avatar',30)->nullable();
            $table->string('name')->nullable();
            $table->string('family')->nullable();
            $table->string('born',10)->nullable();
            $table->string('phone',50)->unique();
            $table->string('email')->nullable();
            $table->string('code',8)->nullable();
            $table->string('password')->nullable();
            $table->text('info_about')->nullable();
            $table->boolean('active')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
