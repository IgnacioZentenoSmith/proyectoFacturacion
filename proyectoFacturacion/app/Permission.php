<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $table = 'permissions';
	protected $primaryKey = 'id';

  protected $fillable = [
		'idActions',
		'idUser',
  ];

  public function user(){
    return $this->hasOne('App\User', 'idUser', 'id');
  }
  public function action(){
    return $this->hasOne('App\Action', 'idActions', 'id');
	}
}
