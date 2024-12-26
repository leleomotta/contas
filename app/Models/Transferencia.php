<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'transferencia';

    protected $primaryKey = 'ID_Transferencia';

    public function showAll(){

        $transferencias = Transferencia::where(function ($query) {
            $query->select('*');
            $query->orderBy('Data','DESC');
        })->get();
        return $transferencias;
    }
}
