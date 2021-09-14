<?php

namespace App\GameManager;

use App\Models\Game;
use App\Models\MatchUp;
use App\Models\User;

Trait HasGames
{
    public function games()
    {
        return $this->hasMany(Game::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function gameMatches()
    {
        return $this->hasManyThrough(MatchUp::class, Game::class);
    }


}
