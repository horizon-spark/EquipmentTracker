<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_supplies', function (Blueprint $table) {
            $table->integer('id_service');
            $table->integer('id_supply');
            $table->integer('quantity_used')->default(1);

            $table->primary(['id_service', 'id_supply']);

            $table->foreign('id_service')
                ->references('id_service')
                ->on('service')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_supply')
                ->references('id_supply')
                ->on('supplies')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_supplies');
    }
};