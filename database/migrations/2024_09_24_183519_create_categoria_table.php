<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->integer('ID_Categoria', true);
            $table->string('Nome')->nullable();
            $table->string('Cor')->nullable();
            $table->integer('fk_Categoria_ID_Categoria')->nullable()->index('fk_categoria_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
