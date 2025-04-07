<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icone extends Model
{
    use HasFactory;

    protected $table = 'icone';

    protected $primaryKey = 'ID_Icone';

    public function showAll()
    {
        $icones = Icone::orderBy('Descricao', 'ASC')->get(); // ou ->get() para resultados reais
        return $icones;
    }
}

