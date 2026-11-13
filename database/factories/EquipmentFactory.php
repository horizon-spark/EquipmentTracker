<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\TypeEquipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_type' => TypeEquipment::factory(),
            'inv_number' => 'INV-'.Str::upper(Str::random(10)),
            'serial_number' => 'SN-'.Str::upper(Str::random(12)),
            'assignment_date' => null,
            'id_employee' => null,
        ];
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'id_employee' => Employee::factory(),
            'assignment_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'id_employee' => null,
            'assignment_date' => null,
        ]);
    }

    public function withoutSerialNumber(): static
    {
        return $this->state(fn (array $attributes): array => [
            'serial_number' => null,
        ]);
    }
}
