<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Service;
use App\Models\Supply;
use App\Models\TypeEquipment;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_with_employees_cannot_be_deleted(): void
    {
        $employee = $this->createEmployee();

        $this->expectException(QueryException::class);

        $employee->location->delete();
    }

    public function test_deleting_employee_unassigns_equipment(): void
    {
        $employee = $this->createEmployee();
        $equipment = Equipment::create([
            'id_type' => TypeEquipment::create(['name_type' => 'Ноутбук'])->id_type,
            'inv_number' => 'INV-001',
            'id_employee' => $employee->id_employee,
        ]);

        $employee->delete();

        $this->assertNull($equipment->fresh()->id_employee);
    }

    public function test_type_equipment_with_equipment_cannot_be_deleted(): void
    {
        $equipment = $this->createEquipment();

        $this->expectException(QueryException::class);

        $equipment->type->delete();
    }

    public function test_equipment_with_services_cannot_be_deleted(): void
    {
        $service = Service::create([
            'id_equipment' => $this->createEquipment()->id_equipment,
            'data_service' => now()->toDateString(),
        ]);

        $this->expectException(QueryException::class);

        $service->equipment->delete();
    }

    public function test_deleting_service_removes_its_supply_links(): void
    {
        $service = Service::create([
            'id_equipment' => $this->createEquipment()->id_equipment,
            'data_service' => now()->toDateString(),
        ]);

        $supply = Supply::create([
            'name_supplies' => 'Картридж',
            'quantity' => 5,
        ]);

        DB::table('service_supplies')->insert([
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
            'quantity_used' => 1,
        ]);

        $service->delete();

        $this->assertDatabaseCount('service_supplies', 0);
        $this->assertDatabaseHas('supplies', ['id_supply' => $supply->id_supply]);
    }

    public function test_supply_used_by_service_cannot_be_deleted(): void
    {
        $service = Service::create([
            'id_equipment' => $this->createEquipment()->id_equipment,
            'data_service' => now()->toDateString(),
        ]);

        $supply = Supply::create([
            'name_supplies' => 'Тонер',
            'quantity' => 2,
        ]);

        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 1]);

        $this->expectException(QueryException::class);

        $supply->delete();
    }

    private function createEmployee(): Employee
    {
        return Employee::create([
            'full_name' => 'Иванов Иван Иванович',
            'id_location' => Location::create([
                'city' => 'Мурманск',
                'office' => 'Офис',
                'address' => 'ул. Лобова, д. 100',
            ])->id_location,
        ]);
    }

    private function createEquipment(): Equipment
    {
        return Equipment::create([
            'id_type' => TypeEquipment::create(['name_type' => 'Принтер'])->id_type,
            'inv_number' => 'INV-002',
        ]);
    }
}
