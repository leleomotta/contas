<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'transferencia';

    protected $primaryKey = 'ID_Transferencia';

    /**
     * Define o relacionamento com a conta de origem.
     * Uma transferência pertence a uma conta de origem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contaOrigem()
    {
        return $this->belongsTo(Conta::class, 'ID_Conta_Origem', 'ID_Conta');
    }

    /**
     * Define o relacionamento com a conta de destino.
     * Uma transferência pertence a uma conta de destino.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contaDestino()
    {
        return $this->belongsTo(Conta::class, 'ID_Conta_Destino', 'ID_Conta');
    }

    /**
     * Exibe a listagem completa de transferências.
     *
     * @return \Illuminate\Support\Collection
     */
    public function showAll()
    {
        return $this->with(['contaOrigem', 'contaDestino'])->orderBy('Data', 'DESC')->get();
    }

    /**
     * Exibe as contas de origem únicas das transferências.
     *
     * @return \Illuminate\Support\Collection
     */
    public function showContaOrigem()
    {
        return $this->with('contaOrigem')
            ->distinct('ID_Conta_Origem')
            ->orderBy('ID_Conta_Origem', 'ASC')
            ->get()
            ->pluck('contaOrigem')
            ->unique('ID_Conta');
    }

    /**
     * Exibe as contas de destino únicas das transferências.
     *
     * @return \Illuminate\Support\Collection
     */
    public function showContaDestino()
    {
        return $this->with('contaDestino')
            ->distinct('ID_Conta_Destino')
            ->orderBy('ID_Conta_Destino', 'ASC')
            ->get()
            ->pluck('contaDestino')
            ->unique('ID_Conta');
    }

    public function tranferenciasEntrada($start_date, $end_date, $contaDestino){
        $tranferenciasEntrada = DB::table('transferencia')
            ->select('transferencia.ID_Transferencia', 'transferencia.Data',
                'transferencia.Valor', 'transferencia.Observacao', 'conta.Nome', 'conta.Banco')
            ->join('conta', 'transferencia.ID_Conta_Origem', '=', 'conta.ID_Conta');
        if (! is_null($start_date) ) {
            $tranferenciasEntrada->where('Data', '>=', $start_date);
        }
        if (!is_null($end_date)) {
            $tranferenciasEntrada->where('Data', '<=', $end_date);
        }
        if (!is_null($contaDestino)) {
            $tranferenciasEntrada->where('transferencia.ID_Conta_Destino', '=', $contaDestino);
        }
        $tranferenciasEntrada->orderBy('Data', 'DESC');
        return $tranferenciasEntrada->get();
    }

    public function tranferenciasSaida($start_date, $end_date, $contaOrigem){
        $tranferenciasSaida = DB::table('transferencia')
            ->select('transferencia.ID_Transferencia', 'transferencia.Data',
                'transferencia.Valor', 'transferencia.Observacao', 'conta.Nome', 'conta.Banco')
            ->join('conta', 'transferencia.ID_Conta_Destino', '=', 'conta.ID_Conta');
        if (! is_null($start_date) ) {
            $tranferenciasSaida->where('Data', '>=', $start_date);
        }
        if (!is_null($end_date)) {
            $tranferenciasSaida->where('Data', '<=', $end_date);
        }
        if (!is_null($contaOrigem)) {
            $tranferenciasSaida->where('transferencia.ID_Conta_Origem', '=', $contaOrigem);
        }
        $tranferenciasSaida->orderBy('Data', 'DESC');
        return $tranferenciasSaida->get();
    }
}
