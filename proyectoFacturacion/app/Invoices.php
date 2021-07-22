<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = 'invoices';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idTributaryDocument',
        'idClient',
        'idModule',
        'idPaymentUnit',
        'idContractPaymentDetails',

        'invoices_monto',
        'invoices_porcentaje',
        'invoices_descuento',
        'invoices_neto',
        'invoices_total',
        'invoices_grupo',

        'invoices_numeroOC',
        'invoices_fechaOC',
        'invoices_vigenciaOC',

        'invoices_numeroHES',
        'invoices_fechaHES',
        'invoices_vigenciaHES',

        'invoices_numfact',
    ];

}
