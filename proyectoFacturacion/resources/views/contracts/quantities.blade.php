@extends('contracts.layout')
@section('contractsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Cantidades de unidades de cobro del contrato: <strong>{{$contract['contractsNumero']}}</strong>
            </div>
            <form method="GET" id="inputPeriodoForm"
                action="{{ route('contracts.quantities', [$contract['id'], $periodo]) }}">
                @csrf
                {{ method_field('GET') }}

                <div class="form-group row">
                    <div class="col-md-3">
                        <input id="inputPeriodo" type="month" class="form-control" name="inputPeriodo" required
                            value="{{$periodo}}" onchange="getCurrentDate(this);">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            Seleccionar período
                        </button>
                    </div>
                </div>

            </form>


        </div>
        <form method="POST" action="{{route('contracts.quantitiesUpdate', [$contract['id'], $periodo])}}">
            @csrf
            {{ method_field('PUT') }}
            <div class="table-responsive">
                <table id="tablaQuantities" class="table table-hover w-auto text-nowrap" data-show-export="true"
                    data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                    data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                    data-server-sort="false">
                    <thead>
                        <tr>
                            <th scope="col" data-field="ID" data-sortable="true">ID</th>
                            <th scope="col" data-field="moduleName" data-sortable="true">Módulo</th>
                            <th scope="col" data-field="paymentUnitName" data-sortable="true">Unidad de pago</th>
                            <th scope="col" data-field="clientName" data-sortable="true">Cliente</th>
                            <th scope="col" data-field="contractNumber" data-sortable="true">Contrato</th>
                            <th scope="col" data-field="quantitiesMonth" data-sortable="true">Mes</th>
                            <th scope="col" data-field="quantitiesYear" data-sortable="true">Año</th>
                            <th scope="col" data-field="contractsConditions_fechaInicio" data-sortable="true">Fecha de
                                inicio</th>
                            <th scope="col" data-field="contractsConditions_fechaTermino" data-sortable="true">Fecha de
                                término</th>

                            <th scope="col" data-field="quantitiesCantidad" data-sortable="true">Cantidad</th>
                            <th scope="col" data-field="quantitiesMonto" data-sortable="true">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contractConditions as $contractCondition)
                        <tr>
                            <td>
                                {{$contractCondition['quantitiesId']}}
                                <input type="hidden" name="quantitiesId[]" required
                                    value="{{$contractCondition['quantitiesId']}}">
                            </td>
                            <td>{{$contractCondition['contractCondition_moduleName']}}</td>
                            <td>{{$contractCondition['contractCondition_paymentUnitName']}}</td>
                            <td>{{$contractCondition['contractCondition_clientName']}}</td>
                            <td>{{$contractCondition['contractCondition_contractName']}}</td>
                            <td>{{$contractCondition['quantitiesMonth']}}</td>
                            <td>{{$contractCondition['quantitiesYear']}}</td>
                            <td>{{$contractCondition['contractsConditions_fechaInicio']}}</td>
                            <td>{{$contractCondition['contractsConditions_fechaTermino']}}</td>

                            <td>
                                <input id="quantitiesCantidad[{{$contractCondition['id']}}]" type="number"
                                    class="form-control" name="quantitiesCantidad[]" required min="0"
                                    value="{{$contractCondition['quantitiesCantidad']}}"
                                    onchange="getCurrentValue(this);">
                            </td>
                            <td>
                                <input id="quantitiesMonto[{{$contractCondition['id']}}]" type="number"
                                    class="form-control" name="quantitiesMonto[]"
                                    value="{{$contractCondition['quantitiesMonto']}}" readonly>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <input type="hidden" id="quantitiesTableLength" name="quantitiesTableLength" required value="0">
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
    $('#tablaQuantities').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
    const quantitiesTableLength = $('#tablaQuantities').bootstrapTable('getData').length;
    document.getElementById('quantitiesTableLength').value = quantitiesTableLength;
    const allContractConditions = {!!json_encode($allContractConditions->toArray(), JSON_HEX_TAG) !!};

    function getCurrentDate(inputDate) {
        //Saca el valor del formulario de la fecha
        let formAction = document.getElementById('inputPeriodoForm').action;
        //Elimina su fecha inicial
        formAction = formAction.slice(0, -7);
        //Agrega la fecha del input
        formAction = formAction + inputDate.value;
        document.getElementById('inputPeriodoForm').action = formAction;
    }

    function getCurrentValue(inputNumber) {
        //regex sacar ID - ID en posicion [0]
        let currentConditionID = inputNumber.id.match(/(\d+)/);
        let currentValue = inputNumber.value;
        let currentCondition;
        let outputDocument = document.getElementById('quantitiesMonto[' + currentConditionID[0] + ']');
        //Sacar la condicion con la ID del input
        allContractConditions.forEach((condition) => {
            if (condition['id'] == currentConditionID[0]) {
                currentCondition = condition;
            }
        });
        //Si la cantidad es mayor a 0
        if (currentValue > 0) {
            let neededConditions = [];
            allContractConditions.forEach((condition) => {
                if (condition['idClient'] == currentCondition['idClient'] &&
                    condition['idContract'] == currentCondition['idContract'] &&
                    condition['idModule'] == currentCondition['idModule'] &&
                    condition['idPaymentUnit'] == currentCondition['idPaymentUnit']) {
                    //Si es variable o escalonado
                    neededConditions.push({
                        'modalidad': condition['contractsConditions_Modalidad'],
                        'cantidad': condition['contractsConditions_Cantidad'],
                        'precio': condition['contractsConditions_Precio']
                    })
                }
            });
            let cantidad = 0;
            let maxCantidad = Math.max.apply(Math, neededConditions.map((item) => {
                return item.cantidad;
            }))
            let maxItem = neededConditions.filter((item) => {
                return (item.cantidad === maxCantidad && item.modalidad != 'Adicional' && item.modalidad != 'Descuento')
            });
            let discount = neededConditions.filter((item) => {
                return (item.modalidad == 'Descuento')
            });
            console.log(neededConditions);
            neededConditions.forEach((condition) => {
                if (condition['modalidad'] == 'Variable' || condition['modalidad'] == 'Escalonado') {
                    if (currentValue <= condition['cantidad'] && currentValue > cantidad) {
                        outputDocument.value = parseFloat(condition['precio']).toFixed(2);
                    }
                    //cantidad es el valor de contractCondition cantidad anterior a la iteracion actual
                    cantidad = condition['cantidad'];
                    //Adicional
                } else if (condition['modalidad'] == 'Adicional') {
                    if (currentValue - maxCantidad >= condition['cantidad']) {
                        outputDocument.value = parseFloat(parseFloat(maxItem[0].precio) + Math.floor(currentValue - maxCantidad / condition['cantidad']) * parseFloat(condition['precio'])).toFixed(2);
                    }
                } else if (condition['modalidad'] == 'Fijo') {
                    outputDocument.value = parseFloat(condition['precio']).toFixed(2);
                }
            });
            if (discount.length > 0) {
                outputDocument.value = parseFloat(outputDocument.value).toFixed(2) * parseFloat(discount[0].precio).toFixed(2) / 100;
            }
        } else {
            outputDocument.value = 0;
        }
        //let hiddenInput = document.getElementById('inputPeriodoForm').action
    }
</script>
@endsection