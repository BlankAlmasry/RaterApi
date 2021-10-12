<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\Client;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable
{
    use HasFactory;
    use HasSlug;

    protected $fillable = ['name', 'client_id'];

    public function games()
    {
        return $this->belongsToMany(Game::class)->withPivot('rating', 'rating_deviation', 'rating_volatility', 'wins', 'loses')->withTimestamps();
    }

    public function matches()
    {
        return $this->belongsToMany(MatchUp::class, 'match_user', 'user_id', 'match_id')->withPivot('team', 'result')->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
