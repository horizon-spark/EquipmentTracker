<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('ru_RU');

        return [
            'city' => $faker->city(),
            'office' => $faker->randomElement([
                'Аппарат управления',
                'Офис',
                'Склад',
                'Сервисный центр',
            ]).' №'.$faker->numberBetween(1, 20),
            'address' => $faker->streetAddress(),
        ];
    }
}
