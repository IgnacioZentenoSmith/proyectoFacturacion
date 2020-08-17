<?php

namespace App\Http\Controllers;


use App\Jobs\SendNotifications;

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

class TributarydocumentsController extends Controller
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
    public function index($periodo)
    {
      $authPermisos = $this->getPermisos();
        if ($periodo == 0) {
          //Periodo
          $periodo = Carbon::now()->format('Y-m');
        }
        $documentosTributarios = Tributarydocuments::where('tributarydocuments_period', $periodo)->get();
        foreach ($documentosTributarios as $documentoTributario) {

          //Saca y agrega a la coleccion el nombre del contrato
          $getContract = Contracts::where('id', $documentoTributario['idContract'])->first();
          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_contractName', $getContract->contractsNombre);
          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_IVA', 19);
          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_isManual', $getContract->contractsManualContract);
          $montoTotalIva = $documentoTributario['tributarydocuments_totalAmount'] * 1.19;
          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_MontoTotalIVA', $montoTotalIva);
        }
        return view('billings.index', compact('authPermisos', 'periodo', 'documentosTributarios'));
    }

    public function generateDocumentos($periodo) {
      $authPermisos = $this->getPermisos();
      app('App\Http\Controllers\ApiquantitiesController')->apiQuantities();
      //HACER FACTURAS
        //Saca todos los contratos que ya han sido creados este periodo
        $getThisPeriodTributarydocuments = Tributarydocuments::where('tributarydocuments_period', $periodo)->get()->pluck('idContract');
        //Obtener todos los contratos activos -> tienen al menos 1 condicion contractual con cantidades
        //Filtrar todos los contratos que ya han sido creados
        $contratos = Contracts::where('contractsEstado', true)->whereNotIn('id', $getThisPeriodTributarydocuments)->get();
        foreach ($contratos as $contrato) {
          //Obtener todas las condiciones contractuales del contrato
          $condicionesContractuales = ContractConditions::where('idContract', $contrato->id)->get();
          //Obtener todas las cantidades que pertenecen a este periodo y a las condiciones contractuales de este contrato
          foreach ($condicionesContractuales as $condicionContractual) {
            //Saca la cantidad que tiene la ID de la condicion contractual y pertenece a este periodo y el monto no es nulo
            $getQuantity = Quantities::where('idContractCondition', $condicionContractual->id)
            ->where('quantitiesPeriodo', $periodo)
            ->whereNotNull('quantitiesMonto')
            ->first();
            if ($getQuantity != null) {
              $condicionContractual = Arr::add($condicionContractual, 'quantitiesId', $getQuantity->id);
              $condicionContractual = Arr::add($condicionContractual, 'quantitiesCantidad', $getQuantity->quantitiesCantidad);
              $condicionContractual = Arr::add($condicionContractual, 'quantitiesPeriodo', $getQuantity->quantitiesPeriodo);
              $condicionContractual = Arr::add($condicionContractual, 'quantitiesMonto', $getQuantity->quantitiesMonto);
            }
          }
          //Saca todas las condiciones contractuales con montos
          $condicionesContractuales = $condicionesContractuales->whereNotNull('quantitiesMonto');
          $uniqueContracts = $condicionesContractuales->unique('idContract');

          $uniqueContracts = $uniqueContracts->pluck('idContract');

          //Para cada contrato unico, generar factura con su monto
          foreach ($uniqueContracts as $uniqueContract) {
            $totalSuma = 0;
            $contract = Contracts::find($uniqueContract);

            foreach ($condicionesContractuales as $condicionContractual) {
              //Si es el mismo contrato que se esta revisando
              if ($condicionContractual->idContract == $uniqueContract) {
                $totalSuma += $condicionContractual->quantitiesMonto;
              }
            }
            $totalAmountTax = $totalSuma * 1.19;
            //Generar el documento
            $newTributaryDocument = new Tributarydocuments([
              'idClient' => $contract->idClient,
              'idContract' => $uniqueContract,
              'tributarydocuments_period' => $periodo,
              'tributarydocuments_documentType' => 'Factura',
              'tributarydocuments_totalAmount' => $totalSuma,
              'tributarydocuments_tax' => 19,
              'tributarydocuments_totalAmountTax' => $totalAmountTax,
            ]);
            $newTributaryDocument->save();
            app('App\Http\Controllers\BinnacleController')->reportBinnacle('CREATE', $newTributaryDocument->getTable(), $contract->contractsNombre, null, $newTributaryDocument);
            $this->createPaymentDetails($newTributaryDocument->id);
          }
        }

        return redirect()->action('TributarydocumentsController@index', ['periodo' => $periodo])->with('success', 'Facturas generadas exitosamente');
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

    public function documentDestroy($id)
    {
      $authPermisos = $this->getPermisos();
      $documentoTributario = Tributarydocuments::find($id);
      $contract = Contracts::find($documentoTributario->idContract);
      $periodo = $documentoTributario->tributarydocuments_period;
      app('App\Http\Controllers\BinnacleController')->reportBinnacle('DELETE', $documentoTributario->getTable(), $contract->contractsNombre, $documentoTributario, null);
      SendNotifications::dispatch('Facturas, ' . $contract->contractsNombre, 'Eliminación de factura')->onQueue('emails');
      $documentoTributario->delete();
      return redirect()->action('TributarydocumentsController@index', ['periodo' => $periodo])->with('success', 'Documento eliminado exitosamente');
    }/*
    public function generateNotaCredito($id, $periodo) {
      $authPermisos = $this->getPermisos();
      $documentoTributario = Tributarydocuments::find($id);
      $newTributaryDocument = new Tributarydocuments([
        'idContract' => $documentoTributario->idContract,
        'tributarydocuments_period' => $documentoTributario->tributarydocuments_period,
        'tributarydocuments_documentType' => 'Nota de crédito',
        'tributarydocuments_totalAmount' => $documentoTributario->tributarydocuments_totalAmount
      ]);
      $newTributaryDocument->save();
      return redirect()->action('TributarydocumentsController@index', ['periodo' => $periodo])->with('success', 'Nota de credito generada exitosamente');
    }
    */


    public function paymentDetailsIndex($idTributarydocument) {
        $authPermisos = $this->getPermisos();
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);

        $tributaryDetails = Tributarydetails::where('idTributarydocument', $tributaryDocument->id)
        ->join('payment_units', 'payment_units.id', '=', 'tributarydetails.idPaymentUnit')
        ->join('modules', 'modules.id', '=', 'tributarydetails.idModule')
        ->join('clients', 'clients.id', '=', 'tributarydetails.idClient')
        ->select('tributarydetails.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units', 'modules.moduleName')
        ->get();

        $contractPaymentDetails = ContractPaymentDetails::where('idContract', $contract->id)
        ->join('payment_units', 'payment_units.id', '=', 'contract_payment_details.idPaymentUnit')
        ->join('clients', 'clients.id', '=', 'contract_payment_details.idClient')
        ->select('contract_payment_details.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units')
        ->get();
        return view('billings.paymentDetails', compact('authPermisos', 'tributaryDocument', 'contract', 'tributaryDetails', 'contractPaymentDetails'));
    }

    public function redistribute(Request $request, $idTributarydocument) {
        $authPermisos = $this->getPermisos();
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);

        $tributaryDetails = Tributarydetails::where('idTributarydocument', $tributaryDocument->id)
        ->join('payment_units', 'payment_units.id', '=', 'tributarydetails.idPaymentUnit')
        ->join('modules', 'modules.id', '=', 'tributarydetails.idModule')
        ->join('clients', 'clients.id', '=', 'tributarydetails.idClient')
        ->select('tributarydetails.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units', 'modules.moduleName')
        ->get();

        $contractPaymentDetails = ContractPaymentDetails::where('idContract', $contract->id)
        ->join('payment_units', 'payment_units.id', '=', 'contract_payment_details.idPaymentUnit')
        ->join('clients', 'clients.id', '=', 'contract_payment_details.idClient')
        ->select('contract_payment_details.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units')
        ->get();

        $razonesSociales = Client::where('clientParentId', $contract->idClient)->get();
        return view('billings.redistribute', compact('authPermisos', 'tributaryDocument', 'contract', 'tributaryDetails', 'contractPaymentDetails', 'razonesSociales'));

    }

    public function generateRedistribucion(Request $request, $idTributarydocument) {
        $largoTabla = $request->tributaryDetailsTableLength;
        $montoTotal = $request->montoTotal;
        //Total de monto y de porcentaje deben ser monto total y 100% respectivamente
        $request->validate([
            'tributaryDetail_id' => 'required|array|min:' . $largoTabla,
            'tributaryDetail_id.*' => 'required|numeric|min:0',
            'tributarydetails_paymentPercentage'=> 'required|array|min:' . $largoTabla,
            'tributarydetails_paymentPercentage.*'=> 'required|numeric|between:0,100',
            'tributarydetails_paymentValue'=> 'required|array|min:' . $largoTabla,
            'tributarydetails_paymentValue.*'=> 'required|numeric|between:0,' . $montoTotal,
            'tributarydetails_discount'=> 'required|array|min:' . $largoTabla,
            'tributarydetails_discount.*'=> 'required|numeric|between:0,100',
            'tributarydetails_paymentTotalValue'=> 'required|array|min:' . $largoTabla,
            'tributarydetails_paymentTotalValue.*'=> 'required|numeric|between:0,' . $montoTotal,
            'porcentajeActual' =>'required|numeric|between:100,100',
            'montoActual' => 'required|numeric|between:' . $montoTotal . ',' . $montoTotal,
        ]);

        $authPermisos = $this->getPermisos();
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);
        //Crear nota de credito
        $tributaryDocument->tributarydocuments_documentType = 'Factura anulada';
        if ($tributaryDocument->tributarydocuments_totalAmountTax != $montoTotal) {
            $totalAmount = $montoTotal / 1.19;
            $totalAmountTax = $montoTotal;
        } else {
            $totalAmount = $tributaryDocument->tributarydocuments_totalAmount;
            $totalAmountTax = $tributaryDocument->tributarydocuments_totalAmountTax;
        }

        $preTributary = Client::find($idTributarydocument);
        app('App\Http\Controllers\BinnacleController')->reportBinnacle('UPDATE', $tributaryDocument->getTable(), $contract->contractsNombre, $preTributary, $tributaryDocument);
        SendNotifications::dispatch('Facturas, ' . $contract->contractsNombre, 'Redistribución de factura')->onQueue('emails');
        $tributaryDocument->save();

        //Generar la factura
        $newTributaryDocument = new Tributarydocuments([
            'idClient' => $tributaryDocument->idClient,
            'idContract' => $tributaryDocument->idContract,
            'tributarydocuments_period' => $tributaryDocument->tributarydocuments_period,
            'tributarydocuments_documentType' => 'Factura',
            'tributarydocuments_totalAmount' => $totalAmount,
            'tributarydocuments_tax' => $tributaryDocument->tributarydocuments_tax,
            'tributarydocuments_totalAmountTax' => $totalAmountTax,
          ]);
          $newTributaryDocument->save();
          app('App\Http\Controllers\BinnacleController')->reportBinnacle('CREATE', $newTributaryDocument->getTable(), $contract->contractsNombre, null, $newTributaryDocument);


        //Crear los nuevos detalles con los datos antiguos, adjuntandolos a la nueva factura con los datos nuevos
        for ($i = 0; $i < $largoTabla; $i++ ) {

            $newTributaryDetail = new Tributarydetails([
                'idTributarydocument' => $newTributaryDocument->id,
                'idClient' => $request->idClient[$i],
                'idPaymentUnit' => $request->idPaymentUnit[$i],
                'idModule' => $request->idModule[$i],
                'tributarydetails_paymentUnitQuantity' => $request->tributarydetails_paymentUnitQuantity[$i],
                'tributarydetails_paymentPercentage' => $request->tributarydetails_paymentPercentage[$i],
                'tributarydetails_paymentValue' => $request->tributarydetails_paymentValue[$i],
                'tributarydetails_discount' => $request->tributarydetails_discount[$i],
                'tributarydetails_paymentTotalValue' => $request->tributarydetails_paymentTotalValue[$i],
                'tributarydetails_paymentTotalTaxValue' => $request->tributarydetails_paymentTotalTaxValue[$i],
              ]);
            $newTributaryDetail->save();

        }
        $largoTablaContractPaymentDetails = $request->contractPaymentDetailsTableLength;
        $request->validate([
            'contractPaymentDetail_id' => 'required|array|min:' . $largoTablaContractPaymentDetails,
            'contractPaymentDetail_id.*' => 'required|numeric|min:0',
            'contractPaymentDetail_idClient'=> 'required|array|min:' . $largoTablaContractPaymentDetails,
            'contractPaymentDetail_idClient.*'=> 'required|numeric|min:0',
        ]);
        for ($i = 0; $i < $largoTablaContractPaymentDetails; $i++ ) {
            $contractPaymentDetail = ContractPaymentDetails::find($request->contractPaymentDetail_id[$i]);
            $contractPaymentDetail->idClient = $request->contractPaymentDetail_idClient[$i];
            if ($contractPaymentDetail->isDirty()) {
                $contractPaymentDetail->save();
            }
        }

        return redirect()->action('TributarydocumentsController@index', ['periodo' => 0])->with('success', 'Nota de crédito generada exitosamente');

    }

    public function createPaymentDetails($idTributarydocument) {
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);

        //Sacar todas las condiciones, cantidades y unidades de pago correspondientes a este periodo
        $thisPeriod_contractConditions_quantities_paymentUnits = ContractConditions::where('idContract', $contract->id)
        ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
        ->join('modules', 'modules.id', '=', 'contract_conditions.idModule')
        ->join('quantities', 'quantities.idContractCondition', '=', 'contract_conditions.id')
        ->where('quantities.quantitiesPeriodo', $tributaryDocument->tributarydocuments_period)
        ->whereNotNull('quantities.quantitiesMonto')
        ->select('contract_conditions.*', 'payment_units.payment_units', 'quantities.id as idQuantities', 'quantities.quantitiesCantidad', 'quantities.quantitiesPeriodo', 'quantities.quantitiesMonto', 'modules.moduleName')
        ->get();

        //Sacar las distribuciones de cada una de las razones sociales del contrato
        $contractDistributions = ContractDistribution::where('idContract', $contract->id)
        ->join('clients', 'clients.id', '=', 'contract_distribution.idClient')
        ->select('contract_distribution.*', 'clients.clientRazonSocial', 'clients.clientRUT')
        ->get();

        foreach ($contractDistributions as $contractDistribution) {
            foreach ($thisPeriod_contractConditions_quantities_paymentUnits as $thisPeriodData) {

                if ($contractDistribution->contractDistribution_type == "Porcentaje") {
                    $paymentQuantity = $thisPeriodData->quantitiesCantidad * $contractDistribution->contractDistribution_percentage/100;
                    if ($paymentQuantity > 0 && $paymentQuantity < 1) {
                        $paymentQuantity = 1;
                    } else {
                        $paymentQuantity = round($paymentQuantity);
                    }

                    $paymentValue = $thisPeriodData->quantitiesMonto * $contractDistribution->contractDistribution_percentage/100;

                    //Si no tiene descuento, es el mismo valor
                    if ($contractDistribution->contractDistribution_discount == 0) {
                        $paymentTotalValue = $paymentValue;
                    } else {
                        $paymentTotalValue = $paymentValue * (1 - $contractDistribution->contractDistribution_discount / 100);
                    }
                    $paymentTotalTaxValue = $paymentTotalValue * 1.19;

                    $newTributaryDetail = new Tributarydetails([
                        'idTributarydocument' => $idTributarydocument,
                        'idClient' => $contractDistribution->idClient,
                        'idPaymentUnit' => $thisPeriodData->idPaymentUnit,
                        'idModule' => $thisPeriodData->idModule,
                        'tributarydetails_paymentUnitQuantity' => $paymentQuantity,
                        'tributarydetails_paymentPercentage' => $contractDistribution->contractDistribution_percentage,
                        'tributarydetails_paymentValue' => $paymentValue,
                        'tributarydetails_discount' => $contractDistribution->contractDistribution_discount,
                        'tributarydetails_paymentTotalValue' => $paymentTotalValue,
                        'tributarydetails_paymentTotalTaxValue' => $paymentTotalTaxValue,

                      ]);
                      $newTributaryDetail->save();
                }
                //Por APIs
                else if ($contractDistribution->contractDistribution_type == "Unidad de cobro") {
                    //Sacar todos los detalles de este contrato en este periodo
                    $totalContractPaymentDetails = ContractPaymentDetails::where('contractPaymentDetails_period', $tributaryDocument->tributarydocuments_period)
                    ->where('idContract', $tributaryDocument->idContract)
                    ->get();
                    //totalContractPaymentDetailsQuantity representa el 100%
                    $totalContractPaymentDetailsQuantity = $thisPeriod_contractConditions_quantities_paymentUnits->sum('quantitiesCantidad');
                    //Sacar todos los detalles de esta razon social
                    //$contractPaymentDetails = $totalContractPaymentDetails->where('idClient', $contractDistribution->idClient);
                    //$contractPaymentDetails = $contractPaymentDetails->where('idPaymentUnit', $thisPeriodData->idPaymentUnit);
                    //cantidad de esta razon social y de esta unidad de pago en especifico
                    $paymentQuantity = $thisPeriodData->quantitiesCantidad;
                    //Evitar division por 0, Transformar cantidad a porcentaje respecto del total
                    if ($totalContractPaymentDetailsQuantity > 0) {
                        $paymentPercentage = $paymentQuantity * 100 / $totalContractPaymentDetailsQuantity;
                    } else {
                        $paymentPercentage = 0;
                    }

                    //Sacar monto respecto del porcentaje
                    $paymentValue = $thisPeriodData->quantitiesMonto * $paymentPercentage / 100;

                    //Si no tiene descuento, es el mismo valor, sino, el total es aplicando el dcto
                    if ($contractDistribution->contractDistribution_discount == 0) {
                        $paymentTotalValue = $paymentValue;
                    } else {
                        $paymentTotalValue = $paymentValue * (1 - $contractDistribution->contractDistribution_discount / 100);
                    }

                    //Sacar monto con IVA
                    $paymentTotalTaxValue = $paymentTotalValue * ($tributaryDocument->tributarydocuments_tax / 100 + 1);


                    //Crear el detalle tributario
                    $newTributaryDetail = new Tributarydetails([
                        'idTributarydocument' => $idTributarydocument,
                        'idClient' => $contractDistribution->idClient,
                        'idPaymentUnit' => $thisPeriodData->idPaymentUnit,
                        'idModule' => $thisPeriodData->idModule,
                        'tributarydetails_paymentUnitQuantity' => $paymentQuantity,
                        'tributarydetails_paymentPercentage' => $paymentPercentage,
                        'tributarydetails_paymentValue' => $paymentValue,
                        'tributarydetails_discount' => $contractDistribution->contractDistribution_discount,
                        'tributarydetails_paymentTotalValue' => $paymentTotalValue,
                        'tributarydetails_paymentTotalTaxValue' => $paymentTotalTaxValue,
                      ]);
                      $newTributaryDetail->save();
                }
            }
        }
    }

    public function getPermisos() {
      $userId = Auth::user()->id;
      $authPermisos = Permission::where('idUser', $userId)->get();
      $authPermisos = $authPermisos->pluck('idActions')->toArray();
      return $authPermisos;
  }
}
