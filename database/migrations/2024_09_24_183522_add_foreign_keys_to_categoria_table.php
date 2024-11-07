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
        Schema::table('categoria', function (Blueprint $table) {
            $table->foreign(['fk_Categoria_ID_Categoria'], 'FK_Categoria_2')->references(['ID_Categoria'])->on('categoria')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria', function (Blueprint $table) {
            $table->dropForeign('FK_Categoria_2');
        });
    }
};
