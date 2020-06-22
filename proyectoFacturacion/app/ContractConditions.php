<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractConditions extends Model
{
    protected $table = 'contract_conditions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idModule',
        'idPaymentUnit',
        'idClient',
        'idContract',
        'contractsConditions_Precio',
        'contractsConditions_Modalidad',
        'contractsConditions_Cantidad',
        'contractsConditions_fechaInicio',
        'contractsConditions_fechaTermino',
    ];

  public function module(){
    return $this->hasOne('App\Modules', 'idModule', 'id');
  }
  public function paymentUnit(){
    return $this->hasOne('App\PaymentUnits', 'idPaymentUnit', 'id');
  }
  public function client(){
    return $this->hasOne('App\Client', 'idClient', 'id');
  }
  public function contract(){
    return $this->hasOne('App\Contracts', 'idContract', 'id');
  }
}