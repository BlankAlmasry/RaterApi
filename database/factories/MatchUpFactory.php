<?php

namespace Database\Factories;

use App\Models\MatchUp;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchUpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MatchUp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'game_id' => 1,
            ];
    }
}
