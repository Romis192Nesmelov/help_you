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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id', false, true);
            $table->foreign('subscriber_id')->references('id')->on('users')->onUpdate('cascade');
            $table->bigInteger('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_subscriber_id_foreign');
            $table->dropColumn('subscriber_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('subscriptions');
    }
};
