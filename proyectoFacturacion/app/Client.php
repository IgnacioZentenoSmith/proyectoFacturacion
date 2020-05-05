<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
	protected $primaryKey = 'id';

    protected $fillable = [
        'clientName',
        'clientRazonSocial',
        'clientRUT',
        'clientParentId',
    ];
    public function contractConditions(){
        return $this->hasMany('App\ContractConditions', 'id', 'idClient');
      }
}
