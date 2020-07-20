<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tributarydocuments extends Model
{
    protected $table = 'tributarydetails';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idTributarydocument',
        'idClient',
        'idPaymentUnit',
        'tributarydetails_paymentUnitQuantity',
        'tributarydetails_paymentPercentage',
        'tributarydetails_paymentValue,'
    ];

    public function contract(){
        return $this->hasOne('App\Contracts', 'idContract', 'id');
      }
}
