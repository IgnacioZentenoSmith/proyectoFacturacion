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
        'contractsNumeroCliente',
        'contractsFecha',
        'contractsEstado',
        'contractsMoneda',
        'contractsRecepcionMunicipal',
        'idModule',
        'contractsManualContract',
    ];

  public function client(){
    return $this->hasOne('App\Client', 'idClient', 'id');
  }
}
