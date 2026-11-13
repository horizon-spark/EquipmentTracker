<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_supplies', function (Blueprint $table) {
            $table->foreignId('id_service')
                ->constrained('service', 'id_service')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('id_supply')
                ->constrained('supplies', 'id_supply')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->integer('quantity_used')->default(1);

            $table->primary(['id_service', 'id_supply']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_supplies');
    }
};
