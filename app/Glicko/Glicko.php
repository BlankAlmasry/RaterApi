<?php
namespace App\Glicko;
use Illuminate\Support\Facades\Facade;
class Glicko extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'glicko';
    }
    public static function ratingToRank($rating)
    {
        $rank = [];
        if ($rating > 2600){
            if ($rating < 2900){
                $rank["rank"] = "Master";
            }
            if ($rating > 2900){
                $rank["rank"] = "Grandmaster";
            }
            if ($rating > 3100){
                $rank["rank"] = "Challenger";
            }
            $rank["points"] = $rating - 2600;
            return $rank ;
        }

        $tiers = ["Iron", "Bronze", "Silver", "Gold", "Platinum", "Diamond", "Master"];
        $divisions = ["IV","III", "II", "I"];
        $lp = 0;
        $rank["rank"] = $tiers[floor(($rating-200)  / 400)] . " " .$divisions[(floor(($rating-200)  / 100) % 4)];;

        $rank["points"] = $rating % 100;
        return $rank;

    }

}
