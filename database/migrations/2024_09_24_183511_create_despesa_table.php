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
        Schema::create('despesa', function (Blueprint $table) {
            $table->integer('ID_Despesa')->primary();
            $table->decimal('Valor', 15)->nullable();
            $table->date('Data')->nullable();
            $table->integer('fk_Conta_ID_Conta')->nullable()->index('fk_despesa_2');
            $table->integer('fk_Cartao_ID_Cartao')->nullable()->index('fk_despesa_3');
            $table->integer('fk_Categoria_ID_Categoria')->nullable()->index('fk_despesa_4');
            ///asd
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despesa');
    }
};
