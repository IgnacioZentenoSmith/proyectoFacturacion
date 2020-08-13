@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Modifique la factura del contrato: <strong>{{$contract['contractsNombre']}}</strong> de número:
                <strong>{{$contract['contractsNumero']}}</strong> para realizar la nota de crédito
            </div>
        </div>

        <form method="POST" action="{{route('billings.generateRedistribucion', $tributaryDocument['id'])}}">
            @csrf
            {{ method_field('PUT') }}

        <div class="table-responsive">
            <table id="tablaTributaryDetails" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
                data-live-search="true" data-buttons-align="left" data-search-align="right">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="idClient" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="idPaymentUnit" data-sortable="true">Unidad de pago</th>
                        <th scope="col" data-field="tributarydetails_paymentUnitQuantity" data-sortable="true">Cantidad</th>
                        <th scope="col" data-field="tributarydetails_paymentPercentage" data-sortable="true">Porcentaje
                        </th>
                        <th scope="col" data-field="tributarydetails_paymentValue" data-sortable="true">Subtotal</th>
                        <th scope="col" data-field="tributarydetails_discount" data-sortable="true">Descuento</th>
                        <th scope="col" data-field="tributarydetails_paymentTotalValue" data-sortable="true">Total</th>
                        <th scope="col" data-field="tributarydetails_paymentTotalTaxValue" data-sortable="true">Neto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tributaryDetails as $tributaryDetail)
                    <tr>
                        <td>
                            {{$tributaryDetail['id']}}
                                <input type="hidden" name="tributaryDetail_id[]" required
                                value="{{$tributaryDetail['id']}}">
                        </td>

                        <td>
                            {{$tributaryDetail['clientRazonSocial']}}
                                <input type="hidden" name="idClient[]" required
                                value="{{$tributaryDetail['idClient']}}">
                        </td>

                        <td>
                            {{$tributaryDetail['payment_units']}} {{$tributaryDetail['moduleName']}}
                                <input type="hidden" name="idPaymentUnit[]" required
                                value="{{$tributaryDetail['idPaymentUnit']}}">
                        </td>

                        <td>
                            {{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}
                                <input type="hidden" name="tributarydetails_paymentUnitQuantity[]" required
                                value="{{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentPercentage[{{$tributaryDetail['id']}}]" type="number"
                            class="form-control" name="tributarydetails_paymentPercentage[]" step="0.01"
                            value="{{$tributaryDetail['tributarydetails_paymentPercentage']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentValue[{{$tributaryDetail['id']}}]" type="number"
                            class="form-control" name="tributarydetails_paymentValue[]" step="0.001"
                            value="{{$tributaryDetail['tributarydetails_paymentValue']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_discount[{{$tributaryDetail['id']}}]" type="number"
                            class="form-control" name="tributarydetails_discount[]" step="0.001"
                            value="{{$tributaryDetail['tributarydetails_discount']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentTotalValue[{{$tributaryDetail['id']}}]" type="number"
                            class="form-control" name="tributarydetails_paymentTotalValue[]" step="0.001"
                            value="{{$tributaryDetail['tributarydetails_paymentTotalValue']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentTotalTaxValue[{{$tributaryDetail['id']}}]" type="number"
                            class="form-control" name="tributarydetails_paymentTotalTaxValue[]" step="0.001" readonly
                            value="{{$tributaryDetail['tributarydetails_paymentTotalTaxValue']}}">
                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <div class="form-group row" id="totalPercentage">
            <label for="porcentajeActual" class="col-auto col-form-label">Porcentaje actual</label>

            <div class="col-auto">
                <input id="porcentajeActual" type="number"
                    class="form-control" name="porcentajeActual" step="0.01" readonly
                    value="0">
            </div>

            <label for="montoActual" class="col-auto col-form-label">Monto actual</label>

            <div class="col-auto">
                <input id="montoActual" type="number"
                    class="form-control" name="montoActual" step="0.01" readonly
                    value="0">
            </div>

            <label for="montoTotal" class="col-auto col-form-label">Monto total</label>

            <div class="col-auto">
                <input id="montoTotal" type="number"
                    class="form-control" name="montoTotal" step="0.01" @if (!$contract['contractsManualContract']) readonly @endif
                    value="{{$tributaryDocument['tributarydocuments_totalAmountTax']}}">
            </div>
        </div>

        <br>

        <div class="table-responsive">
            <table id="tablaPaymentDetails" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
                data-live-search="true" data-buttons-align="left" data-search-align="right">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="idClient" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="idPaymentUnit" data-sortable="true">Unidad de pago</th>
                        <th scope="col" data-field="ccontractPaymentDetails_quantity" data-sortable="true">Cantidad
                            de unidades de pago</th>
                        <th scope="col" data-field="contractPaymentDetails_description" data-sortable="true">Descripción
                        </th>
                        <th scope="col" data-field="contractPaymentDetails_recepcionMunicipal" data-sortable="true">Recepción municipal
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contractPaymentDetails as $contractPaymentDetail)
                    <tr>
                        <td>{{$contractPaymentDetail['id']}}
                            <input type="hidden" name="contractPaymentDetail_id[]" required
                            value="{{$contractPaymentDetail['id']}}">
                        </td>
                        <td>
                            <select class="form-control" id="contractPaymentDetail_idClient[{{$contractPaymentDetail['id']}}]" name="contractPaymentDetail_idClient[]">
                                @foreach($razonesSociales as $razonSocial)
                                <option value="{{$razonSocial['id']}}" @if($contractPaymentDetail['idClient'] == $razonSocial['id']) selected @endif>
                                    {{$razonSocial['clientRazonSocial']}}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{$contractPaymentDetail['payment_units']}}</td>
                        <td>{{$contractPaymentDetail['ccontractPaymentDetails_quantity']}}</td>
                        <td>{{$contractPaymentDetail['contractPaymentDetails_description']}}</td>
                        <td>{{$contractPaymentDetail['contractPaymentDetails_recepcionMunicipal']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <br>
        <input type="hidden" id="tributaryDetailsTableLength" name="tributaryDetailsTableLength" required value="0">
        <input type="hidden" id="contractPaymentDetailsTableLength" name="contractPaymentDetailsTableLength" required value="0">

        <div class="form-group row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    Generar redistribución
                </button>
            </div>
        </div>


        </form>

    </div>
</div>

<script src="{{ asset('js/components/redistribute.js')}}"></script>
@endsection
