<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\OrderType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subtypes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(OrderType::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtypes');
    }
};
