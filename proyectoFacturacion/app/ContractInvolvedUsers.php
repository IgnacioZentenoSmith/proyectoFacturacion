<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractInvolvedUsers extends Model
{
    protected $table = 'contract_involved_users';
    protected $primaryKey = 'id';

  protected $fillable = [
		'idUser',
    'idContract',
    'involvedUser_role',
  ];

  public function user(){
    return $this->hasOne('App\User', 'idUser', 'id');
  }
  public function contracts(){
    return $this->hasOne('App\Contracts', 'idContract', 'id');
	}
}
