<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractDistribution extends Model
{
    protected $table = 'contract_distribution';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idClient',
        'idContract',
        'contractDistribution_type',
        'contractDistribution_percentage',
    ];

  public function client(){
    return $this->hasOne('App\Client', 'idClient', 'id');
  }
  public function contract(){
    return $this->hasOne('App\Contracts', 'idContract', 'id');
  }
}
