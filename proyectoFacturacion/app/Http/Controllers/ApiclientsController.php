<?php

namespace App\Http\Controllers;


use App\Client;
use Auth;
use Illuminate\Support\Arr;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class ApiclientsController extends Controller
{
    public function getAllClients() {
        $holdings = Client::whereNull('clientParentId')->get();
        foreach ($holdings as $holding) {
            $razonesSociales = Client::where('clientParentId', $holding->id)->get();
            $holding = Arr::add($holding, 'razones sociales', $razonesSociales);
        }

        return response($holdings, 200);
    }
}
