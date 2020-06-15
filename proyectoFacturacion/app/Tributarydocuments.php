<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tributarydocuments extends Model
{
    protected $table = 'tributarydocuments';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idClient',
        'idContract',
        'tributarydocuments_period',
        'tributarydocuments_documentType',
        'tributarydocuments_totalAmount',
    ];

    public function client(){
        return $this->hasOne('App\Client', 'idClient', 'id');
      }
    public function contract(){
        return $this->hasOne('App\Contracts', 'idContract', 'id');
      }
}
