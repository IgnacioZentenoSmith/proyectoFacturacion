<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    protected $table = 'modules';
	protected $primaryKey = 'id';

    protected $fillable = [
        'moduleName',
        'moduleParentId',
    ];

    public function contractConditions(){
        return $this->hasMany('App\ContractConditions', 'id', 'idModule');
      }
}
