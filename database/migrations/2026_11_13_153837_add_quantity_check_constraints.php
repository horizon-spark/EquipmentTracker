<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::unprepared(<<<'SQL'
                CREATE TRIGGER supplies_quantity_non_negative_insert
                BEFORE INSERT ON supplies
                FOR EACH ROW WHEN NEW.quantity < 0
                BEGIN
                    SELECT RAISE(ABORT, 'supplies.quantity must be non-negative');
                END;

                CREATE TRIGGER supplies_quantity_non_negative_update
                BEFORE UPDATE OF quantity ON supplies
                FOR EACH ROW WHEN NEW.quantity < 0
                BEGIN
                    SELECT RAISE(ABORT, 'supplies.quantity must be non-negative');
                END;

                CREATE TRIGGER service_supplies_quantity_used_positive_insert
                BEFORE INSERT ON service_supplies
                FOR EACH ROW WHEN NEW.quantity_used <= 0
                BEGIN
                    SELECT RAISE(ABORT, 'service_supplies.quantity_used must be positive');
                END;

                CREATE TRIGGER service_supplies_quantity_used_positive_update
                BEFORE UPDATE OF quantity_used ON service_supplies
                FOR EACH ROW WHEN NEW.quantity_used <= 0
                BEGIN
                    SELECT RAISE(ABORT, 'service_supplies.quantity_used must be positive');
                END;
                SQL);

            return;
        }

        DB::statement('ALTER TABLE supplies ADD CONSTRAINT supplies_quantity_non_negative CHECK (quantity >= 0)');
        DB::statement('ALTER TABLE service_supplies ADD CONSTRAINT service_supplies_quantity_used_positive CHECK (quantity_used > 0)');
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::unprepared(<<<'SQL'
                DROP TRIGGER IF EXISTS supplies_quantity_non_negative_insert;
                DROP TRIGGER IF EXISTS supplies_quantity_non_negative_update;
                DROP TRIGGER IF EXISTS service_supplies_quantity_used_positive_insert;
                DROP TRIGGER IF EXISTS service_supplies_quantity_used_positive_update;
                SQL);

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE supplies DROP CONSTRAINT supplies_quantity_non_negative');
            DB::statement('ALTER TABLE service_supplies DROP CONSTRAINT service_supplies_quantity_used_positive');

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE supplies DROP CHECK supplies_quantity_non_negative');
            DB::statement('ALTER TABLE service_supplies DROP CHECK service_supplies_quantity_used_positive');

            return;
        }

        DB::statement('ALTER TABLE supplies DROP CONSTRAINT supplies_quantity_non_negative');
        DB::statement('ALTER TABLE service_supplies DROP CONSTRAINT service_supplies_quantity_used_positive');
    }
};
