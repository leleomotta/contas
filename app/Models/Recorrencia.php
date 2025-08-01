<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recorrencia extends Model
{
    use HasFactory;

    protected $table = 'recorrencia';

    protected $primaryKey = 'ID_Recorrencia';

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'ID_Categoria', 'ID_Categoria');
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class, 'ID_Conta', 'ID_Conta');
    }

    public function cartao()
    {
        return $this->belongsTo(Cartao::class, 'ID_Cartao', 'ID_Cartao');
    }


}

