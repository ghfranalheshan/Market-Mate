<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_up_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->constrained('user')->cascadeOnDelete();
            $table->integer('market_id')->constrained('market')->cascadeOnDelete();
            $table->enum('request_status',['waiting','accepted','rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_up_requests');
    }
};
