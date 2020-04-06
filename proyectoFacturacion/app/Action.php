<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';
	protected $primaryKey = 'idActions';

    protected $fillable = [
		'actionName',
    ];
    


	public function permissions()
    {
        return $this->hasMany('App\Permission', 'idActions', 'idActions');
    }
}
