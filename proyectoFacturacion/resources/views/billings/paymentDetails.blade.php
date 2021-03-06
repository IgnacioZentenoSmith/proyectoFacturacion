@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                <p>Detalles de la factura del contrato: <strong>{{$contract['contractsNombre']}}</strong> de número:
                <strong>{{$contract['contractsNumero']}}</strong></p>
                <p class="my-1">Total: <strong>{{$tributaryDocument['tributarydocuments_totalAmount']}} UF</strong> Neto: <strong>{{$tributaryDocument['tributarydocuments_totalAmountTax']}} UF</strong></p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaTributaryDetails" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
                data-live-search="true" data-buttons-align="left" data-search-align="right">
                <thead>
                    <tr>
                        <th scope="col" data-field="idClient" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="idModule" data-sortable="true">Módulo</th>
                        <th scope="col" data-field="idPaymentUnit" data-sortable="true">Unidad de pago</th>
                        <th scope="col" data-field="tributarydetails_paymentUnitQuantity" data-sortable="true">Cantidad</th>
                        <th scope="col" data-field="tributarydetails_paymentPercentage" data-sortable="true">Porcentaje
                        </th>
                        <th scope="col" data-field="tributarydetails_paymentValue" data-sortable="true">Subtotal</th>
                        <th scope="col" data-field="tributarydetails_discount" data-sortable="true">Descuento</th>
                        <th scope="col" data-field="tributarydetails_paymentTotalValue" data-sortable="true">Neto</th>
                        <th scope="col" data-field="tributarydetails_paymentTotalTaxValue" data-sortable="true">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tributaryDetails as $tributaryDetail)
                    <tr>
                        <td>{{$tributaryDetail['clientRazonSocial']}}</td>
                        <td>{{$tributaryDetail['moduleName']}}</td>
                        <td>{{$tributaryDetail['payment_units']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_paymentPercentage']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_paymentValue']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_discount']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_paymentTotalValue']}}</td>
                        <td>{{$tributaryDetail['tributarydetails_paymentTotalTaxValue']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <br>

        <div class="table-responsive">
            <table id="tablaPaymentDetails" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
                data-live-search="true" data-buttons-align="left" data-search-align="right">
                <thead>
                    <tr>
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
                        <td>{{$contractPaymentDetail['clientRazonSocial']}}</td>
                        <td>{{$contractPaymentDetail['payment_units']}}</td>
                        <td>{{$contractPaymentDetail['ccontractPaymentDetails_quantity']}}</td>
                        <td>{{$contractPaymentDetail['contractPaymentDetails_description']}}</td>
                        <td>{{$contractPaymentDetail['contractPaymentDetails_recepcionMunicipal']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="{{ asset('js/components/initBTtables.js')}}"></script>
@endsection
