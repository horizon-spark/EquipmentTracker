<?php

namespace Tests\Feature;

use App\Filament\Resources\Equipment\Pages\ListEquipment;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\TypeEquipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EquipmentFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_can_be_filtered_by_employee(): void
    {
        [$firstEquipment, $secondEquipment] = $this->createEquipmentPairs();
        $employee = Employee::query()->where('full_name', 'Иванов Иван')->firstOrFail();

        Livewire::test(ListEquipment::class)
            ->filterTable('id_employee', $employee->id_employee)
            ->assertCanSeeTableRecords([$firstEquipment])
            ->assertCanNotSeeTableRecords([$secondEquipment]);
    }

    public function test_equipment_can_be_filtered_by_type(): void
    {
        [$firstEquipment, $secondEquipment] = $this->createEquipmentPairs();
        $type = TypeEquipment::query()->where('name_type', 'Принтер')->firstOrFail();

        Livewire::test(ListEquipment::class)
            ->filterTable('id_type', $type->id_type)
            ->assertCanSeeTableRecords([$firstEquipment])
            ->assertCanNotSeeTableRecords([$secondEquipment]);
    }

    public function test_equipment_can_be_filtered_by_location(): void
    {
        [$firstEquipment, $secondEquipment] = $this->createEquipmentPairs();
        $location = Location::query()->where('city', 'Мурманск')->firstOrFail();

        Livewire::test(ListEquipment::class)
            ->filterTable('id_location', $location->id_location)
            ->assertCanSeeTableRecords([$firstEquipment])
            ->assertCanNotSeeTableRecords([$secondEquipment]);
    }

    /**
     * @return array{Equipment, Equipment}
     */
    private function createEquipmentPairs(): array
    {
        $this->actingAs(User::factory()->create());

        $firstLocation = Location::factory()->create([
            'city' => 'Мурманск',
            'office' => 'Офис №1',
        ]);
        $secondLocation = Location::factory()->create([
            'city' => 'Санкт-Петербург',
            'office' => 'Офис №2',
        ]);

        $firstEmployee = Employee::factory()->create([
            'full_name' => 'Иванов Иван',
            'id_location' => $firstLocation->id_location,
        ]);
        $secondEmployee = Employee::factory()->create([
            'full_name' => 'Петров Пётр',
            'id_location' => $secondLocation->id_location,
        ]);

        $firstType = TypeEquipment::factory()->create([
            'name_type' => 'Принтер',
            'manufacturer' => 'Pantum',
            'model' => 'P2500W',
        ]);
        $secondType = TypeEquipment::factory()->create([
            'name_type' => 'Монитор',
            'manufacturer' => 'Dell',
            'model' => 'P2422H',
        ]);

        $firstEquipment = Equipment::factory()->create([
            'inv_number' => 'INV-FILTER-1',
            'id_type' => $firstType->id_type,
            'id_employee' => $firstEmployee->id_employee,
        ]);
        $secondEquipment = Equipment::factory()->create([
            'inv_number' => 'INV-FILTER-2',
            'id_type' => $secondType->id_type,
            'id_employee' => $secondEmployee->id_employee,
        ]);

        return [$firstEquipment, $secondEquipment];
    }
}
