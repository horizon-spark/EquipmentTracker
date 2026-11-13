<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->bigIncrements('id_equipment');

            $table->foreignId('id_type')
                ->index()
                ->constrained('type_equipment', 'id_type')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('inv_number', 20)->unique();
            $table->string('serial_number', 45)->nullable()->unique();
            $table->date('assignment_date')->nullable();

            $table->foreignId('id_employee')
                ->nullable()
                ->index()
                ->constrained('employee', 'id_employee')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
