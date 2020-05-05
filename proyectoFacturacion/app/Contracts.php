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
    ];

  public function client(){
    return $this->hasOne('App\Client', 'idClient', 'id');
  }

  public function contractConditions(){
    return $this->hasMany('App\ContractConditions', 'id', 'idClient');
  }
}