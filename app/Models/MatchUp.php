<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchUp extends Model
{
    use HasFactory;
/*
 *
 * */
    protected $fillable = ['game_id','team_length'];

    protected $with = ['game','users'];

    protected $table = "matches";

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'match_user', 'match_id','user_id')->withPivot('team','result','rating','rating_deviation','rating_volatility')->withTimestamps();
    }


}
