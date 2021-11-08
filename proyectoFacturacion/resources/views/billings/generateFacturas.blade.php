@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Generando facturas del contrato: <strong>{{$contract['contractsNombre']}}</strong> de número:
                <strong>{{$contract['contractsNumero']}}</strong>
            </div>
            <div class="alert alert-info" role="alert">
                Monto neto a facturar: <strong>{{$tributaryDocument['tributarydocuments_totalAmount']}} UF</strong>
                <br>
                <input type="hidden" id="totalFacturar" value="{{$tributaryDocument['tributarydocuments_totalAmountTax']}}">
                Monto total a facturar: <strong>{{$tributaryDocument['tributarydocuments_totalAmountTax']}} UF</strong>
            </div>

            <a href="{{ route('billings.createProjectCurrentContract', $tributaryDocument['id']) }}"><input type="button" value="Agregar proyecto" class="btn btn-secondary" id="agregarProyecto"></a>
            <input type="button" value="Agregar fila" class="btn btn-primary" id="agregarFactura">
        </div>

        <form method="POST" action="{{route('billings.generateFacturacion', $tributaryDocument['id'])}}" onsubmit="return validateMyForm(this);">
            @csrf
            {{ method_field('PUT') }}
        <input type="hidden" id="netoFacturar" name="netoFacturar" value="{{$tributaryDocument['tributarydocuments_totalAmount']}}">
        <input type="hidden" id="totalFacturar" name="totalFacturar" value="{{$tributaryDocument['tributarydocuments_totalAmountTax']}}">
        <input type="hidden" id="montoFacturado" name="montoFacturado" value="0">

        <div class="table-responsive">
            <table id="tablaFacturas" class="table table-hover w-auto text-nowrap table-striped table-bordered my-3">
                <thead>
                    <tr>
                        <th scope="col" data-field="razonSocial" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="modulo" data-sortable="true">Módulo</th>
                        <th scope="col" data-field="unidadPago" data-sortable="true">Unidad de pago</th>
                        <th scope="col" data-field="proyecto" data-sortable="true">Proyecto</th>

                        <th scope="col" data-field="monto" data-sortable="true">Monto
                        <th scope="col" data-field="porcentaje" data-sortable="true">Porcentaje (%)</th>
                        <th scope="col" data-field="descuento" data-sortable="true">Descuento (%)</th>
                        <th scope="col" data-field="neto" data-sortable="true">Neto</th>
                        <th scope="col" data-field="total" data-sortable="true">Total</th>
                        <th scope="col" data-field="grupo" data-sortable="true">Grupo</th>

                        <th scope="col" data-field="numeroOC" data-sortable="true">Número OC</th>
                        <th scope="col" data-field="fechaOC" data-sortable="true">Fecha OC</th>
                        <th scope="col" data-field="vigenciaOC" data-sortable="true">Vigencia OC</th>

                        <th scope="col" data-field="fechaHES" data-sortable="true">Número HES</th>
                        <th scope="col" data-field="numeroHES" data-sortable="true">Fecha HES</th>
                        <th scope="col" data-field="vigenciaHES" data-sortable="true">Vigencia HES</th>
                        <th scope="col" data-field="eliminar" data-sortable="true">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            {{-- razonesSociales --}}
                            <td>
                                <select id="razonesSociales[{{$invoice['id']}}]" name="razonesSociales[]">
                                    @foreach($razonesSociales as $razonSocial)
                                    <option value="{{$razonSocial['id']}}" @if($invoice['idClient'] == $razonSocial['id']) selected @endif>
                                        {{$razonSocial['clientRazonSocial']}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            {{-- modules --}}
                            <td>
                                <select id="modules[{{$invoice['id']}}]" name="modules[]">
                                    @foreach($modules as $module)
                                    <option value="{{$module['id']}}" @if($invoice['idModule'] == $module['id']) selected @endif>
                                        {{$module['moduleName']}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            {{-- paymentUnits --}}
                            <td>
                                <select id="paymentUnits[{{$invoice['id']}}]" name="paymentUnits[]">
                                    @foreach($paymentUnits as $paymentUnit)
                                    <option value="{{$paymentUnit['id']}}" @if($invoice['idPaymentUnit'] == $paymentUnit['id']) selected @endif>
                                        {{$paymentUnit['payment_units']}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            {{-- contractPaymentDetails --}}
                            <td>
                                <select id="contractPaymentDetails[{{$invoice['id']}}]" name="contractPaymentDetails[]">
                                    <option value="">
                                        Sin proyecto
                                    </option>
                                    @foreach($contractPaymentDetails as $contractPaymentDetail)
                                    <option value="{{$contractPaymentDetail['id']}}" @if($invoice['idContractPaymentDetails'] == $contractPaymentDetail['id']) selected @endif>
                                        {{$contractPaymentDetail['contractPaymentDetails_description']}}
                                    </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- monto --}}
                            <td>
                                <input id="monto_old_{{$invoice['id']}}" type="number" name="monto[]"
                                value="{{$invoice['invoices_monto']}}" onchange="validateFactura(this)">
                            </td>
                            {{-- porcentaje --}}
                            <td>
                                <input id="porcentaje_old_{{$invoice['id']}}" type="number" name="porcentaje[]" step="0.01"
                                value="{{$invoice['invoices_porcentaje']}}" onchange="validateFactura(this)">
                            </td>
                            {{-- descuento --}}
                            <td>
                                <input id="descuento_old_{{$invoice['id']}}" type="number" name="descuento[]" step="0.01"
                                value="{{$invoice['invoices_descuento']}}" onchange="validateFactura(this)">
                            </td>
                            {{-- neto --}}
                            <td>
                                <input id="neto_old_{{$invoice['id']}}" type="number" name="neto[]"
                                value="{{$invoice['invoices_neto']}}" onchange="validateFactura(this)" readonly>
                            </td>
                            {{-- total --}}
                            <td>
                                <input id="total_old_{{$invoice['id']}}" type="number" name="total[]"
                                value="{{$invoice['invoices_total']}}" onchange="validateFactura(this)" readonly>
                            </td>
                            {{-- grupo --}}
                            <td>
                                <input id="grupo{{$invoice['id']}}" type="number" name="grupo[]"
                                value="{{$invoice['invoices_grupo']}}">
                            </td>

                            {{-- numeroOC --}}
                            <td>
                                <input type="text" name="numeroOC[]"
                                value="{{$invoice['invoices_numeroOC']}}">
                            </td>
                            {{-- fechaOC --}}
                            <td>
                                <input type="date" name="fechaOC[]"
                                value="{{$invoice['invoices_fechaOC']}}">
                            </td>
                            {{-- vigenciaOC --}}
                            <td>
                                <input type="date" name="vigenciaOC[]"
                                value="{{$invoice['invoices_vigenciaOC']}}">
                            </td>

                            {{-- numeroHES --}}
                            <td>
                                <input type="text" name="numeroHES[]"
                                value="{{$invoice['invoices_numeroHES']}}">
                            </td>
                            {{-- fechaHES --}}
                            <td>
                                <input type="date" name="fechaHES[]"
                                value="{{$invoice['invoices_fechaHES']}}">
                            </td>
                            {{-- vigenciaHES --}}
                            <td>
                                <input type="date" name="vigenciaHES[]"
                                value="{{$invoice['invoices_vigenciaHES']}}">
                            </td>

                            {{-- Eliminar --}}
                            <td>
                                <input type="button" name="eliminar[]" value="Eliminar fila" onclick="deleteRow(this)">
                                <input type="hidden" name="id[]" value="{{$invoice['id']}}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-group row my-3">
            <div class="col-md-3">
                <input type="hidden" id="largoTabla" name="largoTabla" value="0">
                <button type="submit" class="btn btn-primary">
                    Generar facturas
                </button>
            </div>
        </div>


        </form>

    </div>
</div>
<script>
    const contractPaymentDetails = {!! json_encode($contractPaymentDetails) !!};
    const modules = {!! json_encode($modules) !!};
    const paymentUnits = {!! json_encode($paymentUnits) !!};
    const razonesSociales = {!! json_encode($razonesSociales) !!};
</script>

<script src="{{ asset('js/components/generateFacturas.js')}}"></script>
@endsection
