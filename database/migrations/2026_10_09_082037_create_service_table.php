<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service', function (Blueprint $table) {
            $table->bigIncrements('id_service');
            $table->date('service_date');
            $table->string('description', 255)->nullable();

            $table->foreignId('id_equipment')
                ->index()
                ->constrained('equipment', 'id_equipment')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};
