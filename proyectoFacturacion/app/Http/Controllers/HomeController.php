<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractPaymentDetails;
use App\Client;
use App\Contracts;
use App\ContractConditions;
use App\PaymentUnits;
use App\Quantities;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return view('home.index', compact('authPermisos'));
    }
    //holding_id_facturacion
    //GCI = 1, PVI = 2, DTP = 3, ET = 4, LICITA = 5
    public function GCI_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'api_key' => '1234567890',
        ])->get('https://comercialinmobiliarias.cl/api_facturacion/');


        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 1);
            if ($contract != null) {
                /*
                foreach ($res['empresas']['proyectos'] as $proyecto) {
                    $newContractPaymentDetails = new ContractPaymentDetails([
                        'idPaymentUnit' => 2,
                        'idClient' => $res['holding_id_facturacion'],
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => 1,
                        'contractPaymentDetails_description' => $proyecto['glosa_proyecto'] . ' ' .  $proyecto['glosa_etapa'] . ' ' .  $proyecto['glosa_subagrupacion'],
                        'contractPaymentDetails_recepcionMunicipal' => $proyecto['fecha_recepcion_municipal'],
                    ]);
                    $newContractPaymentDetails->save();
                }
                */
                //Trae las unidades de pago unicas por cada contrato
                $conditionsPaymentUnits = ContractConditions::where('idContract', $contract->id)
                ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
                ->select('contract_conditions.idContract', 'contract_conditions.idPaymentUnit', 'payment_units.payment_units')
                ->distinct()
                ->get();
                //Si hay mas de 1
                if ($conditionsPaymentUnits->count() > 0) {

                    foreach ($res['empresas']['proyectos'] as $proyecto) {
                            //total_productos = unidades
                            //id 7 Proyecto mayor a 65 unidades
                            if ($conditionsPaymentUnits->contains('idPaymentUnit', 7)) {
                                if ($proyecto['total_productos'] > 65) {
                                    $PaymentUnitId = 7;
                                } else {
                                    $PaymentUnitId = 8;
                                }
                            }
                            //id 8 Proyecto hasta 65 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 8))  {
                                if ($proyecto['total_productos'] <= 65) {
                                    $PaymentUnitId = 8;
                                } else {
                                    $PaymentUnitId = 7;
                                }
                            }
                            //id 10 Proyecto hasta 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 10)) {
                                if ($proyecto['total_productos'] <= 50) {
                                    $PaymentUnitId = 10;
                                } else {
                                    $PaymentUnitId = 11;
                                }
                            }
                            //id 11 Proyecto sobre 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 11))  {
                                if ($proyecto['total_productos'] > 50) {
                                    $PaymentUnitId = 11;
                                } else {
                                    $PaymentUnitId = 10;
                                }
                            }
                            //id 12 Proyecto HASTA 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 12))  {
                                if ($proyecto['total_productos'] <= 60) {
                                    $PaymentUnitId = 12;
                                } else {
                                    $PaymentUnitId = 13;
                                }
                            }
                            //id 13 Proyecto SOBRE 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 13))  {
                                if ($proyecto['total_productos'] > 60) {
                                    $PaymentUnitId = 13;
                                } else {
                                    $PaymentUnitId = 12;
                                }
                            }
                            //id 18 Proyecto desde 50 unidades y más
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 18))  {
                                if ($proyecto['total_productos'] >= 50) {
                                    $PaymentUnitId = 18;
                                } else {
                                    $PaymentUnitId = 19;
                                }
                            }
                            //id 19 Proyecto con menos de 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 19))  {
                                if ($proyecto['total_productos'] < 50) {
                                    $PaymentUnitId = 19;
                                } else {
                                    $PaymentUnitId = 18;
                                }
                            }
                            //id 20 Proyecto con menos de 40 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 20))  {
                                if ($proyecto['total_productos'] < 40) {
                                    $PaymentUnitId = 20;
                                } else {
                                    $PaymentUnitId = 21;
                                }
                            }
                            //id 21 Proyecto desde 40 unidades y más
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 21))  {
                                if ($proyecto['total_productos'] >= 40) {
                                    $PaymentUnitId = 21;
                                } else {
                                    $PaymentUnitId = 20;
                                }
                            }
                            //id 22 Proyecto hasta 20 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 22))  {
                                if ($proyecto['total_productos'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['total_productos'] > 20 && $proyecto['total_productos'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 23 Proyecto con 21 a 35 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 23))  {
                                if ($proyecto['total_productos'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['total_productos'] > 20 && $proyecto['total_productos'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 24 Proyecto con mas de 35 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 24))  {
                                if ($proyecto['total_productos'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['total_productos'] > 20 && $proyecto['total_productos'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 25 Proyecto hasta 30 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 25))  {
                                if ($proyecto['total_productos'] <= 30) {
                                    $PaymentUnitId = 25;
                                } else {
                                    $PaymentUnitId = 26;
                                }
                            }
                            //id 26 Proyecto entre 31 y 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 26))  {
                                if ($proyecto['total_productos'] <= 30) {
                                    $PaymentUnitId = 25;
                                } else {
                                    $PaymentUnitId = 26;
                                }
                            }
                            else if ($conditionsPaymentUnits->contains('payment_units', 'Unidades por proyecto'))  {
                                $PaymentUnitId = 30;
                            }
                            //cualquier otro id
                            else {
                                $PaymentUnitId = 2;
                            }
                            //Inicializar id con el holding
                            $idClient = $res['holding_id_facturacion'];
                            //Ver si existe identificador en el arreglo
                            if (array_key_exists('identificador', $res['empresas'])) {
                                //Ver si NO es null, null -> false
                                if (isset($res['empresas']['identificador'])) {
                                    //Si existe, verificar si esta razon social existe
                                    $client = Client::where('clientRUT', $res['empresas']['identificador'])->first();
                                    if ($client != null) {
                                        //Si existe, cambiar a razon social en vez de holding
                                        $idClient = $client->id;
                                    }
                                }
                            }

                            $newContractPaymentDetails = new ContractPaymentDetails([
                                'idPaymentUnit' => $PaymentUnitId,
                                'idClient' => $idClient,
                                'idContract' => $contract->id,
                                'contractPaymentDetails_period' => $periodo,
                                'ccontractPaymentDetails_quantity' => 1,
                                'contractPaymentDetails_description' => $proyecto['glosa_proyecto'] . ' ' .  $proyecto['glosa_etapa'] . ' ' .  $proyecto['glosa_subagrupacion'],
                                'contractPaymentDetails_recepcionMunicipal' => $proyecto['fecha_recepcion_municipal'],
                                'contractPaymentDetails_units' => $proyecto['total_productos'],
                                'contractPaymentDetails_glosaProyecto' => $proyecto['glosa_proyecto'],
                            ]);
                            $newContractPaymentDetails->save();

                    }
                }
            }
        }
    }


    public function PVI_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'api_key' => '6b4c17219b48f933cc4c7caf69226d46e2b91ffd',
        ])->get('https://pvi.cl/api_facturacion/');

         //numero_unidades
         /*
        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 2);
            if ($contract != null) {
                foreach ($res['empresas']['proyectos'] as $proyecto) {
                    $newContractPaymentDetails = new ContractPaymentDetails([
                        'idPaymentUnit' => 2,
                        'idClient' => $res['holding_id_facturacion'],
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => 1,
                        'contractPaymentDetails_description' => $proyecto['proyecto_nombre'] . ' Unidades: ' . $proyecto['numero_unidades'],
                        'contractPaymentDetails_recepcionMunicipal' => $proyecto['fecha_recepcion_municipal'],
                    ]);
                    $newContractPaymentDetails->save();
                }
            }
        }
        */

        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 2);
            if ($contract != null) {
                //Trae las unidades de pago unicas por cada contrato
                $conditionsPaymentUnits = ContractConditions::where('idContract', $contract->id)
                ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
                ->select('contract_conditions.idContract', 'contract_conditions.idPaymentUnit', 'payment_units.payment_units')
                ->distinct()
                ->get();
                //Si hay mas de 1
                if ($conditionsPaymentUnits->count() > 0) {
                    if (array_key_exists('proyectos', $res['empresas'])) {


                    foreach ($res['empresas']['proyectos'] as $proyecto) {
                            //numero_unidades = unidades
                            //id 7 Proyecto mayor a 65 unidades
                            if ($conditionsPaymentUnits->contains('idPaymentUnit', 7)) {
                                if ($proyecto['numero_unidades'] > 65) {
                                    $PaymentUnitId = 7;
                                } else {
                                    $PaymentUnitId = 8;
                                }
                            }
                            //id 8 Proyecto hasta 65 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 8))  {
                                if ($proyecto['numero_unidades'] <= 65) {
                                    $PaymentUnitId = 8;
                                } else {
                                    $PaymentUnitId = 7;
                                }
                            }
                            //id 10 Proyecto hasta 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 10)) {
                                if ($proyecto['numero_unidades'] <= 50) {
                                    $PaymentUnitId = 10;
                                } else {
                                    $PaymentUnitId = 11;
                                }
                            }
                            //id 11 Proyecto sobre 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 11))  {
                                if ($proyecto['numero_unidades'] > 50) {
                                    $PaymentUnitId = 11;
                                } else {
                                    $PaymentUnitId = 10;
                                }
                            }
                            //id 12 Proyecto HASTA 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 12))  {
                                if ($proyecto['numero_unidades'] <= 60) {
                                    $PaymentUnitId = 12;
                                } else {
                                    $PaymentUnitId = 13;
                                }
                            }
                            //id 13 Proyecto SOBRE 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 13))  {
                                if ($proyecto['numero_unidades'] > 60) {
                                    $PaymentUnitId = 13;
                                } else {
                                    $PaymentUnitId = 12;
                                }
                            }
                            //id 18 Proyecto desde 50 unidades y más
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 18))  {
                                if ($proyecto['numero_unidades'] >= 50) {
                                    $PaymentUnitId = 18;
                                } else {
                                    $PaymentUnitId = 19;
                                }
                            }
                            //id 19 Proyecto con menos de 50 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 19))  {
                                if ($proyecto['numero_unidades'] < 50) {
                                    $PaymentUnitId = 19;
                                } else {
                                    $PaymentUnitId = 18;
                                }
                            }
                            //id 20 Proyecto con menos de 40 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 20))  {
                                if ($proyecto['numero_unidades'] < 40) {
                                    $PaymentUnitId = 20;
                                } else {
                                    $PaymentUnitId = 21;
                                }
                            }
                            //id 21 Proyecto desde 40 unidades y más
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 21))  {
                                if ($proyecto['numero_unidades'] >= 40) {
                                    $PaymentUnitId = 21;
                                } else {
                                    $PaymentUnitId = 20;
                                }
                            }
                            //id 22 Proyecto hasta 20 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 22))  {
                                if ($proyecto['numero_unidades'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['numero_unidades'] > 20 && $proyecto['numero_unidades'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 23 Proyecto con 21 a 35 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 23))  {
                                if ($proyecto['numero_unidades'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['numero_unidades'] > 20 && $proyecto['numero_unidades'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 24 Proyecto con mas de 35 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 24))  {
                                if ($proyecto['numero_unidades'] <= 20) {
                                    $PaymentUnitId = 22;
                                } else if ($proyecto['numero_unidades'] > 20 && $proyecto['numero_unidades'] <= 35) {
                                    $PaymentUnitId = 23;
                                } else {
                                    $PaymentUnitId = 24;
                                }
                            }
                            //id 25 Proyecto hasta 30 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 25))  {
                                if ($proyecto['numero_unidades'] <= 30) {
                                    $PaymentUnitId = 25;
                                } else {
                                    $PaymentUnitId = 26;
                                }
                            }
                            //id 26 Proyecto entre 31 y 60 unidades
                            else if ($conditionsPaymentUnits->contains('idPaymentUnit', 26))  {
                                if ($proyecto['numero_unidades'] <= 30) {
                                    $PaymentUnitId = 25;
                                } else {
                                    $PaymentUnitId = 26;
                                }
                            }
                            else if ($conditionsPaymentUnits->contains('payment_units', 'Unidades por proyecto'))  {
                                $PaymentUnitId = 30;
                            }
                            //cualquier otro id
                            else {
                                $PaymentUnitId = 2;
                            }
                            //$res['empresas']['razon_social_empresa']
                            //Inicializar id con el holding
                            $idClient = $res['holding_id_facturacion'];
                            //Ver si existe identificador en el arreglo
                            if (array_key_exists('identificador', $res['empresas'])) {
                                //Ver si NO es null, null -> false
                                if (isset($res['empresas']['identificador'])) {
                                    //Si existe, verificar si esta razon social existe
                                    $client = Client::where('clientRUT', $res['empresas']['identificador'])->first();
                                    if ($client != null) {
                                        //Si existe, cambiar a razon social en vez de holding
                                        $idClient = $client->id;
                                    }
                                }
                            }
                            $newContractPaymentDetails = new ContractPaymentDetails([
                                'idPaymentUnit' => $PaymentUnitId,
                                'idClient' => $idClient,
                                'idContract' => $contract->id,
                                'contractPaymentDetails_period' => $periodo,
                                'ccontractPaymentDetails_quantity' => 1,
                                'contractPaymentDetails_description' => $proyecto['proyecto_nombre'],
                                'contractPaymentDetails_recepcionMunicipal' => $proyecto['fecha_recepcion_municipal'],
                                'contractPaymentDetails_units' => $proyecto['numero_unidades'],
                                'contractPaymentDetails_glosaProyecto' => null,
                            ]);
                            $newContractPaymentDetails->save();

                    }
                }
                }
            }
        }
    }
    /*
    la de DTP acá:
    https://www.pok.cl/api_facturacion/
    key: 35328fcd1b8cf9e101fc0e398de0be08
    */


    public function ETDTP_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'key' => '35328fcd1b8cf9e101fc0e398de0be08',
        ])->get('https://www.pok.cl/api_facturacion/');

        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 3);
            if ($contract != null) {
                foreach ($res['empresas'] as $empresas) {
                    $cantArchivos = $empresas['cant_archivos'];
                    $cantProyectos = $empresas['cant_proyectos_unidades'];
                    if ($cantArchivos == null) {
                        $cantArchivos = 0;
                    }
                    if ($cantProyectos == null) {
                        $cantProyectos = 0;
                    }
                    //Inicializar id con el holding
                    $idClient = $res['holding_id_facturacion'];
                    //Ver si existe identificador en el arreglo
                    if (array_key_exists('identificador', $res['empresas'])) {
                        //Ver si NO es null, null -> false
                        if (isset($res['empresas']['identificador'])) {
                            //Si existe, verificar si esta razon social existe
                            $client = Client::where('clientRUT', $res['empresas']['identificador'])->first();
                            if ($client != null) {
                                //Si existe, cambiar a razon social en vez de holding
                                $idClient = $client->id;
                            }
                        }
                    }

                    $newContractPaymentDetailsArchivos = new ContractPaymentDetails([
                        'idPaymentUnit' => 3,
                        'idClient' => $idClient,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantArchivos,
                        'contractPaymentDetails_description' => 'Archivos ' . $empresas['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
                        'contractPaymentDetails_units' => null,
                        'contractPaymentDetails_glosaProyecto' => null,
                    ]);
                    $newContractPaymentDetailsArchivos->save();

                    $newContractPaymentDetailsProyectos = new ContractPaymentDetails([
                        'idPaymentUnit' => 2,
                        'idClient' => $idClient,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantProyectos,
                        'contractPaymentDetails_description' => 'Proyectos ' . $empresas['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
                        'contractPaymentDetails_units' => null,
                        'contractPaymentDetails_glosaProyecto' => null,
                    ]);
                    $newContractPaymentDetailsProyectos->save();
                }
            }
        }
    }


    public function LICITA_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'key' => '3d524a53c110e4c22463b10ed32cef9d',
        ])->get('https://planoksecure.licitaok.cl/api_facturacion/');

        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 5);
            if ($contract != null) {
                foreach ($res['empresas'] as $empresas) {
                    //Inicializar id con el holding
                    $idClient = $res['holding_id_facturacion'];
                    //Ver si existe identificador en el arreglo
                    if (array_key_exists('identificador', $res['empresas'])) {
                        //Ver si NO es null, null -> false
                        if (isset($res['empresas']['identificador'])) {
                            //Si existe, verificar si esta razon social existe
                            $client = Client::where('clientRUT', $res['empresas']['identificador'])->first();
                            if ($client != null) {
                                //Si existe, cambiar a razon social en vez de holding
                                $idClient = $client->id;
                            }
                        }
                    }
                    $newContractPaymentDetails = new ContractPaymentDetails([
                        'idPaymentUnit' => 3,
                        'idClient' => $idClient,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $empresas['cant_licitaciones'],
                        'contractPaymentDetails_description' => 'Licitaciones',
                        'contractPaymentDetails_recepcionMunicipal' => null,
                        'contractPaymentDetails_units' => null,
                        'contractPaymentDetails_glosaProyecto' => null,
                    ]);
                    $newContractPaymentDetails->save();
                }
            }
        }
    }


    public function findHoldingModuleContract($idHolding, $idModule) {
        if ($idHolding != null) {
            $holding = Client::find($idHolding);
            //Holding existe
            if ($holding != null) {
                $contract = Contracts::where('idClient', $holding->id)->where('idModule', $idModule)->first();
                //Contrato existe
                if ($contract != null) {
                    return $contract;
                }
            }
        }
        return null;
    }

    public function apiQuantities() {
        $periodo = Carbon::now()->format('Y-m');

        $contracts = Contracts::all();
        foreach ($contracts as $contract) {
            //Saca todos los detalles del contrato de este periodo
            $contractPaymentDetails = ContractPaymentDetails::where('contractPaymentDetails_period', $periodo)
                ->where('idContract', $contract->id)
                ->get();

            //Hay al menos 1 detalle -> buscar condiciones contractuales
            if ($contractPaymentDetails->count() > 0) {
                if ($contract->contractsRecepcionMunicipal == true) {
                    /*
                    Si glosa proyecto igual
                        si fecha igual
                            no cobrar
                        else
                            cobrar
                    else
                        cobrar
                    */
                    //Saca los que tienen glosa y recep municipal iguales en GCI
                    $contractPaymentDetails = $contractPaymentDetails->unique(function ($item) {
                        return $item['contractPaymentDetails_glosaProyecto'].$item['contractPaymentDetails_recepcionMunicipal'];
                    });
                }
                //Sacar las condiciones contractuales validas para este periodo del contrato
                /*
                Donde sea en este contrato
                Donde la fecha de inicio sea menor o igual al 25 de este mes
                Donde la fecha de termino sea mayor o igual al 25 de este mes
                */
                $contractConditions = ContractConditions::where('idContract', $contract->id)
                ->where('contractsConditions_fechaInicio', '<=', $periodo . '-25')
                ->where(function ($query) use ($periodo) {
                    $query->whereNull('contractsConditions_fechaTermino')
                        ->orWhere('contractsConditions_fechaTermino', '>=', $periodo . '-25');
                })
                ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
                ->select('contract_conditions.*', 'payment_units.payment_units')
                ->get();

                //Donde la modalidad sea fija o variable
                $contractConditions_FijoVariable = $contractConditions->whereIn('contractsConditions_Modalidad', ['Fijo', 'Variable']);

                //Hay al menos 1 -> generar cantidades
                if ($contractConditions_FijoVariable->count() > 0) {
                    foreach ($contractConditions_FijoVariable as $contractCondition_FijoVariable) {
                        //Sacar los detalles de esta unidad de pago
                        $detalles = $contractPaymentDetails->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit);

                        //Unidades por proyecto
                        if ($contractCondition_FijoVariable->payment_units == 'Unidades por proyecto') {
                            $unidadesPorProyectoConditions = $contractConditions->where('payment_units', 'Unidades por proyecto');
                            $this->calculate_UnidadesPorProyectoQuantity($unidadesPorProyectoConditions, $detalles, $periodo);
                        }
                        //Otras unidades de cobro

                        else {
                            $cantidadDetalles = $detalles->count();
                            //Fijo
                            if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Fijo') {
                                $quantityMonto = $contractCondition_FijoVariable->contractsConditions_Precio;


                                $newQuantities = new Quantities([
                                    'idContractCondition' => $contractCondition_FijoVariable->id,
                                    'quantitiesCantidad' => 1,
                                    'quantitiesPeriodo' => $periodo,
                                    'quantitiesMonto' => $quantityMonto,
                                ]);
                                //Guardar la cantidad
                                $newQuantities->save();


                                echo $contractCondition_FijoVariable->id;
                                echo '<br>';
                                echo $cantidadDetalles;
                                echo '<br>';
                                echo $periodo;
                                echo '<br>';
                                echo $quantityMonto;
                                echo '<br>';
                                echo '<br>';
                                echo '<br>';

                            }
                            //Variable
                            else if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Variable')  {
                                //Ordenar las condiciones -> variable / escalonados ... (de menor a mayor precio)  / adicional
                                $variableConditions = $contractConditions->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit);
                                if ($variableConditions->where('contractsConditions_Modalidad', 'Adicional')) {
                                    $adicional = $variableConditions->where('contractsConditions_Modalidad', 'Adicional');
                                    $variableConditions = $variableConditions->where('contractsConditions_Modalidad', '!=', 'Adicional');
                                    $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
                                    $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
                                    $sortedVariableConditions = $sortedVariableConditions->concat($adicional);
                                }
                                else {
                                    $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
                                    $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
                                }
                                //Descuentos
                                if ($variableConditions->where('contractsConditions_Modalidad', 'Descuento')) {
                                    $descuento = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Descuento');
                                    $sortedVariableConditions = $sortedVariableConditions->where('contractsConditions_Modalidad', '!=', 'Descuento');
                                    $sortedVariableConditions = $sortedVariableConditions->concat($descuento);
                                }
                                $quantityMonto = 0;
                                $escalonAnterior = 0;

                                foreach ($sortedVariableConditions as $sortedVariableCondition) {
                                    //Variable
                                    if ($sortedVariableCondition->contractsConditions_Modalidad == 'Variable') {
                                        $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                                        $escalonAnterior = $cantidadCondicion;
                                        if ($cantidadDetalles - $cantidadCondicion >= 0) {
                                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                                        }
                                    }
                                    //Escalonado
                                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Escalonado') {
                                        $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                                        //Si es mayor
                                        if ($cantidadDetalles >= $cantidadCondicion) {
                                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                                        }
                                        //Si es mayor al anterior y menor a este escalon
                                        else if ($cantidadDetalles > $escalonAnterior && $cantidadDetalles <= $cantidadCondicion) {
                                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                                        }
                                        $escalonAnterior = $cantidadCondicion;
                                    }

                                    //Adicional
                                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Adicional') {
                                        if ($cantidadDetalles - $maxCantidad >= 1) {
                                            $quantityMonto += ($cantidadDetalles - $maxCantidad) * $sortedVariableCondition->contractsConditions_Precio;
                                        }
                                    }
                                    //Descuento
                                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Descuento') {
                                        $quantityMonto = round($quantityMonto * (100 - $sortedVariableCondition->contractsConditions_Precio) / 100, 2);
                                    }
                                }



                                $newQuantities = new Quantities([
                                    'idContractCondition' => $contractCondition_FijoVariable->id,
                                    'quantitiesCantidad' => $cantidadDetalles,
                                    'quantitiesPeriodo' => $periodo,
                                    'quantitiesMonto' => $quantityMonto,
                                ]);
                                //Guardar la cantidad
                                $newQuantities->save();

                                echo $contractCondition_FijoVariable->id;
                                echo '<br>';
                                echo $cantidadDetalles;
                                echo '<br>';
                                echo $periodo;
                                echo '<br>';
                                echo $quantityMonto;
                                echo '<br>';
                                echo '<br>';
                                echo '<br>';
                            }
                        }

                    }
                }
            }
        }
    }

    public function calculate_UnidadesPorProyectoQuantity($unidadesPorProyectoConditions, $detalles, $periodo) {
        $variableConditions = $unidadesPorProyectoConditions;
        //Ordenar las condiciones -> variable / escalonados ... (de menor a mayor precio)  / adicional
        if ($variableConditions->where('contractsConditions_Modalidad', 'Adicional')) {
            $adicional = $variableConditions->where('contractsConditions_Modalidad', 'Adicional');
            $variableConditions = $variableConditions->where('contractsConditions_Modalidad', '!=', 'Adicional');
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
            $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
            $sortedVariableConditions = $sortedVariableConditions->concat($adicional);
        }
        else {
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
            $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
        }
        //Descuentos
        if ($variableConditions->where('contractsConditions_Modalidad', 'Descuento')) {
            $descuento = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->where('contractsConditions_Modalidad', '!=', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->concat($descuento);
        }
        $quantityMonto = 0;
        $escalonAnterior = 0;

        foreach ($sortedVariableConditions as $sortedVariableCondition) {
            $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
            //Variable
            if ($sortedVariableCondition->contractsConditions_Modalidad == 'Variable') {
                $detallesVariable = $detalles->where('contractPaymentDetails_units', '<=', $cantidadCondicion);
                $cantidadDetalles = $detallesVariable->count();
                $escalonAnterior = $cantidadCondicion;
                //Si hay al menos 1 proyecto q cumpla con esta condicion, cobrar
                if ($cantidadDetalles > 0) {
                    $quantityMonto = $sortedVariableCondition->contractsConditions_Precio * $cantidadDetalles;
                }
                //De lo contrario, no cobrar
                else {
                    $quantityMonto = null;
                }
            }
            //Escalonado
            else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Escalonado') {
                //Buscar donde esten entre este escalon
                $detallesVariable = $detalles->where('contractPaymentDetails_units', '<=', $cantidadCondicion)
                ->where('contractPaymentDetails_units', '>=', $escalonAnterior);
                $cantidadDetalles = $detallesVariable->count();

                //Si hay al menos 1 proyecto q cumpla con esta condicion, cobrar
                if ($cantidadDetalles > 0) {
                    $quantityMonto = $sortedVariableCondition->contractsConditions_Precio * $cantidadDetalles;
                }
                //De lo contrario, no cobrar
                else {
                    $quantityMonto = null;
                }
                $escalonAnterior = $cantidadCondicion;
            }
            //Adicional
            /*
            else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Adicional') {
                if ($cantidadDetalles - $maxCantidad >= 1) {
                    $quantityMonto += ($cantidadDetalles - $maxCantidad) * $sortedVariableCondition->contractsConditions_Precio;
                }
            }
            */
            //Descuento
            /*
            else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Descuento') {
                if ($quantityMonto != null) {
                    $quantityMonto = round($quantityMonto * (100 - $sortedVariableCondition->contractsConditions_Precio) / 100, 2);
                }
            }
            */
            if ($quantityMonto != null) {
                $newQuantities = new Quantities([
                    'idContractCondition' => $sortedVariableCondition->id,
                    'quantitiesCantidad' => $cantidadDetalles,
                    'quantitiesPeriodo' => $periodo,
                    'quantitiesMonto' => $quantityMonto,
                ]);
                //Guardar la cantidad
                $newQuantities->save();
            }
        }


    }

    public function sortVariableConditions($variableConditions) {
        //Ordenar las condiciones -> variable / escalonados ... (de menor a mayor precio)  / adicional
        if ($variableConditions->where('contractsConditions_Modalidad', 'Adicional')) {
            $adicional = $variableConditions->where('contractsConditions_Modalidad', 'Adicional');
            $variableConditions = $variableConditions->where('contractsConditions_Modalidad', '!=', 'Adicional');
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
            $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
            $sortedVariableConditions = $sortedVariableConditions->concat($adicional);
        }
        else {
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
            $maxCantidad = $sortedVariableConditions->max('contractsConditions_Cantidad');
        }
        //Descuentos
        if ($variableConditions->where('contractsConditions_Modalidad', 'Descuento')) {
            $descuento = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->where('contractsConditions_Modalidad', '!=', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->concat($descuento);
        }
        return $sortedVariableConditions;
    }
}
