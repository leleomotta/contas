<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categoria';

    protected $primaryKey = 'ID_Categoria';


    public function showAll(){
        $filtros = DB::table('categoria')
            ->select('*' )
            ->orderBy('Nome','ASC')
        ->get();
        //dd($filtros->toSql());
        return $filtros;
    }



}
