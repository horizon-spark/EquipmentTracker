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

            $table->integer('id_type');
            $table->string('inv_number', 20);
            $table->date('assignment_date')->nullable();
            $table->integer('id_employee')->nullable();

            $table->foreign('id_type')
                ->references('id_type')
                ->on('type_equipment')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('id_employee')
                ->references('id_employee')
                ->on('employee')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};