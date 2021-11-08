<?php

namespace App\Http\Controllers;


use App\Jobs\SendNotifications;

use App\Permission;
use App\ContractConditions;
use App\ContractDistribution;
use App\ContractPaymentDetails;
use App\Binnacle;
use App\Invoices;

use App\Contracts;
use App\Client;
use App\Modules;
use App\PaymentUnits;
use App\User;
use App\Quantities;
use App\Tributarydocuments;
use App\Tributarydetails;
use Auth;
use Carbon\Cli\Invoker;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
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

    public function generateFacturas(Request $request, $idTributarydocument) {
        $authPermisos = $this->getPermisos();
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);
        $invoices = Invoices::where('idTributarydocument', $tributaryDocument->id)->get();

        $tributaryDetails = Tributarydetails::where('idTributarydocument', $tributaryDocument->id)
        ->join('payment_units', 'payment_units.id', '=', 'tributarydetails.idPaymentUnit')
        ->join('modules', 'modules.id', '=', 'tributarydetails.idModule')
        ->join('clients', 'clients.id', '=', 'tributarydetails.idClient')
        ->select('tributarydetails.*', 'clients.clientRazonSocial', 'clients.clientRUT', 'payment_units.payment_units', 'modules.moduleName')
        ->get();

        $contractPaymentDetails = ContractPaymentDetails::where('idContract', $contract->id)
        ->where('contractPaymentDetails_period', $tributaryDocument->tributarydocuments_period)
        ->get();

        $modules = ContractConditions::where('idContract', $contract->id)
        ->join('modules', 'modules.id', '=', 'idModule')
        ->select('modules.*')
        ->distinct()
        ->get();

        $paymentUnits = ContractConditions::where('idContract', $contract->id)
        ->join('payment_units', 'payment_units.id', '=', 'idPaymentUnit')
        ->select('payment_units.*')
        ->distinct()
        ->get();

        $razonesSociales = Client::where('clientParentId', $contract->idClient)->get();
        return view('billings.generateFacturas', compact('authPermisos', 'invoices', 'modules', 'paymentUnits', 'tributaryDocument', 'contract', 'tributaryDetails', 'contractPaymentDetails', 'razonesSociales'));

    }

    /*
        Datos
        idClient --> razon social
        idModule --> modulo
        idPaymentUnit --> unidad de pago
        idContractPaymentDetails --> proyecto
        idTributaryDocument --> factura --> contrato + periodo + monto total

        tax -> iva
        totalAmountTax -> monto total con iva

        razonesSociales
        modules
        paymentUnits
        contractPaymentDetails

        monto
        porcentaje
        descuento
        neto
        total
        grupo

        numeroOC
        fechaOC
        vigenciaOC
        numeroHES
        fechaHES
        vigenciaHES
    */
    public function generateFacturacion(Request $request, $idTributarydocument) {
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $largoTabla = $request->largoTabla;
        $invoices = Invoices::where('idTributaryDocument', $idTributarydocument)->get();
        if ($largoTabla == 0) {
            foreach ($invoices as $invoice) {
                $invoice->delete();
            }
            return redirect()->action('InvoiceController@generateFacturas', ['idTributarydocument' => $idTributarydocument])->with('success', 'Facturas eliminadas exitosamente.');
        } else {

            $montoTotal = $tributaryDocument['tributarydocuments_totalAmount'];
            $request->validate([
                'montoFacturado' => 'required|numeric|in:' . $montoTotal,
                'razonesSociales' => 'required|array|min:' . $largoTabla,
                'razonesSociales.*' => 'required|numeric|min:0',
                'modules'=> 'required|array|min:' . $largoTabla,
                'modules.*'=> 'required|numeric|min:0',
                'paymentUnits'=> 'required|array|min:' . $largoTabla,
                'paymentUnits.*'=> 'required|numeric|min:0',
                'contractPaymentDetails'=> 'required|array|min:' . $largoTabla,
                'contractPaymentDetails.*'=> 'required|numeric|min:0',

                'monto'=> 'required|array|min:' . $largoTabla,
                'monto.*'=> 'required|numeric|between:0,' . $tributaryDocument->tributarydocuments_totalAmount,
                'porcentaje'=> 'required|array|min:' . $largoTabla,
                'porcentaje.*'=> 'required|numeric|between:0,100',
                'descuento'=> 'required|array|min:' . $largoTabla,
                'descuento.*'=> 'nullable|numeric|between:0,100',
                'neto'=> 'required|array|min:' . $largoTabla,
                'neto.*'=> 'required|numeric|between:0,' . $tributaryDocument->tributarydocuments_totalAmount,
                'total'=> 'required|array|min:' . $largoTabla,
                'total.*'=> 'required|numeric|between:0,' . $tributaryDocument->tributarydocuments_totalAmountTax,
                'grupo'=> 'required|array|min:' . $largoTabla,
                'grupo.*'=> 'required|numeric|min:1',

                'numeroOC'=> 'required|array|min:' . $largoTabla,
                'numeroOC.*'=> 'nullable|string|max:100',
                'fechaOC'=> 'required|array|min:' . $largoTabla,
                'fechaOC.*'=> 'nullable|date_format:Y-m-d',
                'vigenciaOC'=> 'required|array|min:' . $largoTabla,
                'vigenciaOC.*'=> 'nullable|date_format:Y-m-d',

                'numeroHES'=> 'required|array|min:' . $largoTabla,
                'numeroHES.*'=> 'nullable|string|max:100',
                'fechaHES'=> 'required|array|min:' . $largoTabla,
                'fechaHES.*'=> 'nullable|date_format:Y-m-d',
                'vigenciaHES'=> 'required|array|min:' . $largoTabla,
                'vigenciaHES.*'=> 'nullable|date_format:Y-m-d',

                'id' => 'required|array|min:' . $largoTabla,
                'id.*' => 'required|numeric|min:0',
            ]);

            // Verificar si hay que eliminar alguna factura

            if ($invoices->count() > $largoTabla) {
                // Obtener las facturas que se borraron
                $deletedInvoices = $invoices->whereNotIn('id', $request->id);
                // Eliminarlas
                foreach ($deletedInvoices as $deletedInvoice) {
                    $deletedInvoice->delete();
                }
            }

            for ($i = 0; $i < $largoTabla; $i++ ) {
                // Si el id es 0, entonces es una factura nueva
                if ($request->id[$i] == 0) {
                    $newInvoice = new Invoices([
                        'idTributaryDocument' => $idTributarydocument,
                        'idClient' => $request->razonesSociales[$i],
                        'idModule' => $request->modules[$i],
                        'idPaymentUnit' => $request->paymentUnits[$i],
                        'idContractPaymentDetails' => $request->contractPaymentDetails[$i],

                        'invoices_monto' => $request->monto[$i],
                        'invoices_porcentaje' => $request->porcentaje[$i],
                        'invoices_descuento' => $request->descuento[$i],
                        'invoices_neto' => $request->neto[$i],
                        'invoices_total' => $request->total[$i],
                        'invoices_grupo' => $request->grupo[$i],

                        'invoices_numeroOC' => $request->numeroOC[$i],
                        'invoices_fechaOC' => $request->fechaOC[$i],
                        'invoices_vigenciaOC' => $request->vigenciaOC[$i],

                        'invoices_numeroHES' => $request->numeroHES[$i],
                        'invoices_fechaHES' => $request->fechaHES[$i],
                        'invoices_vigenciaHES' => $request->vigenciaHES[$i],
                    ]);
                    $newInvoice->save();
                }
                // Si el id no es 0, es una factura ya existente
                else {
                    // Encontrar la factura asociada al id
                    $invoice = Invoices::find($request->id[$i]);

                    $invoice->idTributaryDocument = $tributaryDocument->id;
                    $invoice->idClient = $request->razonesSociales[$i];
                    $invoice->idModule = $request->modules[$i];
                    $invoice->idPaymentUnit = $request->paymentUnits[$i];
                    $invoice->idContractPaymentDetails = $request->contractPaymentDetails[$i];

                    $invoice->invoices_monto = $request->monto[$i];
                    $invoice->invoices_porcentaje = $request->porcentaje[$i];
                    $invoice->invoices_descuento = $request->descuento[$i];
                    $invoice->invoices_neto = $request->neto[$i];
                    $invoice->invoices_total = $request->total[$i];
                    $invoice->invoices_grupo = $request->grupo[$i];

                    $invoice->invoices_numeroOC = $request->numeroOC[$i];
                    $invoice->invoices_fechaOC = $request->fechaOC[$i];
                    $invoice->invoices_vigenciaOC = $request->vigenciaOC[$i];

                    $invoice->invoices_numeroHES = $request->numeroHES[$i];
                    $invoice->invoices_fechaHES = $request->fechaHES[$i];
                    $invoice->invoices_vigenciaHES = $request->vigenciaHES[$i];

                    // Si ha habido algun cambio, guardar
                    if ($invoice->isDirty()) {
                        $invoice->save();
                    }
                }
            }

            return redirect()->action('TributarydocumentsController@index', ['periodo' => $tributaryDocument->tributarydocuments_period])->with('success', 'Facturas generadas exitosamente.');
        }
    }

    public function createProjectCurrentContract($idTributarydocument) {
        $authPermisos = $this->getPermisos();
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);

        return view('billings.createProjectCurrentContract', compact('authPermisos', 'tributaryDocument', 'contract'));
    }

    public function storeProjectCurrentContract(Request $request, $idTributarydocument) {
        $request->validate([
            'contractPaymentDetails_description' => 'required|string|max:255',
            'contractPaymentDetails_glosaProyecto' => 'required|string|max:255',
            'contractPaymentDetails_units' => 'required|numeric|min:0',
            'contractPaymentDetails_recepcionMunicipal' => 'date|nullable',
        ]);
        $tributaryDocument = Tributarydocuments::find($idTributarydocument);
        $contract = Contracts::find($tributaryDocument->idContract);
        $client = Client::find($tributaryDocument->idClient);
        $idPaymentUnit = 2;

        $newContractPaymentDetail = new ContractPaymentDetails([
            'idClient' => $client->id,
            'idContract' => $contract->id,
            'idPaymentUnit' => $idPaymentUnit,

            'contractPaymentDetails_period' => $tributaryDocument->tributarydocuments_period,
            'ccontractPaymentDetails_quantity' => 1,
            'contractPaymentDetails_description' => $request->contractPaymentDetails_description,
            'contractPaymentDetails_recepcionMunicipal' => $request->contractPaymentDetails_recepcionMunicipal,
            'contractPaymentDetails_units' => $request->contractPaymentDetails_units,
            'contractPaymentDetails_glosaProyecto' => $request->contractPaymentDetails_glosaProyecto,
        ]);
        $newContractPaymentDetail->save();

        return redirect()->action('InvoiceController@generateFacturas', ['idTributarydocument' => $idTributarydocument])->with('success', 'Proyecto generado exitosamente.');
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
}
