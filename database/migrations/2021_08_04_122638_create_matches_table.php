<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('match_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('match_id');
            $table->string('team');
            $table->float('rating',6,2)->default(1500);
            $table->float('rating_deviation',5,2)->default(350);
            $table->decimal('rating_volatility',9,8)->default(0.06);
            $table->tinyInteger('result')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
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
        Schema::dropIfExists('matches');
    }
}
