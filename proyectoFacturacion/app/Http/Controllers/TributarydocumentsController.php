<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractConditions;
use App\ContractInvolvedUsers;
use App\Contracts;
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
          $montoTotalIva = $documentoTributario['tributarydocuments_totalAmount'] * 1.19;
          $documentoTributario = Arr::add($documentoTributario, 'documentoTributario_MontoTotalIVA', $montoTotalIva);
        }
        return view('billings.index', compact('authPermisos', 'periodo', 'documentosTributarios'));
    }

    public function generateDocumentos($periodo, $tipoDocumento) {
      $authPermisos = $this->getPermisos();
      //HACER FACTURAS
      if ($tipoDocumento === 'Factura') {
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
            //Generar el documento
            $newTributaryDocument = new Tributarydocuments([
              'idContract' => $uniqueContract,
              'tributarydocuments_period' => $periodo,
              'tributarydocuments_documentType' => $tipoDocumento,
              'tributarydocuments_totalAmount' => $totalSuma,
              'tributarydocuments_tax' => 19,
              'tributarydocuments_totalAmountTax' => $totalSuma * 1.19
            ]);
            $newTributaryDocument->save();
          }
        }

        return redirect()->action('TributarydocumentsController@index', ['periodo' => $periodo])->with('success', 'Facturas generadas exitosamente');
      }
      //HACER NOTAS DE CREDITO
      else if ($tipoDocumento === 'NotaCredito') {

      }
      //ERROR
      else {

      }

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
      $documentoTributario->delete();
      return redirect()->action('TributarydocumentsController@index', ['periodo' => 0])->with('success', 'Documento eliminado exitosamente');
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
        $quantities = Quantities::where('idContractCondition', $condicionContractual->id)
            ->where('quantitiesPeriodo', $periodo)
            ->whereNotNull('quantitiesMonto')
    }

    public function paymentDetailsUpdate(Request $request, $idTributarydocument) {

    }

    public function getPermisos() {
      $userId = Auth::user()->id;
      $authPermisos = Permission::where('idUser', $userId)->get();
      $authPermisos = $authPermisos->pluck('idActions')->toArray();
      return $authPermisos;
  }
}
