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

            return $filtros;
    }

    public function show(string $tipoCategoria){
        //aqui pegamos todos os pais
        $despesasPai = Categoria::where(function ($query) use ($tipoCategoria) {
            $query->select('*');
            $query->where('Tipo',$tipoCategoria);
            $query->WhereNull('ID_Categoria_Pai');
            //$query->orderBy('Nome','ASC');
        })->orderBy('Nome','ASC')
            //->toSql(); dd($despesasPai);
            ->get();


        //aqui apenas criamos o dataset vazio
        $despesas = Categoria::where(function ($query) {
            $query->select('*');
            $query->where('Tipo','X');
        })->get();


        //percorre, adciona o atual, depois os possíveis filhos e assim vai
        foreach($despesasPai as $desp) {
            $despesas = $despesas->add($desp);
            //procura filhos da despesa específica
            $X = Categoria::where(function ($query) use ($desp) {
                $query->select('*');
                $query->where('ID_Categoria_Pai',$desp->ID_Categoria);
            })->orderBy('Nome','ASC')
                //->toSql(); dd($X);
                ->get();

            //confere se tem os filhos e adciona na mão para poder colocar o ->
            if (!($X == NULL)){
                foreach($X as $Y) {
                    $Y['Nome'] = '-> '  . $Y['Nome'];
                    $despesas = $despesas->add($Y);
                }
            }


        }
        return  $despesas;
    }



}
