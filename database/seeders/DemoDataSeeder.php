<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Service;
use App\Models\Supply;
use App\Models\TypeEquipment;
use Database\Factories\SupplyFactory;
use Database\Factories\TypeEquipmentFactory;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database with demo equipment tracking data.
     */
    public function run(): void
    {
        $locations = Location::factory()->count(5)->create();

        $employees = Employee::factory()
            ->count(15)
            ->recycle($locations)
            ->create();

        $types = collect(TypeEquipmentFactory::catalog())
            ->map(fn (array $attributes) => TypeEquipment::factory()->create($attributes));

        $supplies = collect(SupplyFactory::catalog())
            ->map(fn (string $name) => Supply::factory()->create([
                'name_supplies' => $name,
                'quantity' => fake()->numberBetween(300, 500),
            ]));

        $assignedEquipment = Equipment::factory()
            ->count(20)
            ->recycle($types)
            ->recycle($employees)
            ->assigned()
            ->create();

        $stockEquipment = Equipment::factory()
            ->count(10)
            ->recycle($types)
            ->create();

        $equipment = $assignedEquipment->merge($stockEquipment);

        $equipment->random(5)->each(
            fn (Equipment $item) => $item->update(['serial_number' => null])
        );

        $equipment->random(25)->each(function (Equipment $item) use ($supplies): void {
            $service = Service::factory()
                ->for($item, 'equipment')
                ->create();

            $usedSupplies = $supplies->random(fake()->numberBetween(1, 3));

            foreach ($usedSupplies as $supply) {
                $service->supplies()->attach($supply->id_supply, [
                    'quantity_used' => fake()->numberBetween(1, 5),
                ]);
            }
        });
    }
}
