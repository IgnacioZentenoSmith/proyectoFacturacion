<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractPaymentDetails;
use App\Client;
use App\Contracts;
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
        ])->get('https://comercialinmobiliarias.cl/gci_desarrollo/wpenaloza/api/');

        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 1);
            if ($contract != null) {
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
            }
        }
    }


    public function PVI_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'api_key' => '6b4c17219b48f933cc4c7caf69226d46e2b91ffd',
        ])->get('http://pvi.cl/facturacion/test.php');


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
    }


    public function ETDTP_Api() {
        $periodo = Carbon::now()->format('Y-m');

        $response = Http::withHeaders([
            'key' => '35328fcd1b8cf9e101fc0e398de0be08',
        ])->get('https://www.pok.cl/apirest/post.php');

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
                    $newContractPaymentDetailsArchivos = new ContractPaymentDetails([
                        'idPaymentUnit' => 3,
                        'idClient' => $res['holding_id_facturacion'],
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantArchivos,
                        'contractPaymentDetails_description' => 'Archivos ' . $empresas['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
                    ]);
                    $newContractPaymentDetailsArchivos->save();

                    $newContractPaymentDetailsProyectos = new ContractPaymentDetails([
                        'idPaymentUnit' => 2,
                        'idClient' => $res['holding_id_facturacion'],
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $cantProyectos,
                        'contractPaymentDetails_description' => 'Proyectos ' . $empresas['razon_social'],
                        'contractPaymentDetails_recepcionMunicipal' => null,
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
        ])->get('http://10.33.51.203/v3/restapi_licita_v1/post.php');

        foreach ($response->json() as $res) {
            $contract = $this->findHoldingModuleContract($res['holding_id_facturacion'], 5);
            if ($contract != null) {
                foreach ($res['empresas'] as $empresas) {
                    $newContractPaymentDetails = new ContractPaymentDetails([
                        'idPaymentUnit' => 3,
                        'idClient' => $res['holding_id_facturacion'],
                        'idContract' => $contract->id,
                        'contractPaymentDetails_period' => $periodo,
                        'ccontractPaymentDetails_quantity' => $empresas['cant_licitaciones'],
                        'contractPaymentDetails_description' => 'Licitaciones',
                        'contractPaymentDetails_recepcionMunicipal' => null,
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
}
