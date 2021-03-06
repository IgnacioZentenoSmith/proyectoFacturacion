<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotifications;

use App\Permission;
use App\Client;
use App\ContractConditions;
use App\Contracts;
use App\Modules;
use App\PaymentUnits;
use App\User;
use App\Quantities;
use App\ContractDistribution;
use App\Binnacle;
use Auth;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class ContractdistributionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function distributionsIndex($idContrato) {
        $authPermisos = $this->getPermisos();
        $contract = Contracts::find($idContrato);
        //Sacar todas las razones sociales del holding
        $razonesSociales = Client::where('clientParentId', $contract->idClient)->get();
        //Ver si existen, si existen, asignar el mismo tipo de distribucion
        $contractDistributions = ContractDistribution::where('idContract', $idContrato)->get();
        if ($contractDistributions->count() > 0) {
            $type = $contractDistributions[0]->contractDistribution_type;
        } else {
            $type = 'No asignado';
        }
        //Si tiene razones sociales
        if ($razonesSociales->count() > 0) {
            //Por cada razon social, crear elemento si no existe
            foreach ($razonesSociales as $razonSocial) {
                //Unicos -> id cliente y id contrato, check si NO existe
                if (ContractDistribution::where('idClient', $razonSocial->id)->where('idContract', $idContrato)->count() == 0) {
                    $newContractDistribution = new ContractDistribution([
                        'idClient' => $razonSocial->id,
                        'idContract' => $idContrato,
                        'contractDistribution_type' => $type,
                        'contractDistribution_percentage' => 0,
                        'contractDistribution_discount' => 0,
                    ]);
                    $newContractDistribution->save();
                    app('App\Http\Controllers\BinnacleController')->reportBinnacle('CREATE', $newContractDistribution->getTable(), $newContractDistribution->id, null, $newContractDistribution);
                }
            }
            //Ya se han creado todas las distribuciones si es que no existen
            //Ahora, sacar todas las distribuciones de este contrato
            $contractDistributions = ContractDistribution::where('idContract', $idContrato)->get();
            //Agregarle los nombres de los clientes
            foreach ($contractDistributions as $contractDistribution) {
                //Asignar nombre del cliente
                $getClient = Client::where('id', $contractDistribution->idClient)->first();
                $contractDistribution = Arr::add($contractDistribution, 'contractDistribution_clientName', $getClient->clientRazonSocial);
            }
            return view('contracts.distributions', compact('authPermisos', 'contract', 'contractDistributions'));
        }
        //Si NO tiene razones sociales -> error y devolver a los contratos
        else {
            return redirect()->action('ContractsController@index')->with('error', 'El holding debe tener al menos 1 razón social para implementar sus distribuciones.');
        }
    }

    public function distributionsUpdate(Request $request, $idContrato) {
        $contract = Contracts::find($idContrato);
        $largoTabla = $request->distributionsTableLength;
        $request->validate([
            'contractDistribution_id' => 'required|array|min:' . $largoTabla,
            'contractDistribution_id.*' => 'required|numeric|min:0',
            'contractDistribution_type'=> 'required|array|min:' . $largoTabla,
            'contractDistribution_type.*'=> 'required|string|max:50',
            'contractDistribution_percentage'=> 'required|array|min:' . $largoTabla,
            'contractDistribution_percentage.*'=> 'required|numeric|between:0,100',
            'contractDistribution_discount'=> 'required|array|min:' . $largoTabla,
            'contractDistribution_discount.*'=> 'required|numeric|between:0,100',
            'distributionsType' => 'required|string|max:50',
        ]);
        //Validaciones extra
        if ($request->distributionsType == 'No asignado') {
            return redirect()->action('ContractdistributionController@distributionsIndex', ['idContrato' => $idContrato])->with('error', 'Se debe seleccionar un tipo de distribución.');
        } else if ($request->distributionsType == 'Porcentaje') {
            $request->validate([
                'contractDistribution_totalPercentage' => 'required|numeric|between:100,100',
            ]);
        }

        for ($i = 0; $i < $largoTabla; $i++ ) {
            $distributions = ContractDistribution::find($request->contractDistribution_id[$i]);


            $distributions->contractDistribution_type = $request->contractDistribution_type[$i];
            $distributions->contractDistribution_percentage = $request->contractDistribution_percentage[$i];
            $distributions->contractDistribution_discount = $request->contractDistribution_discount[$i];
            //Guardar si hay un cambio
            if ($distributions->isDirty()) {
                $postDistributions = ContractDistribution::find($request->contractDistribution_id[$i]);
                app('App\Http\Controllers\BinnacleController')->reportBinnacle('UPDATE', $distributions->getTable(), $distributions->id, $distributions, $postDistributions);
                $distributions->save();
            }
        }
        SendNotifications::dispatch('Contratos, ' . $contract->contractsNombre, 'Actualización de distribución de cobro')->onQueue('emails');
        return redirect()->action('ContractdistributionController@distributionsIndex', ['idContrato' => $idContrato])->with('success', 'Distribución de cobros guardados exitosamente.');

    }

    public function getPermisos() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return $authPermisos;
    }
}
