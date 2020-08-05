<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Binnacle extends Model
{
    protected $table = 'binnacle';
	protected $primaryKey = 'id';

    protected $fillable = [
        'idUser',
        'binnacle_action',
        'binnacle_tableName',
        'binnacle_tableId',
        'binnacle_tablePreValues',
        'binnacle_tablePostValues',
    ];
}
