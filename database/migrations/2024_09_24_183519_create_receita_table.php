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
        Schema::create('receita', function (Blueprint $table) {
            $table->integer('ID_Receita')->primary();
            $table->decimal('Valor', 15)->nullable();
            $table->date('Data')->nullable();
            $table->integer('fk_Conta_ID_Conta')->nullable()->index('fk_receita_2');
            $table->integer('fk_Categoria_ID_Categoria')->nullable()->index('fk_receita_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receita');
    }
};
