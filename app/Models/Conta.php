<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conta extends Model
{
    use HasFactory;

    protected $table = 'conta';

    protected $primaryKey = 'ID_Conta';

    public function showAll(){

        $contas = DB::table('conta')->paginate(99999);
            /*= DB::table('receita')
            //->leftJoin('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            //->join('materia', 'prova.idMateria', '=', 'materia.idMateria')
            //->select('prova.*', 'materia.Nome', DB::raw('count(prova_questao.idQuestao) as totQuestoes'))->groupBy('prova.idProva')

            //->toSql(); dd($receitas);
            ->paginate(99999);
            */
        return $contas;
    }
}
