<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idClient',
        'contractsNombre',
        'contractsNumero',
        'contractsFecha',
        'contractsEstado',
        'contractsMoneda',
        'idModule',
    ];

  public function client(){
    return $this->hasOne('App\Client', 'idClient', 'id');
  }
}