<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentUnits extends Model
{
    protected $table = 'payment_units';
	protected $primaryKey = 'id';

    protected $fillable = [
        'payment_units',
    ];

    public function contractConditions(){
        return $this->hasMany('App\ContractConditions', 'id', 'idPaymentUnit');
      }
}
