@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Detalles de la factura del contrato: <strong>{{$contract['contractsNombre']}}</strong> de número: <strong>{{$contract['contractsNumero']}}</strong>
            </div>

        </div>
        <form method="POST" action="{{route('billings.paymentDetailsUpdate', $tributaryDocument['id'])}}">
            @csrf
            {{ method_field('PUT') }}
            <div class="table-responsive">
                <table id="tablaPaymentDetails" class="table table-hover w-auto text-nowrap" data-show-export="true"
                    data-click-to-select="true" data-show-columns="true" data-sortable="true"
                    data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right">
                    <thead>
                        <tr>
                            <th scope="col" data-field="ID" data-sortable="true">ID</th>
                            <th scope="col" data-field="idClient" data-sortable="true">Razón social</th>
                            <th scope="col" data-field="idPaymentUnit" data-sortable="true">Unidad de pago</th>
                            <th scope="col" data-field="tributarydetails_paymentUnitQuantity" data-sortable="true">Cantidad de unidades de pago</th>
                            <th scope="col" data-field="tributarydetails_paymentPercentage" data-sortable="true">Porcentaje</th>
                            <th scope="col" data-field="tributarydetails_paymentValue" data-sortable="true">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tributaryDetails as $tributaryDetail)
                        <tr>
                            <td>{{$tributaryDetail['id']}}</td>
                            <td>{{$tributaryDetail['clientRazonSocial']}}</td>
                            <td>{{$tributaryDetail['payment_units']}}</td>
                            <td>{{$tributaryDetail['tributarydetails_paymentUnitQuantity']}}</td>
                            <td>{{$tributaryDetail['tributarydetails_paymentPercentage']}}</td>
                            <td>{{$tributaryDetail['tributarydetails_paymentValue']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <input type="hidden" id="paymentDetailsTableLength" name="paymentDetailsTableLength" required value="0">
            <div class="form-group row">

            <div class="form-group row">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaPaymentDetails').bootstrapTable({
        pageSize: 100,
        exportDataType: 'all',
    });
    const paymentDetailsTableLength = $('#tablaPaymentDetails').bootstrapTable('getData').length;
    document.getElementById('paymentDetailsTableLength').value = paymentDetailsTableLength;
</script>
@endsection
