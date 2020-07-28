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
                        <th scope="col" data-field="tributarydetails_paymentUnitQuantity" data-sortable="true">Cantidad
                            de unidades de pago</th>
                        <th scope="col" data-field="tributarydetails_paymentPercentage" data-sortable="true">Porcentaje
                        </th>
                        <th scope="col" data-field="tributarydetails_paymentValue" data-sortable="true">Monto</th>
                        <th scope="col" data-field="tributarydetails_discount" data-sortable="true">Descuento</th>
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
                            {{$tributaryDetail['payment_units']}}
                                <input type="hidden" name="idPaymentUnit[]" required
                                value="{{$tributaryDetail['idPaymentUnit']}}">
                        </td>

                        <td>
                            {{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}
                                <input type="hidden" name="tributarydetails_paymentUnitQuantity[]" required
                                value="{{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentPercentage[{{$tributaryDetail['id']}}]" type="number" onchange="getPercentage(this);"
                            class="form-control" name="tributarydetails_paymentPercentage[]" step="0.01"
                            value="{{$tributaryDetail['tributarydetails_paymentPercentage']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_paymentValue[{{$tributaryDetail['id']}}]" type="number" onchange="getValue(this);"
                            class="form-control" name="tributarydetails_paymentValue[]" step="0.001"
                            value="{{$tributaryDetail['tributarydetails_paymentValue']}}">
                        </td>

                        <td>
                            <input id="tributarydetails_discount[{{$tributaryDetail['id']}}]" type="number" onchange="getDiscount(this);"
                            class="form-control" name="tributarydetails_discount[]" step="0.001"
                            value="{{$tributaryDetail['tributarydetails_discount']}}">
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
                    class="form-control" name="montoTotal" step="0.01" readonly
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
                        <td>{{$contractPaymentDetail['id']}}</td>
                        <td>
                            <select class="form-control" id="clientRazonSocial[{{$contractPaymentDetail['id']}}]" name="clientRazonSocial[]">
                                @foreach($razonesSociales as $razonSocial)
                                <option value="{{$razonSocial['clientRazonSocial']}}" @if($contractPaymentDetail['clientRazonSocial'] == $razonSocial['clientRazonSocial']) selected @endif>
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

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaTributaryDetails').bootstrapTable({
        pageSize: 100,
        exportDataType: 'all',
    });

    $('#tablaPaymentDetails').bootstrapTable({
        pageSize: 100,
        exportDataType: 'all',
    });

    const tributaryDetailsTableLength = $('#tablaTributaryDetails').bootstrapTable('getData').length;
    document.getElementById('tributaryDetailsTableLength').value = tributaryDetailsTableLength;


    function getPercentage(percentageInput) {
        //Entradas no validas
        if (parseFloat(percentageInput.value) > 100) {
            percentageInput.value = parseFloat(0);
        }
        else if (parseFloat(percentageInput.value) < 0) {
            percentageInput.value = parseFloat(0);
        }
        //Entradas validas
        else {
            let inputId = getInputId(percentageInput);
            //Input del valor asociado al porcentaje
            let valueInput = document.getElementById('tributarydetails_paymentValue[' + inputId + ']');
            //Si el total supera 100
            if (getTotalPorcentaje() > 100) {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            //Si el total es menor a 0
            else if (getTotalPorcentaje() < 0) {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            else if (percentageInput.value == '') {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            //Si esta todo OK
            else {
                let montoTotal = document.getElementById('montoTotal').value;
                let value = parseFloat(percentageInput.value)/100 * parseFloat(montoTotal);
                valueInput.value = parseFloat(value).toFixed(3);
            }
        }
        getTotalPorcentaje();
        getTotalValue();
    }

    function getValue(valueInput) {
        //Entradas no validas
        let montoTotal = document.getElementById('montoTotal').value;
        if (parseFloat(valueInput.value) > parseFloat(montoTotal)) {
            valueInput.value = parseFloat(0);
        }
        else if (parseFloat(valueInput.value) < 0) {
            valueInput.value = parseFloat(0);
        }
        //Entradas validas
        else {
            let inputId = getInputId(valueInput);
            //Input del porcentaje asociado al valor
            let percentageInput = document.getElementById('tributarydetails_paymentPercentage[' + inputId + ']');
            //Verificar el total
            //Si el total supera el monto total
            if (getTotalValue() > parseFloat(montoTotal)) {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            //Si el total es menor a 0
            else if (getTotalValue() < 0) {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            else if (valueInput.value == '') {
                percentageInput.value = parseFloat(0);
                valueInput.value = parseFloat(0);
            }
            //Si esta todo OK
            else {
                let percentage = parseFloat(valueInput.value) * 100 / parseFloat(montoTotal);
                percentageInput.value = parseFloat(percentage).toFixed(2);
            }
        }
        getTotalPorcentaje();
        getTotalValue();
    }

    function getTotalPorcentaje() {
        let totalPercentage = 0;
        document.getElementsByName('tributarydetails_paymentPercentage[]').forEach(input => {
                totalPercentage += parseFloat(input.value);
            });
        document.getElementById('porcentajeActual').value = parseFloat(totalPercentage).toFixed(2);
        return totalPercentage;
    }

    function getTotalValue() {
        let totalValue = 0;
        document.getElementsByName('tributarydetails_paymentValue[]').forEach(input => {
                totalValue += parseFloat(input.value);
            });
        document.getElementById('montoActual').value = parseFloat(totalValue).toFixed(2);
        return totalValue;
    }

    function getInputId(inputElement) {
        //Regex
        let inputId = inputElement.id.match(/(\d+)/);
        return inputId[0];
    }

    function getDiscount(discountInput) {
        if (parseFloat(discountInput.value) > 100) {
            discountInput.value = parseFloat(0);
        }
        else if (parseFloat(discountInput.value) < 0) {
            discountInput.value = parseFloat(0);
        }
    }

    getTotalPorcentaje();
    getTotalValue();

</script>
@endsection
