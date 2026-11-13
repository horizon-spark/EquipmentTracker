<?php

namespace Database\Factories;

use App\Models\Supply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supply>
 */
class SupplyFactory extends Factory
{
    protected $model = Supply::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_supplies' => fake()->randomElement(static::catalog()),
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function catalog(): array
    {
        return [
            'Картридж Pantum',
            'Тонер HP',
            'Оперативная память DDR4 8 ГБ',
            'SSD 512 ГБ',
            'Мышь проводная',
            'Клавиатура USB',
            'Кабель HDMI 2 м',
            'Бумага А4',
            'Сетевой фильтр',
            'Патч-корд UTP cat.5e',
        ];
    }
}
