<?php

namespace App\Http\Controllers;


use App\Client;
use Auth;
use App\Permission;
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

    public function fepena() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        $razonesSociales = Client::whereNotNull('clientParentId')
        ->select('clients.clientParentId as id_holding', 'clients.id as id_razon_social', 'clients.clientRUT as rut_razon_social', 'clients.clientRazonSocial as nombre_razon_social')
        ->get();

        foreach ($razonesSociales as $razonSocial) {
            $holding = Client::where('id', $razonSocial->id_holding)->first();
            $razonSocial = Arr::add($razonSocial, 'nombre_holding', $holding->clientRazonSocial);
        }

        return view('home.fpena', compact('razonesSociales', 'authPermisos'));
    }
}
//id_holding, nombre_holding, id_razon_social, rut_razon_social, nombre_razon_social
