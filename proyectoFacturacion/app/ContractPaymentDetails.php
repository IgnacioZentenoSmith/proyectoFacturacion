<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractPaymentDetails extends Model
{
    protected $table = 'contract_payment_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idPaymentUnit',
        'idClient',
        'idContract',
        'contractPaymentDetails_period',
        'ccontractPaymentDetails_quantity',
        'contractPaymentDetails_description',
        'contractPaymentDetails_recepcionMunicipal',
        'contractPaymentDetails_units',
        'contractPaymentDetails_glosaProyecto',
    ];

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
