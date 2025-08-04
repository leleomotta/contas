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

    public function icone()
    {
        // Define o relacionamento com a tabela 'icone'
        return $this->belongsTo(Icone::class, 'ID_Icone', 'ID_Icone');
    }

    public function showAll(){
            $filtros = DB::table('categoria')
                ->select('*' )
                ->orderBy('Nome','ASC')
            ->get();

            return $filtros;
    }

    public function show(string $tipoCategoria){
        //aqui pegamos todos os pais
        $despesasPai = Categoria::where('Tipo', $tipoCategoria)
            ->whereNull('ID_Categoria_Pai')
            ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
            ->orderBy('Nome', 'ASC')
            ->get();

        $despesas = collect(); // mais limpo que usar um where fake

        //percorre, adciona o atual, depois os possíveis filhos e assim vai
        foreach ($despesasPai as $desp) {
            $despesas->push($desp);

            $filhos = Categoria::where('ID_Categoria_Pai', $desp->ID_Categoria)
                ->leftJoin('icone', 'icone.ID_Icone', '=', 'categoria.ID_Icone')
                ->orderBy('Nome', 'ASC')
                ->get();

            foreach ($filhos as $filho) {
                $filho->Nome = $desp->Nome . ' -> ' . $filho->Nome;
                $despesas->push($filho);
            }
        }

        return  $despesas;
    }
}
