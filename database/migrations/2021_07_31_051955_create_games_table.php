<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Symfony\Component\Translation\t;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('client_id');
            $table->foreign('client_id')->references('id')->on('oauth_clients');
            $table->timestamps();
        });
        Schema::create('game_user', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('game_id');
            $table->float('rating',6,2)->default(1500);
            $table->float('rating_deviation',5,2)->default(350);
            $table->decimal('rating_volatility',9,8)->default(0.06);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->primary(['game_id','user_id']);
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
        Schema::dropIfExists('games');
    }
}
