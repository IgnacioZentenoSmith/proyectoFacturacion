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

    public function getHoldings($idHolding = 'default') {
        //Defecto -> entregar todos los holdings
        if ($idHolding = 'default') {
            $holdings = Client::whereNull('clientParentId')->get();
            return response($holdings, 200);
        //Parametro -> entregar el holding
        } else {
            $holdings = Client::find($idHolding);
            return response($holdings, 200);
        }
    }

    public function getRazonesSociales($idHolding) {
        //Entrega todas las razones sociales del holding
        $razonesSociales = Client::where('clientParentId', $idHolding)->get();
        return response($razonesSociales, 200);
    }
}
