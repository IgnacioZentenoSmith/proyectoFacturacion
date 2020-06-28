<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
	protected $primaryKey = 'id';

    protected $fillable = [
        'clientRazonSocial',
        'clientRUT',
        'clientParentId',
        'clientContactEmail',
        'clientPhone',
        'clientDirection',
        'clientBusinessActivity',
        'idUser',
        'clientTipoEmpresa'
    ];
    public function contractConditions(){
        return $this->hasMany('App\ContractConditions', 'id', 'idClient');
      }
}
