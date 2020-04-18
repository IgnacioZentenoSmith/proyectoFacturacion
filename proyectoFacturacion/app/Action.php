<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';
	protected $primaryKey = 'id';

    protected $fillable = [
        'actionName',
        'actionParentId',
        'actionType',
    ];
    


	public function permissions()
    {
        return $this->hasMany('App\Permission', 'idActions', 'id');
    }
}
