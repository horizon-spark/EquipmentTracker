<?php

namespace Tests\Feature;

use App\Filament\Exports\EquipmentExporter;
use App\Filament\Exports\LocationExporter;
use App\Filament\Exports\ServiceExporter;
use App\Filament\Imports\EmployeeImporter;
use App\Filament\Imports\EquipmentImporter;
use App\Filament\Imports\LocationImporter;
use App\Filament\Imports\ServiceImporter;
use App\Filament\Imports\SupplyImporter;
use App\Filament\Imports\TypeEquipmentImporter;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Service;
use App\Models\Supply;
use App\Models\TypeEquipment;
use App\Models\User;
use Filament\Actions\ImportAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CsvImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_can_be_imported(): void
    {
        LocationImporter::test()
            ->import([
                'city' => 'Мурманск',
                'office' => 'Офис №1',
                'address' => 'ул. Лобова, д. 100',
            ])
            ->assertImported();

        $this->assertDatabaseHas('location', [
            'city' => 'Мурманск',
            'office' => 'Офис №1',
            'address' => 'ул. Лобова, д. 100',
        ]);
    }

    public function test_employee_import_creates_missing_location_when_address_is_provided(): void
    {
        EmployeeImporter::test()
            ->import([
                'full_name' => 'Иванов Иван Иванович',
                'city' => 'Мурманск',
                'office' => 'Офис №1',
                'address' => 'ул. Лобова, д. 100',
            ])
            ->assertImported();

        $this->assertDatabaseHas('location', ['city' => 'Мурманск', 'office' => 'Офис №1']);
        $this->assertDatabaseHas('employee', ['full_name' => 'Иванов Иван Иванович']);
    }

    public function test_employee_import_fails_when_location_is_missing_and_address_is_blank(): void
    {
        EmployeeImporter::test()
            ->import([
                'full_name' => 'Иванов Иван Иванович',
                'city' => 'Мурманск',
                'office' => 'Неизвестный офис',
            ])
            ->assertHasRowFailure();

        $this->assertDatabaseCount('employee', 0);
    }

    public function test_type_equipment_and_supply_can_be_imported(): void
    {
        TypeEquipmentImporter::test()
            ->import([
                'name_type' => 'Принтер',
                'manufacturer' => 'Pantum',
                'model' => 'P2500W',
            ])
            ->assertImported();

        SupplyImporter::test()
            ->import([
                'name_supplies' => 'Картридж Pantum',
                'quantity' => '15',
            ])
            ->assertImported();

        $this->assertDatabaseHas('type_equipment', ['name_type' => 'Принтер', 'model' => 'P2500W']);
        $this->assertDatabaseHas('supplies', ['name_supplies' => 'Картридж Pantum', 'quantity' => 15]);
    }

    public function test_equipment_import_resolves_type_and_employee(): void
    {
        $type = TypeEquipment::factory()->create([
            'name_type' => 'Принтер',
            'manufacturer' => 'Pantum',
            'model' => 'P2500W',
        ]);

        $employee = Employee::factory()->create([
            'full_name' => 'Иванов Иван Иванович',
        ]);

        EquipmentImporter::test()
            ->import([
                'inv_number' => 'INV-100',
                'serial_number' => 'SN-100',
                'name_type' => $type->name_type,
                'manufacturer' => $type->manufacturer,
                'model' => $type->model,
                'full_name' => $employee->full_name,
                'assignment_date' => '10.09.2026',
            ])
            ->assertImported();

        $equipment = Equipment::query()->where('inv_number', 'INV-100')->firstOrFail();

        $this->assertTrue($equipment->type->is($type));
        $this->assertTrue($equipment->employee->is($employee));
        $this->assertSame('2026-09-10', $equipment->assignment_date->format('Y-m-d'));
    }

    public function test_equipment_import_fails_when_employee_does_not_exist(): void
    {
        EquipmentImporter::test()
            ->import([
                'inv_number' => 'INV-200',
                'name_type' => 'Принтер',
                'full_name' => 'Неизвестный Сотрудник',
            ])
            ->assertHasRowFailure();

        $this->assertDatabaseMissing('equipment', ['inv_number' => 'INV-200']);
    }

    public function test_service_can_be_imported(): void
    {
        $equipment = Equipment::factory()->create(['inv_number' => 'INV-300']);

        ServiceImporter::test()
            ->import([
                'equipment_inv_number' => $equipment->inv_number,
                'service_date' => '11.09.2026',
                'description' => 'Плановое обслуживание',
            ])
            ->assertImported();

        $this->assertDatabaseHas('service', [
            'id_equipment' => $equipment->id_equipment,
            'description' => 'Плановое обслуживание',
        ]);
    }

    public function test_location_export_contains_expected_columns(): void
    {
        $location = Location::factory()->create([
            'city' => 'Мурманск',
            'office' => 'Офис №1',
            'address' => 'ул. Лобова, д. 100',
        ]);

        $row = LocationExporter::test()->export($location);

        $this->assertSame([
            (string) $location->id_location,
            'Мурманск',
            'Офис №1',
            'ул. Лобова, д. 100',
        ], $row);
    }

    public function test_equipment_export_contains_relations_and_formatted_date(): void
    {
        $equipment = Equipment::factory()->create([
            'inv_number' => 'INV-400',
            'serial_number' => 'SN-400',
            'assignment_date' => '2026-09-10',
        ]);
        $equipment->load(['type', 'employee.location']);

        $row = EquipmentExporter::test()->export($equipment);

        $this->assertSame('INV-400', $row[1]);
        $this->assertSame('SN-400', $row[2]);
        $this->assertSame($equipment->type->name_type, $row[3]);
        $this->assertSame('10.09.2026', $row[9]);
    }

    public function test_import_action_dispatches_location_import(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->create());

        $imports = ImportAction::fake();

        Livewire::test(ListLocations::class)
            ->mountAction('import')
            ->fillForm([
                'file' => UploadedFile::fake()->createWithContent(
                    'locations.csv',
                    "city,office,address\nТестоград,Офис 99,\"ул. Тестовая, 1\"\n",
                ),
            ])
            ->fillForm([
                'columnMap' => [
                    'city' => 'city',
                    'office' => 'office',
                    'address' => 'address',
                ],
                'updateExisting' => true,
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors();

        $imports->assertDispatched(LocationImporter::class);
    }

    public function test_service_export_includes_supplies_summary(): void
    {
        $equipment = Equipment::factory()->create();
        $service = Service::factory()->for($equipment, 'equipment')->create([
            'service_date' => '2026-09-10',
        ]);
        $supply = Supply::factory()->create([
            'name_supplies' => 'Картридж Pantum',
            'quantity' => 10,
        ]);

        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 2]);
        $service->load(['equipment', 'supplies']);

        $row = ServiceExporter::test()->export($service);

        $this->assertSame('10.09.2026', $row[1]);
        $this->assertSame('Картридж Pantum x2', $row[4]);
    }
}
