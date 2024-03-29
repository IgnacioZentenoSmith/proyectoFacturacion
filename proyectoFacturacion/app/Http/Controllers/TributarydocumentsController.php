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
use App\Invoices;

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
          //Formatea tributarydocuments_totalAmount y tributarydocuments_totalAmountTax
          $documentoTributario['tributarydocuments_totalAmount'] = $this->formatNumber($documentoTributario['tributarydocuments_totalAmount']);
          $documentoTributario['tributarydocuments_totalAmountTax'] = $this->formatNumber($documentoTributario['tributarydocuments_totalAmountTax']);

          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_MontoTotalIVA', $montoTotalIva);
        }
        return view('billings.index', compact('authPermisos', 'periodo', 'documentosTributarios'));
    }

    public function generateDocumentos($periodo) {
      $authPermisos = $this->getPermisos();
      app('App\Http\Controllers\ApiquantitiesController')->apiQuantities($periodo);
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
          $condicionesContractuales2 = $condicionesContractuales->whereNotNull('quantitiesMonto');
          $uniqueContracts = $condicionesContractuales2->unique('idContract');

          $uniqueContracts = $uniqueContracts->pluck('idContract');

          //Para cada contrato unico, generar factura con su monto
          foreach ($uniqueContracts as $uniqueContract) {
            $totalSuma = 0;
            $contract = Contracts::find($uniqueContract);

            foreach ($condicionesContractuales2 as $condicionContractual) {
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
            $this->createInvoices($newTributaryDocument);
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
      $this->destroyDocumentQuantities($contract, $periodo);
      $this->setNullContractPaymentDetailsUnits($contract, $periodo);
      app('App\Http\Controllers\BinnacleController')->reportBinnacle('DELETE', $documentoTributario->getTable(), $contract->contractsNombre, $documentoTributario, null);
      SendNotifications::dispatch('Facturas, ' . $contract->contractsNombre, 'Eliminación de factura')->onQueue('emails');
      $documentoTributario->delete();
      return redirect()->action('TributarydocumentsController@index', ['periodo' => $periodo])->with('success', 'Documento eliminado exitosamente');
    }

    //Sacar las cantidades de este contrato $contract en este periodo $periodo
    private function destroyDocumentQuantities($contract, $periodo) {
        //Saca todas las condiciones contractuales de este contrato
        $contractConditions = ContractConditions::where('idContract', $contract->id)->get();
        //Revisa y elimina todas las cantidades de las condiciones contractuales
        //de este contrato en este periodo
        foreach ($contractConditions as $contractCondition) {
            $quantities = Quantities::where('idContractCondition', $contractCondition->id)
            ->where('quantitiesPeriodo', $periodo)
            ->first();
            if ($quantities != null) {
                $quantities->delete();
            }
        }
    }


    private function setNullContractPaymentDetailsUnits($contract, $periodo) {
        //Saca todos los detalles del contrato de este periodo
        $contractPaymentDetails = ContractPaymentDetails::where('contractPaymentDetails_period', $periodo)
        ->where('idContract', $contract->id)
        ->get();
        foreach ($contractPaymentDetails as $contractPaymentDetail) {
            // Poner la unidad en nulo y guardar
            $contractPaymentDetail->idPaymentUnit = null;
            $contractPaymentDetail->save();
        }
    }


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

        #Obtener las unidades de pago que se estan cobrando
        $tributaryDetails_idPaymentUnits = $tributaryDetails->pluck('idPaymentUnit')->unique();

        $contractPaymentDetails = ContractPaymentDetails::where('idContract', $contract->id)
        ->where('contractPaymentDetails_period', $tributaryDocument->tributarydocuments_period)
        #Filtrar por las unidades de pago que se estan cobrando
        ->whereIn('contract_payment_details.idPaymentUnit', $tributaryDetails_idPaymentUnits)
        ->join('payment_units', 'payment_units.id', '=', 'contract_payment_details.idPaymentUnit')
        ->join('clients', 'clients.id', '=', 'contract_payment_details.idClient')
        ->select('contract_payment_details.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units')
        ->get();

        //Formatea valores (de punto a coma) de tributaryDetails
        foreach ($tributaryDetails as $tributaryDetail) {
            $tributaryDetail['tributarydetails_paymentPercentage'] = $this->formatNumber($tributaryDetail['tributarydetails_paymentPercentage']);
            $tributaryDetail['tributarydetails_paymentValue'] = $this->formatNumber($tributaryDetail['tributarydetails_paymentValue']);
            $tributaryDetail['tributarydetails_discount'] = $this->formatNumber($tributaryDetail['tributarydetails_discount']);
            $tributaryDetail['tributarydetails_paymentTotalValue'] = $this->formatNumber($tributaryDetail['tributarydetails_paymentTotalValue']);
            $tributaryDetail['tributarydetails_paymentTotalTaxValue'] = $this->formatNumber($tributaryDetail['tributarydetails_paymentTotalTaxValue']);
        }


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
        ->where('contractPaymentDetails_period', $tributaryDocument->tributarydocuments_period)
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

  public function formatNumber($number) {
    $formattedNumber = preg_replace('/\./', ',', $number);
    return $formattedNumber;
  }

    public function createInvoices($tributaryDocument) {
        // Obtener la fecha del mes pasado respecto del documento
        $thisDocumentDate = Carbon::createFromFormat('Y-m', $tributaryDocument->tributarydocuments_period);
        $lastMonth = $thisDocumentDate->subMonth()->format('Y-m');
        // Obtener el documento del mes pasado
        $lastMonthTributaryDocument = Tributarydocuments::where('idClient', $tributaryDocument->idClient)
        ->where('idContract', $tributaryDocument->idContract)
        ->where('tributarydocuments_period', $lastMonth)
        ->first();
        // Si este documento existe
        if ($lastMonthTributaryDocument != null) {
            // Si el monto del mes pasado es el mismo al de este mes
            if ($lastMonthTributaryDocument->tributarydocuments_totalAmount == $tributaryDocument->tributarydocuments_totalAmount) {
                // Obtener las facturas de este documento
                $lastMonthInvoices = Invoices::where('idTributaryDocument', $lastMonthTributaryDocument->id)->get();
                // Copiar las facturas del mes anterior
                foreach ($lastMonthInvoices as $lastMonthInvoice) {
                    $newInvoice = new Invoices([
                        'idTributaryDocument' => $tributaryDocument->id,
                        'idClient' => $lastMonthInvoice->idClient,
                        'idModule' => $lastMonthInvoice->idModule,
                        'idPaymentUnit' => $lastMonthInvoice->idPaymentUnit,
                        'idContractPaymentDetails' => $lastMonthInvoice->idContractPaymentDetails,

                        'invoices_monto' => $lastMonthInvoice->invoices_monto,
                        'invoices_porcentaje' => $lastMonthInvoice->invoices_porcentaje,
                        'invoices_descuento' => $lastMonthInvoice->invoices_descuento,
                        'invoices_neto' => $lastMonthInvoice->invoices_neto,
                        'invoices_total' => $lastMonthInvoice->invoices_total,
                        'invoices_grupo' => $lastMonthInvoice->invoices_grupo,

                        'invoices_numeroOC' => $lastMonthInvoice->invoices_numeroOC,
                        'invoices_fechaOC' => $lastMonthInvoice->invoices_fechaOC,
                        'invoices_vigenciaOC' => $lastMonthInvoice->invoices_vigenciaOC,

                        'invoices_numeroHES' => $lastMonthInvoice->invoices_numeroHES,
                        'invoices_fechaHES' => $lastMonthInvoice->invoices_fechaHES,
                        'invoices_vigenciaHES' => $lastMonthInvoice->invoices_vigenciaHES,
                    ]);
                    $newInvoice->save();
                }
            }
        }
    }
}
