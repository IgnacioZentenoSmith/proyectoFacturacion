<?php

namespace App\Http\Controllers;


use App\Client;
use App\Contracts;
use App\Modules;
use Auth;
use App\Permission;
use Illuminate\Support\Arr;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class ApiclientsController extends Controller
{
    public function getAllClients(Request $request) {
        $api_key = $request->header('api-key');
        if(!$request->headers->has('api-key') || $api_key == getenv('API_KEY')) {
            return response()->json([
                "error" => true,
                "message" => "Falta token de autorizacion"
            ]);
        }

        $holdings = Client::whereNull('clientParentId')->get();
        foreach ($holdings as $holding) {
            $razonesSociales = Client::where('clientParentId', $holding->id)->get();
            $holding = Arr::add($holding, 'razones sociales', $razonesSociales);
        }

        return response($holdings, 200);
    }

    public function getHoldings(Request $request, $idHolding = 'default') {
        $api_key = $request->header('api-key');
        if(!$request->headers->has('api-key') || $api_key == getenv('API_KEY')) {
            return response()->json([
                "error" => true,
                "message" => "Falta token de autorizacion"
            ]);
        }
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

    public function getRazonesSociales(Request $request, $idHolding) {
        $api_key = $request->header('api-key');
        if(!$request->headers->has('api-key') || $api_key == getenv('API_KEY')) {
            return response()->json([
                "error" => true,
                "message" => "Falta token de autorizacion"
            ]);
        }
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

    public function fepena2() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        $razonesSociales = Client::whereNotNull('clientParentId')
        ->select('clients.clientParentId as id_holding', 'clients.id as id_razon_social',
        'clients.clientRUT as rut_razon_social', 'clients.clientRazonSocial as nombre_razon_social')
        ->get();
        /*
        ->join('contracts', 'contracts.idClient', '=', 'clients.clientParentId')
        ->join('modules', 'contracts.idModule', '=', 'modules.id')
        'contracts.contractsNumero as n_contrato', 'modules.moduleName as modulo_base_contrato'
        */

        foreach ($razonesSociales as $razonSocial) {
            $holding = Client::where('id', $razonSocial->id_holding)->first();
            $razonSocial = Arr::add($razonSocial, 'nombre_holding', $holding->clientRazonSocial);

            $contract = Contracts::where('idClient', $razonSocial->id_holding)->first();
            if ($contract != null) {
                $razonSocial = Arr::add($razonSocial, 'n_contrato', $contract->contractsNumero);
                $module = Modules::where('id', $contract->idModule)->first();
                $razonSocial = Arr::add($razonSocial, 'modulo_base_contrato', $module->moduleName);
            }
            else {
                $razonSocial = Arr::add($razonSocial, 'n_contrato', '');
                $razonSocial = Arr::add($razonSocial, 'modulo_base_contrato', '');
            }
        }

        return view('home.fpena2', compact('razonesSociales', 'authPermisos'));
    }

}
