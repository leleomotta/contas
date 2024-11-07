<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Receita extends Model
{
    use HasFactory;

    protected $table = 'receita';

    protected $primaryKey = 'ID_Receita';

    public function conta()
    {
        return $this->hasOne(Conta::class, 'ID_Conta', 'ID_Conta');
    }

    public function showAll(){

        $receitas = DB::table('receita')
            //->leftJoin('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            //->join('materia', 'prova.idMateria', '=', 'materia.idMateria')
            //->select('prova.*', 'materia.Nome', DB::raw('count(prova_questao.idQuestao) as totQuestoes'))->groupBy('prova.idProva')

            //->toSql(); dd($receitas);
            ->paginate(99999);
        return $receitas;
    }

    public function show($filtro){
        $dt = Carbon::now();
        $dt->setDateFrom($filtro . '-15');
        $receitas = DB::table('receita')
            ->select('receita.*', 'categoria.Nome as NomeCategoria', 'conta.Banco' )
            ->join('conta', 'receita.ID_Conta', '=', 'conta.ID_Conta')
            ->join('categoria', 'receita.ID_Categoria', '=', 'categoria.ID_Categoria')
            ->whereBetween('Data',
                [
                    //Carbon::createFromDate(2024, 6, 01)->toDateString(),
                    //Carbon::createFromDate(2024, 6, 30)->toDateString()
                    Carbon::createFromDate($dt->firstOfMonth())->toDateString(),
                    Carbon::createFromDate($dt->lastOfMonth())->toDateString()
                ]
            )
            ->orderBy('Data','ASC')
            //->toSql(); dd($receitas);
            ->paginate(99999);
        return $receitas;
    }

}

