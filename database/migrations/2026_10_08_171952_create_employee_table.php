<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->bigIncrements('id_employee'); 
            $table->string('full_name', 45);

            $table->integer('id_location');

            $table->foreign('id_location')
                ->references('id_location')
                ->on('location')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};