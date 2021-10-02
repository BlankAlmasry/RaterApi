<?php

namespace App\Providers;

use Blankalmasry\Glicko2\Rating\Rating;
use Blankalmasry\Glicko2\Glicko2;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        $this->app->bind('rating',function (){
           return new Rating();
        });
        $this->app->singleton('glicko',function (){
           return new Glicko2();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
