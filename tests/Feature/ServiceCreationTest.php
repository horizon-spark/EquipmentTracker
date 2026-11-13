<?php

namespace Tests\Feature;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Models\Equipment;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_be_created_with_a_supply(): void
    {
        $this->actingAs(User::factory()->create());

        $equipment = Equipment::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);

        Livewire::test(CreateService::class)
            ->fillForm([
                'id_equipment' => $equipment->id_equipment,
                'service_date' => now()->toDateString(),
                'description' => 'Тестовое обслуживание',
                'supplies' => [
                    [
                        'id_supply' => $supply->id_supply,
                        'quantity_used' => 3,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('service', [
            'id_equipment' => $equipment->id_equipment,
            'description' => 'Тестовое обслуживание',
        ]);
        $this->assertDatabaseHas('service_supplies', [
            'id_supply' => $supply->id_supply,
            'quantity_used' => 3,
        ]);
        $this->assertSame(7, $supply->fresh()->quantity);
    }

    public function test_service_creation_is_rolled_back_when_there_is_not_enough_stock(): void
    {
        $this->actingAs(User::factory()->create());

        $equipment = Equipment::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 2]);

        Livewire::test(CreateService::class)
            ->fillForm([
                'id_equipment' => $equipment->id_equipment,
                'service_date' => now()->toDateString(),
                'description' => 'Тестовое обслуживание',
                'supplies' => [
                    [
                        'id_supply' => $supply->id_supply,
                        'quantity_used' => 5,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasFormErrors([
                'supplies.0.quantity_used' => 'Количество не может превышать остаток на складе (2).',
            ]);

        $this->assertDatabaseCount('service', 0);
        $this->assertDatabaseCount('service_supplies', 0);
        $this->assertSame(2, $supply->fresh()->quantity);
    }
}
