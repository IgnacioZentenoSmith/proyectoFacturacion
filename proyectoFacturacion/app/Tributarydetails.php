<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tributarydetails extends Model
{
    protected $table = 'tributarydetails';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idTributarydocument',
        'idClient',
        'idPaymentUnit',
        'idModule',
        'tributarydetails_paymentUnitQuantity',
        'tributarydetails_paymentPercentage',
        'tributarydetails_paymentValue',
        'tributarydetails_discount',
        'tributarydetails_paymentTotalValue',
    ];
}
