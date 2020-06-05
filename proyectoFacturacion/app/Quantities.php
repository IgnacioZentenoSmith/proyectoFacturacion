<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quantities extends Model
{
    protected $table = 'quantities';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idContractCondition',
        'quantitiesCantidad',
        'quantitiesPeriodo',
        'quantitiesMonto'
    ];

    public function contractConditions(){
        return $this->hasOne('App\ContractConditions', 'idContractCondition', 'id');
      }
}
