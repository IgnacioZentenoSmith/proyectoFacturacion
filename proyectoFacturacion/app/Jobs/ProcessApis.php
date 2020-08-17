<?php

namespace App\Jobs;

use App\Client;
use App\Contracts;
use App\Modules;
use App\PaymentUnits;
use App\Quantities;
use App\ContractPaymentDetails;
use App\ContractConditions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



class ProcessApis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getGCI();
        $this->getPVI();
        $this->getDTP();
        $this->getLICITA();
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
                        $description = $proyecto['glosa_proyecto'] . ' / ' .  $proyecto['glosa_etapa'] . ' / ' .  $proyecto['glosa_subagrupacion'];

                        $this->createContractPaymentDetails($paymentUnitID, $clientID, $contract->id, $periodo, 1, $description,
                            $proyecto['fecha_recepcion_municipal'], $proyecto['total_productos'], $proyecto['glosa_proyecto']);
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
                        $description = $proyecto['proyecto_nombre'] . ' / ' . $proyecto['etapa_id'];

                        $this->createContractPaymentDetails($paymentUnitID, $clientID, $contract->id, $periodo, 1, $description,
                            $proyecto['fecha_recepcion_municipal'], $proyecto['numero_unidades'], null);
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
                    $descripcionArchivos = 'Archivos ' . $res['empresas'][$i]['razon_social'];
                    $descripcionProyectos = 'Proyectos ' . $res['empresas'][$i]['razon_social'];
                    $this->createContractPaymentDetails(3, $clientID, $contract->id, $periodo, $cantArchivos, $descripcionArchivos,
                        null, null, null);
                    $this->createContractPaymentDetails(2, $clientID, $contract->id, $periodo, $cantProyectos, $descripcionProyectos,
                        null, null, null);
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

                    $this->createContractPaymentDetails(4, $clientID, $contract->id, $periodo, $cantArchivos, 'Licitaciones',
                        null, null, null);
                }
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

    private function createContractPaymentDetails($idPaymentUnit, $idClient, $idContract, $periodo,
    $quantity, $description, $recepcionMunicipal, $units, $glosaProyecto) {

        //Si no existe, crear
        $checkContractPaymentDetail = ContractPaymentDetails::where('idPaymentUnit', $idPaymentUnit)
        ->where('idClient', $idClient)
        ->where('idContract', $idContract)
        ->where('contractPaymentDetails_period', $periodo)
        ->where('ccontractPaymentDetails_quantity', $quantity)
        ->where('contractPaymentDetails_description', $description)
        ->where('contractPaymentDetails_recepcionMunicipal', $recepcionMunicipal)
        ->where('contractPaymentDetails_units', $units)
        ->where('contractPaymentDetails_glosaProyecto', $glosaProyecto)
        ->first();
        if ($checkContractPaymentDetail == null) {
            $newContractPaymentDetails = new ContractPaymentDetails([
                'idPaymentUnit' => $idPaymentUnit,
                'idClient' => $idClient,
                'idContract' => $idContract,
                'contractPaymentDetails_period' => $periodo,
                'ccontractPaymentDetails_quantity' => $quantity,
                'contractPaymentDetails_description' => $description,
                'contractPaymentDetails_recepcionMunicipal' => $recepcionMunicipal,
                'contractPaymentDetails_units' => $units,
                'contractPaymentDetails_glosaProyecto' => $glosaProyecto,
            ]);
            $newContractPaymentDetails->save();
        }
    }
}
