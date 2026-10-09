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
            $table->date('data_service');
            $table->string('description', 255)->nullable();
            $table->integer('id_equipment');

            $table->foreign('id_equipment')
                ->references('id_equipment')
                ->on('equipment')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service');
    }
};