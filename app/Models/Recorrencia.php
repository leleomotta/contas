<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recorrencia extends Model
{
    use HasFactory;

    protected $table = 'recorrencia';

    protected $primaryKey = 'ID_Recorrencia';

}

