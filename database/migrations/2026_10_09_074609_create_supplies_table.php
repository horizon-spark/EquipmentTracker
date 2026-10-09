<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->bigIncrements('id_supply');
            $table->string('name_supplies', 45);
            $table->integer('quantity')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};