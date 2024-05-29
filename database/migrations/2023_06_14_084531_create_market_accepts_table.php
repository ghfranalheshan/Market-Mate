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
        Schema::create('market_accepts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
            $table->enum('status',['waiting','accepted','rejected']);

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
        Schema::dropIfExists('market_accepts');
    }
};
