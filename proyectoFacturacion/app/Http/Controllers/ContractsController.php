<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Client;
use App\ContractConditions;
use App\Contracts;
use App\Modules;
use App\PaymentUnits;
use App\User;
use App\Quantities;
use Auth;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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
        //Periodo
        $periodo = Carbon::now()->format('Y-m');

        foreach ($contracts as $contract) {

            //Saca el nombre del cliente
            $client = Client::where('id', $contract['idClient'])->first();
            //Agregar a la coleccion
            $contract = Arr::add($contract, 'contract_clientName', $client->clientRazonSocial);
            //Saca el nombre del modulo
            $module = Modules::where('id', $contract['idModule'])->first();
            if ($module == null) {
                //Agregar a la coleccion
                $contract = Arr::add($contract, 'contract_moduleName', 'Aún no asignado');
            } else {
                //Agregar a la coleccion
                $contract = Arr::add($contract, 'contract_moduleName', $module->moduleName);
            }

            //Sacar ejecutivo y agregarlo a la coleccion
            if ($client->idUser != null) {
                $ejecutivo = User::find($client->idUser);
                $ejecutivoNombre = $ejecutivo->name;
            } else {
                $ejecutivoNombre = 'Aún no asignado';
            }
            $contract = Arr::add($contract, 'contract_clientEjecutivoName', $ejecutivoNombre);
        }

        //return $contracts;
        return view('contracts.index', compact('authPermisos', 'contracts', 'periodo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authPermisos = $this->getPermisos();
        //Saca clientes padre
        $clients = Client::whereNull('clientParentId')->get();
        $users = User::all();
        //Saca modulos padre
        $modules = Modules::whereNull('moduleParentId')->get();
        return view('contracts.create', compact('authPermisos', 'clients', 'users', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //VALIDAR DATOS
        $request->validate([
            'idClient'=> 'required|numeric',
            'idModule'=> 'required|numeric',
            'contractsNumero'=> 'required|string|max:100|unique:contracts,contractsNumero',
            'contractsMoneda' => 'required|string|max:100',
            'contractsFecha'=> 'required|date',
            'contractsRecepcionMunicipal' => 'required|boolean',
        ]);
        //VALIDAR CLIENTE Y MODULO --> COMBINACION UNICA
        $request->validate([
            'idClient'=> 'unique:contracts,idClient,NULL,id,idModule,' . $request->idModule,
            'idModule'=> 'unique:contracts,idModule,NULL,id,idClient,' . $request->idClient,
        ]);

        $holding = Client::find($request->idClient);
        $modulo = Modules::find($request->idModule);
        $nombre = $holding->clientRazonSocial . ' ' . $modulo->moduleName;


        $newContract = new Contracts([
            'idClient' => $request->idClient,
            'contractsNombre' => $nombre,
            'contractsNumero' => $request->contractsNumero,
            'contractsMoneda' => $request->contractsMoneda,
            'contractsFecha' => $request->contractsFecha,
            'contractsEstado' => false,
            'idModule' => $request->idModule,
            'contractsRecepcionMunicipal' => $request->contractsRecepcionMunicipal,
        ]);
        //Guarda datos
        $newContract->save();
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
        $clients = Client::whereNull('clientParentId')->get();
        $users = User::all();
        $modules = Modules::whereNull('moduleParentId')->get();
        return view('contracts.edit', compact('contract', 'clients', 'users', 'authPermisos', 'modules'));
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
        //VALIDAR DATOS MENOS ESTE ID
        $request->validate([
            'idClient'=> 'required|numeric',
            'idModule'=> 'required|numeric',
            'contractsNumero'=> 'required|string|max:100|unique:contracts,contractsNumero,' .$id ,
            'contractsMoneda' => 'required|string|max:100',
            'contractsFecha'=> 'required|date',
            'contractsRecepcionMunicipal' => 'required|boolean',
        ]);
        //VALIDAR CLIENTE Y MODULO --> COMBINACION UNICA MENOS ESTE ID
        $request->validate([
            'idClient'=> 'unique:contracts,idClient,' . $id . ',id,idModule,' . $request->idModule,
            'idModule'=> 'unique:contracts,idModule,' . $id . ',id,idClient,' . $request->idClient,
        ]);
        $modulo = Modules::find($request->idModule);

        $contract = Contracts::find($id);
        $contract->idClient = $request->idClient;
        $contract->contractsNumero = $request->contractsNumero;
        $contract->contractsMoneda = $request->contractsMoneda;
        $contract->contractsFecha = $request->contractsFecha;
        $contract->idModule = $request->idModule;
        $contract->contractsRecepcionMunicipal = $request->contractsRecepcionMunicipal;

        //Si hay un cambio en el contrato o se cambio al ejecutivo del contrato
        if ($contract->isDirty()) {
            $contract->contractsEstado = false;
            $holding = Client::find($contract->idClient);
            $modulo = Modules::find($contract->idModule);
            $nombre = $holding->clientRazonSocial . ' ' . $modulo->moduleName;
            $contract->contractsNombre = $nombre;
            $contract->save();
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
            $contractCondition = Arr::add($contractCondition, 'contractCondition_clientName', $getClient->clientRazonSocial);
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
        $modules = Modules::where('moduleParentId', $contract->idModule)->orWhere('id', $contract->idModule)->get();
        $paymentUnits = PaymentUnits::all();
        return view('contracts.conditionsCreate', compact('authPermisos', 'contract', 'clients', 'modules', 'paymentUnits'));
    }
    //RECIBE ID DE CONTRATO
    public function conditionsStore(Request $request, $id) {
        $request->validate([
            'idModule'=> 'required|numeric',
            'idPaymentUnit'=> 'required|numeric',
            'idClient'=> 'required|numeric',
            'contractsConditions_Precio'=> 'required|numeric|min:0',
            'contractsConditions_Modalidad'=> 'required|string|max:100',
            'contractsConditions_Cantidad'=> 'required|numeric|min:0',
            'contractsConditions_fechaInicio'=> 'required|date_format:Y-m-d',
            'contractsConditions_fechaTermino'=> 'nullable|date_format:Y-m-d',
        ]);
        //Crea nueva condicion contractual
        $newContractConditions = new ContractConditions([
            'idModule' => $request->idModule,
            'idPaymentUnit' => $request->idPaymentUnit,
            'idClient' => $request->idClient,
            'idContract' => $id,
            'contractsConditions_Precio' => $request->contractsConditions_Precio,
            'contractsConditions_Modalidad' => $request->contractsConditions_Modalidad,
            'contractsConditions_Cantidad' => $request->contractsConditions_Cantidad,
            'contractsConditions_fechaInicio' => $request->contractsConditions_fechaInicio,
            'contractsConditions_fechaTermino' => $request->contractsConditions_fechaTermino,
        ]);
        //Guarda datos
        $newContractConditions->save();
        return redirect()->action('ContractsController@conditionsIndex', ['id' => $id])->with('success', 'Condicion contractual agregada exitosamente.');
    }
    //RECIBE ID DE CONDICION CONTRACTUAL
    public function conditionsEdit($id) {
        $authPermisos = $this->getPermisos();
        $contractConditions = ContractConditions::find($id);
        //Saca ID del contrato
        $contract = Contracts::find($contractConditions->idContract);
        //Saca hijos y padres
        $clients = Client::where('clientParentId', $contract->idClient)->orWhere('id', $contract->idClient)->get();
        $modules = Modules::where('moduleParentId', $contract->idModule)->orWhere('id', $contract->idModule)->get();
        $paymentUnits = PaymentUnits::all();
        return view('contracts.conditionsEdit', compact('authPermisos', 'contractConditions', 'clients', 'modules', 'paymentUnits'));
    }
    //RECIBE ID DE CONDICION CONTRACTUAL
    public function conditionsUpdate(Request $request, $id) {

        $request->validate([
            'idModule'=> 'required|numeric',
            'idPaymentUnit'=> 'required|numeric',
            'idClient'=> 'required|numeric',
            'contractsConditions_Precio'=> 'required|numeric|min:0',
            'contractsConditions_Modalidad'=> 'required|string|max:100',
            'contractsConditions_Cantidad'=> 'required|numeric|min:0',
            'contractsConditions_fechaInicio'=> 'required|date_format:Y-m-d',
            'contractsConditions_fechaTermino'=> 'nullable|date_format:Y-m-d',
        ]);
        //Si la fecha de termino existe y la fecha de inicio es mayor a la fecha de termino
        if ($request->contractsConditions_fechaTermino != null && Carbon::createFromFormat('Y-m-d', $request->contractsConditions_fechaInicio) > Carbon::createFromFormat('Y-m-d', $request->contractsConditions_fechaTermino)) {
            return redirect()->action('ContractsController@conditionsEdit', ['id' => $id])->with('warning', 'La fecha de término debe ser mayor a la de inicio.');
        }
        //Si cambio la fecha de termino
        $contractConditionsFechaTermino = ContractConditions::find($id);
        $contractConditionsFechaTermino->contractsConditions_fechaTermino = $request->contractsConditions_fechaTermino;
        if ($contractConditionsFechaTermino->isDirty('contractsConditions_fechaTermino')) {
            $contractConditionsFechaTermino->save();
        }

        //Verificar los otros campos
        $contractConditions = ContractConditions::find($id);
        $contractConditions->idModule = $request->idModule;
        $contractConditions->idPaymentUnit = $request->idPaymentUnit;
        $contractConditions->idClient = $request->idClient;
        //ID del contrato es el mismo
        $contractConditions->contractsConditions_Precio = $request->contractsConditions_Precio;
        $contractConditions->contractsConditions_Modalidad = $request->contractsConditions_Modalidad;
        $contractConditions->contractsConditions_Cantidad = $request->contractsConditions_Cantidad;
        $contractConditions->contractsConditions_fechaInicio = $request->contractsConditions_fechaInicio;
        //Saca el id para hacer el redirect
        $contractId = $contractConditions->idContract;

        if ($contractConditions->isDirty()) {
            //Si ha habido algun cambio distinto de la fecha de termino, crear una nueva condicion contractual
            $newContractConditions = new ContractConditions([
                'idModule' => $request->idModule,
                'idPaymentUnit' => $request->idPaymentUnit,
                'idClient' => $request->idClient,
                'idContract' => $contractConditions->idContract,
                'contractsConditions_Precio' => $request->contractsConditions_Precio,
                'contractsConditions_Modalidad' => $request->contractsConditions_Modalidad,
                'contractsConditions_Cantidad' => $request->contractsConditions_Cantidad,
                'contractsConditions_fechaInicio' => $request->contractsConditions_fechaInicio,
                'contractsConditions_fechaTermino' => $request->contractsConditions_fechaTermino,
            ]);
             $newContractConditions->save();
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

    public function quantitiesIndex($idContrato, $periodo) {
        $authPermisos = $this->getPermisos();
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $contract = Contracts::find($idContrato);
        //Sacar las condiciones contractuales del contrato que sean Fijo o Variables
        /*
        Contrato con el ID id
        Modalidad de fijo y variable
        Fecha de inicio debe ser menor al periodo
        La fecha de termino debe ser mayor o igual al periodo, o bien, null
        */
        $contractConditions = ContractConditions::where('idContract', $idContrato)
        //Sacar modalidad fija y variable
        ->whereIn('contractsConditions_Modalidad', ['Fijo', 'Variable'])
        //Sacar fecha inicio menor o igual al periodo
        ->where('contractsConditions_fechaInicio', '<=', $periodo . '-25')
        //Sacar fecha termino mayor o igual al periodo o null
        ->where(function($query) use ($periodo) {
                $query->where('contractsConditions_fechaTermino', '>=', $periodo . '-25')
                      ->orWhere('contractsConditions_fechaTermino', null);
            })
        ->get();
        //Crear todas las cantidades relevantes (Fijo/Variable) de las condiciones contractuales
        foreach ($contractConditions as $contractCondition) {
            //Si no existe el ID ni el periodo -> crear la cantidad
            if (Quantities::where('idContractCondition', $contractCondition->id)->where('quantitiesPeriodo', $periodo)->count() == 0) {
                $newQuantities = new Quantities([
                    'idContractCondition' => $contractCondition->id,
                    'quantitiesCantidad' => 0,
                    'quantitiesPeriodo' => $periodo,
                    'quantitiesMonto' => null,
                ]);
                //Guardar la cantidad
                $newQuantities->save();
            }
            //Agregar nombre del modulo, paymentunit, contrato, cliente a todas las condiciones contractuales relevantes
            $this->fillModulesUnitsClientsContracts($contractCondition, $contractCondition);
            //Sacar la cantidades y agregar cantidad y periodo
            $getQuantity = Quantities::where('idContractCondition', $contractCondition->id)->where('quantitiesPeriodo', $periodo)->first();
            $contractCondition = Arr::add($contractCondition, 'quantitiesId', $getQuantity->id);
            $contractCondition = Arr::add($contractCondition, 'quantitiesCantidad', $getQuantity->quantitiesCantidad);
            $contractCondition = Arr::add($contractCondition, 'quantitiesPeriodo', $getQuantity->quantitiesPeriodo);
            $contractCondition = Arr::add($contractCondition, 'quantitiesMonto', $getQuantity->quantitiesMonto);

            $carbonPeriodo = Carbon::createFromFormat('Y-m-d', $getQuantity->quantitiesPeriodo . '-25');
            //Transformar mes a espaniol
            $contractCondition = Arr::add($contractCondition, 'quantitiesMonth', $meses[($carbonPeriodo->month) - 1]);
            $contractCondition = Arr::add($contractCondition, 'quantitiesYear', $carbonPeriodo->year);
        }
        $allContractConditions = ContractConditions::where('idContract', $idContrato)->get();
        return view('contracts.quantities', compact('authPermisos', 'contract', 'periodo', 'contractConditions', 'allContractConditions'));
    }

    public function quantitiesUpdate(Request $request, $idContrato, $periodo) {
        $largoTabla = $request->quantitiesTableLength;
        $request->validate([
            'quantitiesId' => 'required|array|min:' . $largoTabla,
            'quantitiesId.*' => 'required|numeric|min:0',
            'quantitiesCantidad'=> 'required|array|min:' . $largoTabla,
            'quantitiesCantidad.*'=> 'required|numeric|min:0',
            'quantitiesMonto'=> 'required|array|min:' . $largoTabla,
            'quantitiesMonto.*'=> 'numeric|min:0|nullable',
        ]);
        for ($i = 0; $i < $largoTabla; $i++ ) {
            $quantity = Quantities::find($request->quantitiesId[$i]);
            $quantity->quantitiesCantidad = $request->quantitiesCantidad[$i];
            $quantity->quantitiesMonto = $request->quantitiesMonto[$i];
            //Guardar si hay un cambio
            if ($quantity->isDirty()) {
                $quantity->save();
            }
        }
        return redirect()->action('ContractsController@quantitiesIndex', ['idContrato' => $idContrato, 'periodo' => $periodo])->with('success', 'Montos guardados exitosamente.');
    }

    public function getPermisos() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return $authPermisos;
    }

    public function fillModulesUnitsClientsContracts($contractCondition, $targetArray) {
        //Saca y agrega a la coleccion el nombre del modulo
        $getModule = Modules::where('id', $contractCondition['idModule'])->first();
        $targetArray = Arr::add($targetArray, 'contractCondition_moduleName', $getModule->moduleName);
        //Saca y agrega a la coleccion el nombre de la unidad de pago
        $getPaymentUnit = PaymentUnits::where('id', $contractCondition['idPaymentUnit'])->first();
        $targetArray = Arr::add($targetArray, 'contractCondition_paymentUnitName', $getPaymentUnit->payment_units);
        //Saca y agrega a la coleccion el nombre del cliente
        $getClient = Client::where('id', $contractCondition['idClient'])->first();
        $targetArray = Arr::add($targetArray, 'contractCondition_clientName', $getClient->clientRazonSocial);
        //Saca y agrega a la coleccion el nombre del contrato
        $getContract = Contracts::where('id', $contractCondition['idContract'])->first();
        $targetArray = Arr::add($targetArray, 'contractCondition_contractName', $getContract->contractsNombre);
        return $targetArray;
    }
}
