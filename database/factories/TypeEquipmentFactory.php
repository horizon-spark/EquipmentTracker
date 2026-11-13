<?php

namespace Database\Factories;

use App\Models\TypeEquipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TypeEquipment>
 */
class TypeEquipmentFactory extends Factory
{
    protected $model = TypeEquipment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return fake()->randomElement(static::catalog());
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function catalog(): array
    {
        return [
            ['name_type' => 'Ноутбук', 'manufacturer' => 'Lenovo', 'model' => 'ThinkPad T14'],
            ['name_type' => 'Ноутбук', 'manufacturer' => 'HP', 'model' => 'ProBook 450 G9'],
            ['name_type' => 'Принтер', 'manufacturer' => 'Pantum', 'model' => 'P2500W'],
            ['name_type' => 'МФУ', 'manufacturer' => 'Kyocera', 'model' => 'M2040dn'],
            ['name_type' => 'Монитор', 'manufacturer' => 'Dell', 'model' => 'P2422H'],
            ['name_type' => 'Системный блок', 'manufacturer' => 'Dell', 'model' => 'OptiPlex 7010'],
            ['name_type' => 'Сканер', 'manufacturer' => 'Epson', 'model' => 'Perfection V39'],
            ['name_type' => 'ИБП', 'manufacturer' => 'APC', 'model' => 'Back-UPS BX650CI'],
            ['name_type' => 'Коммутатор', 'manufacturer' => 'TP-Link', 'model' => 'TL-SG1024D'],
            ['name_type' => 'Клавиатура', 'manufacturer' => 'Logitech', 'model' => 'K120'],
        ];
    }
}
