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
            'service_date' => now()->toDateString(),
        ]);

        $this->expectException(QueryException::class);

        $service->equipment->delete();
    }

    public function test_deleting_service_removes_its_supply_links(): void
    {
        $service = Service::create([
            'id_equipment' => $this->createEquipment()->id_equipment,
            'service_date' => now()->toDateString(),
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
            'service_date' => now()->toDateString(),
        ]);

        $supply = Supply::create([
            'name_supplies' => 'Тонер',
            'quantity' => 2,
        ]);

        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 1]);

        $this->expectException(QueryException::class);

        $supply->delete();
    }

    public function test_inventory_number_must_be_unique(): void
    {
        $this->createEquipment('INV-002');

        $this->expectException(QueryException::class);

        Equipment::create([
            'id_type' => TypeEquipment::create(['name_type' => 'МФУ'])->id_type,
            'inv_number' => 'INV-002',
        ]);
    }

    public function test_serial_number_must_be_unique_when_present(): void
    {
        $this->createEquipment('INV-003', 'SN-001');

        $this->expectException(QueryException::class);

        Equipment::create([
            'id_type' => TypeEquipment::create(['name_type' => 'МФУ'])->id_type,
            'inv_number' => 'INV-004',
            'serial_number' => 'SN-001',
        ]);
    }

    public function test_equipment_may_have_empty_serial_numbers(): void
    {
        $this->createEquipment('INV-005');
        $this->createEquipment('INV-006');

        $this->assertDatabaseCount('equipment', 2);
    }

    public function test_supply_quantity_cannot_be_negative(): void
    {
        $this->expectException(QueryException::class);

        Supply::create([
            'name_supplies' => 'Тонер',
            'quantity' => -1,
        ]);
    }

    public function test_service_supply_quantity_used_must_be_positive(): void
    {
        $service = Service::create([
            'id_equipment' => $this->createEquipment()->id_equipment,
            'service_date' => now()->toDateString(),
        ]);

        $supply = Supply::create([
            'name_supplies' => 'Тонер',
            'quantity' => 1,
        ]);

        $this->expectException(QueryException::class);

        DB::table('service_supplies')->insert([
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
            'quantity_used' => 0,
        ]);
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

    private function createEquipment(string $invNumber = 'INV-002', ?string $serialNumber = null): Equipment
    {
        return Equipment::create([
            'id_type' => TypeEquipment::create(['name_type' => 'Принтер'])->id_type,
            'inv_number' => $invNumber,
            'serial_number' => $serialNumber,
        ]);
    }
}
