<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Client;
use App\ContractConditions;
use App\ContractInvolvedUsers;
use App\Contracts;
use App\Modules;
use App\PaymentUnits;
use App\User;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContractsController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$collection->put('price', 100);
        $authPermisos = $this->getPermisos();
        $contracts = Contracts::all();
        
        foreach ($contracts as $contract) {
            //Saca el nombre del ejecutivo
            $idEjecutivo = ContractInvolvedUsers::where('idContract', $contract['id'])->where('involvedUser_role', 'Ejecutivo')->first();
            $userEjecutivo = User::where('id', $idEjecutivo->idUser)->first();

            //Saca el nombre del cliente
            $client = Client::where('id', $contract['idClient'])->first();

            //Agregar a la coleccion si es que existen
            $contract = Arr::add($contract, 'contract_clientName', $client->clientName);
            $contract = Arr::add($contract, 'contract_ejecutivoName', $userEjecutivo->name);
        }
        
        //return $contracts;
        return view('contracts.index', compact('authPermisos', 'contracts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authPermisos = $this->getPermisos();
        //Saca solo los padres
        $clients = Client::whereNull('clientParentId')->get();
        $users = User::all();
        return view('contracts.create', compact('authPermisos', 'clients', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'idClient'=> 'required|numeric',
            'contractsNombre'=> 'required|string|max:100',
            'contractsNumero'=> 'required|string|max:100|unique:contracts,contractsNumero',
            'contractsFecha'=> 'required|date',
            'idEjecutivo'=> 'required|numeric|min:1',
        ]);

        //Saca id del ejecutivo
        $idEjecutivo = $request->idEjecutivo;
        //Saca numero del contrato
        $numeroContrato = $request->contractsNumero;

        $newContract = new Contracts([
            'idClient' => $request->idClient,
            'contractsNombre' => $request->contractsNombre,
            'contractsNumero' => $numeroContrato,
            'contractsFecha' => $request->contractsFecha,
            'contractsEstado' => false
        ]);
        //Guarda datos
        $newContract->save();
        //Saca ese contrato
        $contract = Contracts::where('contractsNumero', $numeroContrato)->first();
        //Guardar usuario y contrato en tabla asociativa
        //involvedUser_role Rol del usuario dentro del contrato - Creador -> crea el contrato, Ejecutivo -> Encargado del contrato
        $newContractInvolvedUsers_Ejecutivo = new ContractInvolvedUsers([
            'idUser' => $idEjecutivo,
            'idContract' => $contract->id,
            'involvedUser_role' => 'Ejecutivo'
        ]);
        $newContractInvolvedUsers_Ejecutivo->save();
        //Saca usuario actual
        $idAuthUser = Auth::user()->id;
        $newContractInvolvedUsers_Creador = new ContractInvolvedUsers([
            'idUser' => $idAuthUser,
            'idContract' => $contract->id,
            'involvedUser_role' => 'Creador'
        ]);
        $newContractInvolvedUsers_Creador->save();
        
        
        return redirect('contracts')->with('success', 'Contrato agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $authPermisos = $this->getPermisos();
        $contract = Contracts::where('id', $id)->first();
        $ejecutivoActual = ContractInvolvedUsers::where('idContract', $id)->where('involvedUser_role', 'Ejecutivo')->first();
        $clients = Client::whereNull('clientParentId')->get();
        $users = User::all();
        return view('contracts.edit', compact('contract', 'clients', 'users', 'ejecutivoActual', 'authPermisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'idClient'=> 'required|numeric',
            'contractsNombre'=> 'required|string|max:100',
            'contractsNumero'=> 'required|string|max:100|unique:contracts,contractsNumero,' .$id ,
            'contractsFecha'=> 'required|date',
            'idEjecutivo'=> 'required|numeric|min:1',
        ]);

        $idEjecutivo = $request->idEjecutivo;

        $contract = Contracts::find($id);
        $contract->idClient = $request->idClient;
        $contract->contractsNombre = $request->contractsNombre;
        $contract->contractsNumero = $request->contractsNumero;
        $contract->contractsFecha = $request->contractsFecha;

        //Encuentra al ejecutivo de este contrato
        $involvedUser = ContractInvolvedUsers::where('idContract', $id)->where('idUser', $idEjecutivo)->where('involvedUser_role', 'Ejecutivo')->first();
        //Si hay un cambio en el contrato o se cambio al ejecutivo del contrato
        if ($contract->isDirty() || !$involvedUser) {
            if ($contract->isDirty()) {
                $contract->contractsEstado = false;
                $contract->save();
            }
            if (!$involvedUser) {
                //Saca al ejecutivo actual y eliminalo
                $currentEjecutivo = ContractInvolvedUsers::where('idContract', $id)->where('involvedUser_role', 'Ejecutivo')->first();
                if ($currentEjecutivo)
                    $currentEjecutivo->delete();
                //Agrega al ejecutivo nuevo
                $newContractInvolvedUsers_Ejecutivo = new ContractInvolvedUsers([
                    'idUser' => $idEjecutivo,
                    'idContract' => $id,
                    'involvedUser_role' => 'Ejecutivo'
                ]);
                $newContractInvolvedUsers_Ejecutivo->save();
            }
            return redirect('contracts')->with('success', 'Contrato editado exitosamente.');
        } else {
            return redirect('contracts');
        }
    }

    public function editContractStatus($id)
    {
        //Extra validacion
        $contract = Contracts::find($id);
        $contractConditions = ContractConditions::where('idContract', $contract->id)->get();
        if ($contractConditions->count() > 0) {
            $contract->contractsEstado = !$contract->contractsEstado;
            $contract->save();
            return redirect('contracts')->with('success', 'Status del contrato modificado correctamente.');
        } else {
            return redirect('contracts')->with('warning', 'Este contrato debe al menos tener 1 condicion contractual para estar activo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contract = Contracts::find($id);
        $contract->delete();
        return redirect('contracts')->with('success', 'Contrato eliminado exitosamente.');
    }

    //ID -> del contrato
    public function conditionsIndex($id) {
        $authPermisos = $this->getPermisos();
        $contract = Contracts::find($id);
        //Sacar las condiciones de este contrato en particular
        $contractConditions = ContractConditions::where('idContract', $contract->id)->get();
        //Traducir IDS a nombres en el indice
        foreach ($contractConditions as $contractCondition) {
            //Saca y agrega a la coleccion el nombre del modulo
            $getModule = Modules::where('id', $contractCondition['idModule'])->first();
            $contractCondition = Arr::add($contractCondition, 'contractCondition_moduleName', $getModule->moduleName);
            //Saca y agrega a la coleccion el nombre de la unidad de pago
            $getPaymentUnit = PaymentUnits::where('id', $contractCondition['idPaymentUnit'])->first();
            $contractCondition = Arr::add($contractCondition, 'contractCondition_paymentUnitName', $getPaymentUnit->payment_units);
            //Saca y agrega a la coleccion el nombre del cliente
            $getClient = Client::where('id', $contractCondition['idClient'])->first();
            $contractCondition = Arr::add($contractCondition, 'contractCondition_clientName', $getClient->clientName);
            //Saca y agrega a la coleccion el nombre del contrato
            $getContract = Contracts::where('id', $contractCondition['idContract'])->first();
            $contractCondition = Arr::add($contractCondition, 'contractCondition_contractName', $getContract->contractsNombre);
        }
        return view('contracts.conditions', compact('authPermisos', 'contract', 'contractConditions'));
    }
    public function conditionsCreate($id) {
        $authPermisos = $this->getPermisos();
        $contract = Contracts::find($id);
        //Saca hijos y padres
        $clients = Client::where('clientParentId', $contract->idClient)->orWhere('id', $contract->idClient)->get();
        $modules = Modules::all();
        $paymentUnits = PaymentUnits::all();
        return view('contracts.conditionsCreate', compact('authPermisos', 'contract', 'clients', 'modules', 'paymentUnits'));
    }
    //RECIBE ID DE CONTRATO
    public function conditionsStore(Request $request, $id) {
        $request->validate([
            'idModule'=> 'required|numeric',
            'idPaymentUnit'=> 'required|numeric',
            'idClient'=> 'required|numeric',
            'contractsConditions_Moneda'=> 'required|string|max:100',
            'contractsConditions_Precio'=> 'required|numeric|min:1',
            'contractsConditions_Modalidad'=> 'required|string|max:100',
            'contractsConditions_Cantidad'=> 'required|numeric|min:1',
        ]);

        $newContractConditions = new ContractConditions([
            'idModule' => $request->idModule,
            'idPaymentUnit' => $request->idPaymentUnit,
            'idClient' => $request->idClient,
            'idContract' => $id,
            'contractsConditions_Moneda' => $request->contractsConditions_Moneda,
            'contractsConditions_Precio' => $request->contractsConditions_Precio,
            'contractsConditions_Modalidad' => $request->contractsConditions_Modalidad,
            'contractsConditions_Cantidad' => $request->contractsConditions_Cantidad,
        ]);
        //Guarda datos
        $newContractConditions->save();
        return redirect()->action('ContractsController@conditionsIndex', ['id' => $id])->with('success', 'Condicion contractual agregada exitosamente.');
    }
    //RECIBE ID DE CONDICION CONTRACTUAL
    public function conditionsEdit($id) {
        $authPermisos = $this->getPermisos();
        $contractConditions = ContractConditions::find($id);
        $contractId = $contractConditions->idContract;
        //Saca hijos y padres
        $clients = Client::where('clientParentId', $contractId)->orWhere('id', $contractId)->get();
        $modules = Modules::all();
        $paymentUnits = PaymentUnits::all();
        return view('contracts.conditionsEdit', compact('authPermisos', 'contractConditions', 'clients', 'modules', 'paymentUnits'));
    }
    //RECIBE ID DE CONDICION CONTRACTUAL
    public function conditionsUpdate(Request $request, $id) {

        $request->validate([
            'idModule'=> 'required|numeric',
            'idPaymentUnit'=> 'required|numeric',
            'idClient'=> 'required|numeric',
            'contractsConditions_Moneda'=> 'required|string|max:100',
            'contractsConditions_Precio'=> 'required|numeric|min:1',
            'contractsConditions_Modalidad'=> 'required|string|max:100',
            'contractsConditions_Cantidad'=> 'required|numeric|min:1',
        ]);

        $contractConditions = ContractConditions::find($id);
        $contractConditions->idModule = $request->idModule;
        $contractConditions->idPaymentUnit = $request->idPaymentUnit;
        $contractConditions->idClient = $request->idClient;
        //ID del contrato es el mismo
        $contractConditions->contractsConditions_Moneda = $request->contractsConditions_Moneda;
        $contractConditions->contractsConditions_Precio = $request->contractsConditions_Precio;
        $contractConditions->contractsConditions_Modalidad = $request->contractsConditions_Modalidad;
        $contractConditions->contractsConditions_Cantidad = $request->contractsConditions_Cantidad;
        
        //Saca el id para hacer el redirect
        $contractId = $contractConditions->idContract;

        if ($contractConditions->isDirty()) {
             $contractConditions->save();
             return redirect()->action('ContractsController@conditionsIndex', ['id' => $contractId])->with('success', 'Condicion contractual editada exitosamente.');
        } else {
            return redirect()->action('ContractsController@conditionsIndex', ['id' => $contractId]);
        }
    }
    //RECIBE ID DE CONDICION CONTRACTUAL
    public function conditionsDestroy($id) {
        $contractCondition = ContractConditions::find($id);
        $contractId = $contractCondition->idContract;
        $contractCondition->delete();
        return redirect()->action('ContractsController@conditionsIndex', ['id' => $contractId])->with('success', 'Condicion contractual eliminada exitosamente.');
    }

    public function getPermisos() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return $authPermisos;
    }
}
