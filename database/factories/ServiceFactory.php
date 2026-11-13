<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_equipment' => Equipment::factory(),
            'service_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description' => fake()->randomElement([
                'Плановое техобслуживание',
                'Чистка от пыли',
                'Замена картриджа',
                'Заправка картриджа',
                'Диагностика неисправности',
                'Замена комплектующих',
                'Обновление прошивки',
                'Настройка после перемещения',
            ]),
        ];
    }
}
