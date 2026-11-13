<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Supply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SupplyStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_attaching_supply_decrements_stock(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);

        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 3]);

        $this->assertSame(7, $supply->fresh()->quantity);
        $this->assertDatabaseHas('service_supplies', [
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
            'quantity_used' => 3,
        ]);
    }

    public function test_attaching_more_than_available_throws_and_keeps_stock(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 2]);

        try {
            $service->supplies()->attach($supply->id_supply, ['quantity_used' => 3]);
            $this->fail('Ожидалось исключение валидации.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('quantity_used', $exception->errors());
        }

        $this->assertSame(2, $supply->fresh()->quantity);
        $this->assertDatabaseMissing('service_supplies', [
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
        ]);
    }

    public function test_updating_quantity_upwards_decrements_stock_by_delta(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);
        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 2]);

        $service->supplies()->updateExistingPivot($supply->id_supply, ['quantity_used' => 5]);

        $this->assertSame(5, $supply->fresh()->quantity);
    }

    public function test_updating_quantity_downwards_restores_stock_by_delta(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);
        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 5]);

        $service->supplies()->updateExistingPivot($supply->id_supply, ['quantity_used' => 2]);

        $this->assertSame(8, $supply->fresh()->quantity);
    }

    public function test_updating_quantity_beyond_stock_throws_and_keeps_state(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 5]);
        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 2]);

        try {
            $service->supplies()->updateExistingPivot($supply->id_supply, ['quantity_used' => 10]);
            $this->fail('Ожидалось исключение валидации.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('quantity_used', $exception->errors());
        }

        $this->assertSame(3, $supply->fresh()->quantity);
        $this->assertDatabaseHas('service_supplies', [
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
            'quantity_used' => 2,
        ]);
    }

    public function test_detaching_supply_restores_stock(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);
        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 4]);

        $service->supplies()->detach($supply->id_supply);

        $this->assertSame(10, $supply->fresh()->quantity);
        $this->assertDatabaseMissing('service_supplies', [
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
        ]);
    }

    public function test_deleting_service_restores_all_supplies(): void
    {
        $service = Service::factory()->create();

        $firstSupply = Supply::factory()->create(['quantity' => 5]);
        $secondSupply = Supply::factory()->create(['quantity' => 10]);

        $service->supplies()->attach($firstSupply->id_supply, ['quantity_used' => 2]);
        $service->supplies()->attach($secondSupply->id_supply, ['quantity_used' => 3]);

        $this->assertSame(3, $firstSupply->fresh()->quantity);
        $this->assertSame(7, $secondSupply->fresh()->quantity);

        $service->delete();

        $this->assertSame(5, $firstSupply->fresh()->quantity);
        $this->assertSame(10, $secondSupply->fresh()->quantity);
        $this->assertDatabaseMissing('service', ['id_service' => $service->id_service]);
        $this->assertDatabaseCount('service_supplies', 0);
    }

    public function test_quantity_used_cannot_be_less_than_one(): void
    {
        $service = Service::factory()->create();
        $supply = Supply::factory()->create(['quantity' => 10]);
        $service->supplies()->attach($supply->id_supply, ['quantity_used' => 1]);

        try {
            $service->supplies()->updateExistingPivot($supply->id_supply, ['quantity_used' => 0]);
            $this->fail('Ожидалось исключение валидации.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('quantity_used', $exception->errors());
        }

        $this->assertSame(9, $supply->fresh()->quantity);
        $this->assertDatabaseHas('service_supplies', [
            'id_service' => $service->id_service,
            'id_supply' => $supply->id_supply,
            'quantity_used' => 1,
        ]);
    }
}
