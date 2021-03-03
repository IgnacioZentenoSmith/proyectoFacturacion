<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractConditions;
use App\ContractDistribution;
use App\ContractPaymentDetails;
use App\Binnacle;

use App\Contracts;
use App\Client;
use App\Modules;
use App\PaymentUnits;
use App\User;
use App\Quantities;
use App\Tributarydocuments;
use App\Tributarydetails;
use Auth;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class ManagerController extends Controller
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
    public function managerExport($periodo)
    {
        
        if ($periodo == 0) {
            //Periodo
            $periodo = Carbon::now()->format('Y-m');
        }
        
        $periodoManager1 = Carbon::parse($periodo)->format('m-Y');
        $peridoDos = new Carbon($periodo);
        $periodoManager2 = Carbon::parse($peridoDos->addMonths(1))->format('m-Y');


        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();


        $results = DB::select( DB::raw("SELECT
            tributarydocuments.id,
            tributarydocuments.tributarydocuments_period,
            tributarydocuments.tributarydocuments_documentType,
            tributarydocuments.idClient AS idHolding,
            tributarydetails.idClient,
            tributarydetails.idPaymentUnit,
            tributarydetails.idModule,
            tributarydetails.tributarydetails_paymentUnitQuantity,
            tributarydetails.tributarydetails_paymentPercentage,
            tributarydetails.tributarydetails_paymentValue,
            tributarydetails.created_at,
            tributarydetails.tributarydetails_discount,
            tributarydetails.tributarydetails_paymentTotalValue,
            tributarydetails.tributarydetails_paymentTotalTaxValue,
            clients.clientRazonSocial,
            clients.clientRUT,
            clients.clientContactEmail,
            clients.clientPhone,
            clients.clientDirection,
            clients.clientBusinessActivity,
            holdings.clientRazonSocial AS holdingRazonSocial,
            users.NAME,
            users.role,
            payment_units.payment_units,
            tributarydocuments.idContract AS idContratoReal,
            modules.moduleName 
        FROM
            tributarydocuments
            JOIN tributarydetails ON tributarydocuments.id = tributarydetails.idTributarydocument
            JOIN payment_units ON payment_units.id = tributarydetails.idPaymentUnit
            JOIN modules ON modules.id = tributarydetails.idModule
            JOIN clients ON clients.id = tributarydetails.idClient
            JOIN clients AS holdings ON holdings.id = clients.clientParentId
            JOIN users ON users.id = holdings.idUser 
            JOIN contracts ON contracts.idModule = modules.id
        WHERE
            tributarydocuments_documentType = 'Factura' 
            AND tributarydocuments_period = '$periodo'
            GROUP BY tributarydocuments.id,
            tributarydocuments.tributarydocuments_period,
            tributarydocuments.tributarydocuments_documentType,
            tributarydocuments.idClient,
            tributarydetails.idClient,
            tributarydetails.idPaymentUnit,
            tributarydetails.idModule,
            tributarydetails.tributarydetails_paymentUnitQuantity,
            tributarydetails.tributarydetails_paymentPercentage,
            tributarydetails.tributarydetails_paymentValue,
            tributarydetails.created_at,
            tributarydetails.tributarydetails_discount,
            tributarydetails.tributarydetails_paymentTotalValue,
            tributarydetails.tributarydetails_paymentTotalTaxValue,
            clients.clientRazonSocial,
            clients.clientRUT,
            clients.clientContactEmail,
            clients.clientPhone,
            clients.clientDirection,
            clients.clientBusinessActivity,
            holdings.clientRazonSocial,
            users.NAME,
            users.role,
            payment_units.payment_units,
            tributarydocuments.idContract,
            modules.moduleName 
        ") );


        $dataF = [];
        $dataFinal = [];
        foreach ($results as $manager) {

           
            $detail = ContractPaymentDetails::where('idClient', $manager->idClient)
                        ->where('contractPaymentDetails_description','not like','proyectos %')
                        ->where('contractPaymentDetails_period', $periodo)
                        ->where('contract_payment_details.idContract', $manager->idContratoReal)
                        ->select("contractPaymentDetails_description")
                        ->get();


            $dataFinal[] = [
                'tributarydocuments.id' => $manager->id,
                'tributarydocuments_documentType' => $manager->tributarydocuments_documentType,
                'idHolding' =>  $manager->idHolding,
                'idClient' =>   $manager->idClient,
                'idPaymentUnit' => $manager->idPaymentUnit,
                'idModule' => $manager->idModule,
                'tributarydetails_paymentUnitQuantity' => $manager->tributarydetails_paymentUnitQuantity,
                'tributarydetails_paymentPercentage' => $manager->tributarydetails_paymentPercentage,
                'tributarydetails_paymentValue' => $manager->tributarydetails_paymentValue,
                'created_at' => $manager->created_at,
                'tributarydetails_discount' => $manager->tributarydetails_discount,
                'tributarydetails_paymentTotalValue' => $manager->tributarydetails_paymentTotalValue,
                'tributarydetails_paymentTotalTaxValue' => $manager->tributarydetails_paymentTotalTaxValue,
                'clientRazonSocial' => $manager->clientRazonSocial,
                'clientRUT' => $manager->clientRUT,
                'clientContactEmail' => $manager->clientContactEmail,
                'clientPhone' => $manager->clientPhone,
                'clientDirection' => $manager->clientDirection,
                'clientBusinessActivity' => $manager->clientBusinessActivity,
                'holdingRazonSocial' => $manager->holdingRazonSocial,
                'payment_units' => $manager->payment_units,
                'moduleName' => $manager->moduleName,
                'detalles' => $detail,
            ];

        }


        return view('billings.managerExport', compact('authPermisos', 'dataFinal','periodo', 'periodoManager1', 'periodoManager2'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
