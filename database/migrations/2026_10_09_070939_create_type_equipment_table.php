<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_equipment', function (Blueprint $table) {
            $table->increments('id_type');
            $table->string('name_type', 45);
            $table->string('manufacturer', 45)->nullable();
            $table->string('model', 45)->nullable();
            $table->string('serial_number', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_equipment');
    }
};