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
    public function managerExport()
    {
        //Periodo
        $periodo = Carbon::now()->format('Y-m');

        $periodoManager1 = Carbon::now()->format('m-Y');
        $periodoManager2 = Carbon::now()->addMonths(1)->format('m-Y');

        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        //Sacar todos los detalles
        $managerData = Tributarydocuments::where('tributarydocuments_period', $periodo)
        ->where('tributarydocuments_documentType', 'Factura')
        ->join('tributarydetails', 'tributarydocuments.id', '=', 'tributarydetails.idTributarydocument')
        ->join('payment_units', 'payment_units.id', '=', 'tributarydetails.idPaymentUnit')
        ->join('modules', 'modules.id', '=', 'tributarydetails.idModule')
        ->join('clients', 'clients.id', '=', 'tributarydetails.idClient')
        ->join('clients as holdings', 'holdings.id', '=', 'clients.clientParentId')
        ->join('users', 'holdings.idUser', '=', 'users.id')
        //->join('contract_payment_details', 'contract_payment_details.idClient', '=', 'tributarydetails.idClient')
        ->select('tributarydocuments.id', 'tributarydocuments.tributarydocuments_period', 'tributarydocuments.tributarydocuments_documentType', 'tributarydocuments.idClient as idHolding',
        'tributarydetails.idClient', 'tributarydetails.idPaymentUnit', 'tributarydetails.idModule',
        'tributarydetails.tributarydetails_paymentUnitQuantity', 'tributarydetails.tributarydetails_paymentPercentage', 'tributarydetails.tributarydetails_paymentValue', 'tributarydetails.created_at',
        'tributarydetails.tributarydetails_discount', 'tributarydetails.tributarydetails_paymentTotalValue', 'tributarydetails.tributarydetails_paymentTotalTaxValue',
        'clients.clientRazonSocial', 'clients.clientRUT', 'clients.clientContactEmail',
        'clients.clientPhone', 'clients.clientDirection', 'clients.clientBusinessActivity',
        'holdings.clientRazonSocial as holdingRazonSocial',
        'users.name', 'users.role',
        'payment_units.payment_units',
        //'contract_payment_details.contractPaymentDetails_description',
        'modules.moduleName')
        ->get();

        return view('billings.managerExport', compact('authPermisos', 'managerData', 'periodoManager1', 'periodoManager2'));
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
