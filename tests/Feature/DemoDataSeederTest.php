<?php

namespace Tests\Feature;

use App\Models\Equipment;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_seeder_creates_consistent_test_data(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('location', 5);
        $this->assertDatabaseCount('employee', 15);
        $this->assertDatabaseCount('type_equipment', 10);
        $this->assertDatabaseCount('supplies', 10);
        $this->assertDatabaseCount('equipment', 30);
        $this->assertDatabaseCount('service', 25);

        $this->assertSame(20, Equipment::whereNotNull('id_employee')->count());
        $this->assertSame(10, Equipment::whereNull('id_employee')->count());
        $this->assertSame(5, Equipment::whereNull('serial_number')->count());

        $this->assertGreaterThan(0, DB::table('service_supplies')->count());
        $this->assertSame(
            0,
            DB::table('service_supplies')->where('quantity_used', '<=', 0)->count()
        );
        $this->assertSame(
            0,
            DB::table('supplies')->where('quantity', '<', 0)->count()
        );
    }
}
