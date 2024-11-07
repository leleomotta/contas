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
        Schema::table('despesa', function (Blueprint $table) {
            $table->foreign(['fk_Conta_ID_Conta'], 'FK_Despesa_2')->references(['ID_Conta'])->on('conta')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['fk_Cartao_ID_Cartao'], 'FK_Despesa_3')->references(['ID_Cartao'])->on('cartao')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['fk_Categoria_ID_Categoria'], 'FK_Despesa_4')->references(['ID_Categoria'])->on('categoria')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('despesa', function (Blueprint $table) {
            $table->dropForeign('FK_Despesa_2');
            $table->dropForeign('FK_Despesa_3');
            $table->dropForeign('FK_Despesa_4');
        });
    }
};
