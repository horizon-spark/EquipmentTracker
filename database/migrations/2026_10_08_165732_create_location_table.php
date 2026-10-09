<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location', function (Blueprint $table) {
            $table->bigIncrements('id_location');
            $table->string('city', 255);
            $table->string('office', 255);
            $table->string('address', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location');
    }
};