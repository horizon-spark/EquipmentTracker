<?php

namespace Tests\Feature;

use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\TypeEquipment\Pages\ListTypeEquipment;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Service;
use App\Models\TypeEquipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TableFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_employees_can_be_filtered_by_city(): void
    {
        [$firstEmployee, $secondEmployee] = $this->createEmployeePairs();

        Livewire::test(ListEmployees::class)
            ->filterTable('city', 'Мурманск')
            ->assertCanSeeTableRecords([$firstEmployee])
            ->assertCanNotSeeTableRecords([$secondEmployee]);
    }

    public function test_employees_can_be_filtered_by_office(): void
    {
        [$firstEmployee, $secondEmployee] = $this->createEmployeePairs();

        Livewire::test(ListEmployees::class)
            ->filterTable('office', 'Мурманск|Офис №1')
            ->assertCanSeeTableRecords([$firstEmployee])
            ->assertCanNotSeeTableRecords([$secondEmployee]);
    }

    public function test_services_can_be_filtered_by_equipment(): void
    {
        [$firstService, $secondService, $firstEquipment] = $this->createServicePairs();

        Livewire::test(ListServices::class)
            ->filterTable('id_equipment', $firstEquipment->id_equipment)
            ->assertCanSeeTableRecords([$firstService])
            ->assertCanNotSeeTableRecords([$secondService]);
    }

    public function test_services_can_be_filtered_by_type(): void
    {
        [$firstService, $secondService, $firstEquipment] = $this->createServicePairs();

        Livewire::test(ListServices::class)
            ->filterTable('id_type', $firstEquipment->id_type)
            ->assertCanSeeTableRecords([$firstService])
            ->assertCanNotSeeTableRecords([$secondService]);
    }

    public function test_services_can_be_filtered_by_date_range(): void
    {
        [$firstService, $secondService] = $this->createServicePairs();

        Livewire::test(ListServices::class)
            ->filterTable('service_date', [
                'from' => '2026-09-01',
                'until' => '2026-09-30',
            ])
            ->assertCanSeeTableRecords([$firstService])
            ->assertCanNotSeeTableRecords([$secondService]);
    }

    public function test_locations_can_be_filtered_by_office(): void
    {
        $firstLocation = Location::factory()->create([
            'city' => 'Мурманск',
            'office' => 'Офис №1',
        ]);
        $secondLocation = Location::factory()->create([
            'city' => 'Санкт-Петербург',
            'office' => 'Офис №2',
        ]);

        Livewire::test(ListLocations::class)
            ->filterTable('office', $firstLocation->id_location)
            ->assertCanSeeTableRecords([$firstLocation])
            ->assertCanNotSeeTableRecords([$secondLocation]);
    }

    public function test_type_equipment_can_be_filtered_by_name(): void
    {
        [$firstType, $secondType] = $this->createTypePairs();

        Livewire::test(ListTypeEquipment::class)
            ->filterTable('name_type', 'Принтер')
            ->assertCanSeeTableRecords([$firstType])
            ->assertCanNotSeeTableRecords([$secondType]);
    }

    public function test_type_equipment_can_be_filtered_by_manufacturer(): void
    {
        [$firstType, $secondType] = $this->createTypePairs();

        Livewire::test(ListTypeEquipment::class)
            ->filterTable('manufacturer', 'Pantum')
            ->assertCanSeeTableRecords([$firstType])
            ->assertCanNotSeeTableRecords([$secondType]);
    }

    /**
     * @return array{Employee, Employee}
     */
    private function createEmployeePairs(): array
    {
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

        return [$firstEmployee, $secondEmployee];
    }

    /**
     * @return array{Service, Service, Equipment}
     */
    private function createServicePairs(): array
    {
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
            'inv_number' => 'INV-SERVICE-1',
            'id_type' => $firstType->id_type,
        ]);
        $secondEquipment = Equipment::factory()->create([
            'inv_number' => 'INV-SERVICE-2',
            'id_type' => $secondType->id_type,
        ]);

        $firstService = Service::factory()->for($firstEquipment, 'equipment')->create([
            'service_date' => '2026-09-10',
            'description' => 'Сентябрьское обслуживание',
        ]);
        $secondService = Service::factory()->for($secondEquipment, 'equipment')->create([
            'service_date' => '2026-10-10',
            'description' => 'Октябрьское обслуживание',
        ]);

        return [$firstService, $secondService, $firstEquipment];
    }

    /**
     * @return array{TypeEquipment, TypeEquipment}
     */
    private function createTypePairs(): array
    {
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

        return [$firstType, $secondType];
    }
}
