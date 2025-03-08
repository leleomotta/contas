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
        $icones = Icone::where(function ($query) {
            $query->select('*');
            //$query->where('Arquivada',0);
            //$query->orderBy('Nome','ASC');
        })->get();
        return $icones;
    }
}

