<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Game extends Model
{
    use HasFactory;

    use HasSlug;

    protected $fillable = ['name', 'client_id'];


    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('rating', 'rating_deviation', 'rating_volatility', 'wins', 'loses')
            ->withTimestamps();
    }

    public function matches()
    {
        return $this->hasMany(MatchUp::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function matchUsers()
    {
        return $this->hasManyThrough(User::class, MatchUp::class);
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
