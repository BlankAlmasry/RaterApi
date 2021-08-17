<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class Game extends Model
{
    use HasFactory;

    use HasSlug;

    protected $fillable = ['name',  'ranking_system', 'client_id'];


    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('rating','rating_deviation','rating_volatility')->withTimestamps()->where('client_id',$this->client_id);
    }

    public function matches()
    {
        return $this->hasMany(MatchUp::class)->where('state', '<>', 'deleted');
    }
    public function setRankingSystemAttribute($value){
        if (in_array($value, ['numerical', 'divisional'])) {
            $this->attributes['ranking_system'] = $value;
        };
    }


    public function getSlugOptions() : SlugOptions
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
