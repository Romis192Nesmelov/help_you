<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\OrderType;
use \App\Models\Subtype;
use \App\Models\User;
use \App\Models\City;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(OrderType::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Subtype::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name',50);
            $table->smallInteger('need_performers');
            $table->string('address');
            $table->double('latitude',8, 6);
            $table->double('longitude',8, 6);
            $table->string('description_short')->nullable();
            $table->text('description_full')->nullable();
            $table->boolean('approved');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
