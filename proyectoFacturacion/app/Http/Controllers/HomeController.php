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
                if ($contract->contractsRecepcionMunicipal) {
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

                                $checkQuantity = Quantities::where('idContractCondition', $contractCondition_FijoVariable->id)
                                ->where('quantitiesCantidad', 1)
                                ->where('quantitiesPeriodo', $periodo)
                                ->where('quantitiesMonto', $quantityMonto)
                                ->first();

                                if ($checkQuantity == null) {
                                    $newQuantities = new Quantities([
                                        'idContractCondition' => $contractCondition_FijoVariable->id,
                                        'quantitiesCantidad' => 1,
                                        'quantitiesPeriodo' => $periodo,
                                        'quantitiesMonto' => $quantityMonto,
                                    ]);
                                    //Guardar la cantidad
                                    $newQuantities->save();
                                }


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


                                $checkQuantity = Quantities::where('idContractCondition', $contractCondition_FijoVariable->id)
                                ->where('quantitiesCantidad', $cantidadDetalles)
                                ->where('quantitiesPeriodo', $periodo)
                                ->where('quantitiesMonto', $quantityMonto)
                                ->first();

                                if ($checkQuantity == null) {
                                    $newQuantities = new Quantities([
                                        'idContractCondition' => $contractCondition_FijoVariable->id,
                                        'quantitiesCantidad' => $cantidadDetalles,
                                        'quantitiesPeriodo' => $periodo,
                                        'quantitiesMonto' => $quantityMonto,
                                    ]);
                                    //Guardar la cantidad
                                    $newQuantities->save();
                                }

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

    private function findHoldingModuleContract($idHolding, $idModule) {
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

    private function getUniqueContractConditions($contract) {
        if ($contract != null) {
            //Trae las unidades de pago unicas por cada contrato
            $uniqueContractConditions = ContractConditions::where('idContract', $contract->id)
            ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
            ->select('contract_conditions.idContract', 'contract_conditions.idPaymentUnit', 'payment_units.payment_units')
            ->distinct()
            ->get();
            //Si hay mas de 1
            if ($uniqueContractConditions->count() > 0) {
                return $uniqueContractConditions;
            }
        }
        return null;
    }

    private function getAPIClientID($holdingID, $apiClientId) {
        //El ID existe

        if ($apiClientId != "SIN INFORMACIÓN" && $apiClientId != null) {
            //Si tiene el formato incorrecto, corregir (Si no tiene guion)
            // Str::substr(start, length)
            $getGuion = Str::substr($apiClientId, Str::length($apiClientId) - 2, 1);
            if ($getGuion != '-') {
                $rutSinIdentificador = Str::substr($apiClientId, 0, Str::length($apiClientId) - 1);
                $rutIdentificador = Str::substr($apiClientId, Str::length($apiClientId) - 1, 1);
                $apiClientId = $rutSinIdentificador . '-' . $rutIdentificador;
            }

            $client = Client::where('clientRUT', $apiClientId)->first();
            if ($client != null) {
                //Si existe, cambiar a razon social en vez de holding
                return $client->id;
            }
        }
        return $holdingID;
    }

    private function getPaymentUnitID($uniqueConditions, $unidades) {
        /*
        GCI -> total_productos
        PVI -> numero_unidades
        */

        //id 7 Proyecto mayor a 65 unidades
        if ($uniqueConditions->contains('idPaymentUnit', 7)) {
            if ($unidades > 65) {
                $PaymentUnitId = 7;
            } else {
                $PaymentUnitId = 8;
            }
        }
        //id 8 Proyecto hasta 65 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 8))  {
            if ($unidades <= 65) {
                $PaymentUnitId = 8;
            } else {
                $PaymentUnitId = 7;
            }
        }
        //id 10 Proyecto hasta 50 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 10)) {
            if ($unidades <= 50) {
                $PaymentUnitId = 10;
            } else {
                $PaymentUnitId = 11;
            }
        }
        //id 11 Proyecto sobre 50 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 11))  {
            if ($unidades > 50) {
                $PaymentUnitId = 11;
            } else {
                $PaymentUnitId = 10;
            }
        }
        //id 12 Proyecto HASTA 60 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 12))  {
            if ($unidades <= 60) {
                $PaymentUnitId = 12;
            } else {
                $PaymentUnitId = 13;
            }
        }
        //id 13 Proyecto SOBRE 60 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 13))  {
            if ($unidades > 60) {
                $PaymentUnitId = 13;
            } else {
                $PaymentUnitId = 12;
            }
        }
        //id 18 Proyecto desde 50 unidades y más
        else if ($uniqueConditions->contains('idPaymentUnit', 18))  {
            if ($unidades >= 50) {
                $PaymentUnitId = 18;
            } else {
                $PaymentUnitId = 19;
            }
        }
        //id 19 Proyecto con menos de 50 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 19))  {
            if ($unidades < 50) {
                $PaymentUnitId = 19;
            } else {
                $PaymentUnitId = 18;
            }
        }
        //id 20 Proyecto con menos de 40 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 20))  {
            if ($unidades < 40) {
                $PaymentUnitId = 20;
            } else {
                $PaymentUnitId = 21;
            }
        }
        //id 21 Proyecto desde 40 unidades y más
        else if ($uniqueConditions->contains('idPaymentUnit', 21))  {
            if ($unidades >= 40) {
                $PaymentUnitId = 21;
            } else {
                $PaymentUnitId = 20;
            }
        }
        //id 22 Proyecto hasta 20 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 22))  {
            if ($unidades <= 20) {
                $PaymentUnitId = 22;
            } else if ($unidades > 20 && $unidades <= 35) {
                $PaymentUnitId = 23;
            } else {
                $PaymentUnitId = 24;
            }
        }
        //id 23 Proyecto con 21 a 35 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 23))  {
            if ($unidades <= 20) {
                $PaymentUnitId = 22;
            } else if ($unidades > 20 && $unidades <= 35) {
                $PaymentUnitId = 23;
            } else {
                $PaymentUnitId = 24;
            }
        }
        //id 24 Proyecto con mas de 35 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 24))  {
            if ($unidades <= 20) {
                $PaymentUnitId = 22;
            } else if ($unidades > 20 && $unidades <= 35) {
                $PaymentUnitId = 23;
            } else {
                $PaymentUnitId = 24;
            }
        }
        //id 25 Proyecto hasta 30 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 25))  {
            if ($unidades <= 30) {
                $PaymentUnitId = 25;
            } else {
                $PaymentUnitId = 26;
            }
        }
        //id 26 Proyecto entre 31 y 60 unidades
        else if ($uniqueConditions->contains('idPaymentUnit', 26))  {
            if ($unidades <= 30) {
                $PaymentUnitId = 25;
            } else {
                $PaymentUnitId = 26;
            }
        }
        else if ($uniqueConditions->contains('payment_units', 'Unidades por proyecto'))  {
            $PaymentUnitId = 30;
        }
        //cualquier otro id
        else {
            $PaymentUnitId = 2;
        }
        return $PaymentUnitId;
    }

    //GCI = 1, PVI = 2, DTP = 3, ET = 4, LICITA = 12
    private function getAPIresponse($modulo) {
        if ($modulo == 'GCI') {
            $key = getenv('API_GCI_KEY');
            $url = getenv('API_GCI_URL');
            $headerName = 'api_key';
        } else if ($modulo == 'PVI') {
            $key = getenv('API_PVI_KEY');
            $url = getenv('API_PVI_URL');
            $headerName = 'api_key';
        } else if ($modulo == 'DTP') {
            $key = getenv('API_DTP_KEY');
            $url = getenv('API_DTP_URL');
            $headerName = 'key';
        } else if ($modulo == 'ET') {
            $key = getenv('API_ET_KEY');
            $url = getenv('API_ET_URL');
            $headerName = 'key';
        } else if ($modulo == 'LICITA') {
            $key = getenv('API_LICITA_KEY');
            $url = getenv('API_LICITA_URL');
            $headerName = 'key';
        }

        $response = Http::withHeaders([
            $headerName => $key,
        ])->get($url);
        return $response->json();
    }

    public function getGCI() {
        $periodo = Carbon::now()->format('Y-m');
        $response = $this->getAPIresponse('GCI');
        foreach ($response as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 1);
            $uniqueConditions = $this->getUniqueContractConditions($contract);
            if ($uniqueConditions != null) {
                //Iteracion por empresas
                for ($i = 0; $i < count($res['empresas']); $i++) {
                    $clientID = $this->getAPIClientID($res['holding_id_facturacion'], $res['empresas'][$i]['identificador']);
                    //Iteracion por cada proyecto de la empresa
                    foreach ($res['empresas'][$i]['proyectos'] as $proyecto) {
                        $paymentUnitID = $this->getPaymentUnitID($uniqueConditions, $proyecto['total_productos']);
                        if ($proyecto['glosa_proyecto'] == null) {
                            $proyecto['glosa_proyecto'] = 'SIN GLOSA';
                        }
                        if ($proyecto['glosa_etapa'] == null) {
                            $proyecto['glosa_etapa'] = 'SIN ETAPA';
                        }
                        if ($proyecto['glosa_subagrupacion'] == null) {
                            $proyecto['glosa_subagrupacion'] = 'SIN SUBAGRUPACIÓN';
                        }
                        if ($proyecto['fecha_recepcion_municipal'] == '0000-00-00') {
                            $proyecto['fecha_recepcion_municipal'] = null;
                        }

                        $newContractPaymentDetails = new ContractPaymentDetails([
                            'idPaymentUnit' => $paymentUnitID,
                            'idClient' => $clientID,
                            'idContract' => $contract->id,
                            'contractPaymentDetails_period' => $periodo,
                            'ccontractPaymentDetails_quantity' => 1,
                            'contractPaymentDetails_description' => $proyecto['glosa_proyecto'] . ' / ' .  $proyecto['glosa_etapa'] . ' / ' .  $proyecto['glosa_subagrupacion'],
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

    public function getPVI() {
        $periodo = Carbon::now()->format('Y-m');
        $response = $this->getAPIresponse('PVI');
        foreach ($response as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 2);
            $uniqueConditions = $this->getUniqueContractConditions($contract);
            if ($uniqueConditions != null) {
                //Iteracion por empresas
                for ($i = 0; $i < count($res['empresas']); $i++) {
                    $clientID = $this->getAPIClientID($res['holding_id_facturacion'], $res['empresas'][$i]['identificador']);
                    //Iteracion por cada proyecto de la empresa
                    foreach ($res['empresas'][$i]['proyectos'] as $proyecto) {
                        $paymentUnitID = $this->getPaymentUnitID($uniqueConditions, $proyecto['numero_unidades']);
                        if ($proyecto['proyecto_nombre'] == null) {
                            $proyecto['proyecto_nombre'] = 'SIN NOMBRE';
                        }
                        if ($proyecto['etapa_id'] == null) {
                            $proyecto['etapa_id'] = 'SIN ETAPA';
                        }
                        if ($proyecto['fecha_recepcion_municipal'] == '0000-00-00') {
                            $proyecto['fecha_recepcion_municipal'] = null;
                        }
                        $newContractPaymentDetails = new ContractPaymentDetails([
                            'idPaymentUnit' => $paymentUnitID,
                            'idClient' => $clientID,
                            'idContract' => $contract->id,
                            'contractPaymentDetails_period' => $periodo,
                            'ccontractPaymentDetails_quantity' => 1,
                            'contractPaymentDetails_description' => $proyecto['proyecto_nombre'] . ' / ' . $proyecto['etapa_id'],
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

    public function getDTP() {
        $periodo = Carbon::now()->format('Y-m');
        $response = $this->getAPIresponse('DTP');
        foreach ($response as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 3);
            $uniqueConditions = $this->getUniqueContractConditions($contract);
            if ($uniqueConditions != null) {
                //Iteracion por empresas
                for ($i = 0; $i < count($res['empresas']); $i++) {
                    $clientID = $this->getAPIClientID($res['holding_id_facturacion'], $res['empresas'][$i]['identificador']);

                    $cantArchivos = $res['empresas'][$i]['cant_archivos'];
                    $cantProyectos = $res['empresas'][$i]['cant_proyectos_unidades'];
                    if ($cantArchivos == null) {
                        $cantArchivos = 0;
                    }
                    if ($cantProyectos == null) {
                        $cantProyectos = 0;
                    }

                    $newContractPaymentDetailsArchivos = new ContractPaymentDetails([
                        'idPaymentUnit' => 3,
                        'idClient' => $clientID,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantArchivos,
                        'contractPaymentDetails_description' => 'Archivos ' . $res['empresas'][$i]['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
                        'contractPaymentDetails_units' => null,
                        'contractPaymentDetails_glosaProyecto' => null,
                    ]);
                    $newContractPaymentDetailsArchivos->save();

                    $newContractPaymentDetailsProyectos = new ContractPaymentDetails([
                        'idPaymentUnit' => 2,
                        'idClient' => $clientID,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantProyectos,
                        'contractPaymentDetails_description' => 'Proyectos ' . $res['empresas'][$i]['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
                        'contractPaymentDetails_units' => null,
                        'contractPaymentDetails_glosaProyecto' => null,
                    ]);
                    $newContractPaymentDetailsProyectos->save();
                }
            }
        }
    }

    public function getLICITA() {
        $periodo = Carbon::now()->format('Y-m');
        $response = $this->getAPIresponse('LICITA');
        foreach ($response as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 12);
            $uniqueConditions = $this->getUniqueContractConditions($contract);
            if ($uniqueConditions != null) {
                //Iteracion por empresas
                for ($i = 0; $i < count($res['empresas']); $i++) {
                    $clientID = $this->getAPIClientID($res['holding_id_facturacion'], $res['empresas'][$i]['identificador']);

                    $cantArchivos = $res['empresas'][$i]['cant_licitaciones'];
                    if ($cantArchivos == null) {
                        $cantArchivos = 0;
                    }

                    $newContractPaymentDetails = new ContractPaymentDetails([
                        'idPaymentUnit' => 4,
                        'idClient' => $clientID,
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantArchivos,
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

    public function calculate_UnidadesPorProyectoQuantity($unidadesPorProyectoConditions, $detalles, $periodo) {
        $sortedVariableConditions = $unidadesPorProyectoConditions->sortBy('contractsConditions_Precio');
        $variableCondition = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Variable');
        //Montos iterables
        $quantityMonto = 0;
        $escalonAnterior = 0;
        //Montos totales
        $montoTotal = 0;
        $cantidadDetallesTotal = 0;

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
                $montoTotal += $quantityMonto;
                $cantidadDetallesTotal += $cantidadDetalles;
            }
        }

        $checkQuantity = Quantities::where('idContractCondition', $variableCondition[0]->id)
        ->where('quantitiesCantidad', $cantidadDetallesTotal)
        ->where('quantitiesPeriodo', $periodo)
        ->where('quantitiesMonto', $montoTotal)
        ->first();

        if ($checkQuantity == null) {
            $newQuantities = new Quantities([
                'idContractCondition' => $variableCondition[0]->id,
                'quantitiesCantidad' => $cantidadDetallesTotal,
                'quantitiesPeriodo' => $periodo,
                'quantitiesMonto' => $montoTotal,
            ]);
            //Guardar la cantidad
            $newQuantities->save();
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
