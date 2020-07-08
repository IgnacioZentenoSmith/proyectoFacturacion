@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.conditionsUpdate', $contractConditions['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Módulo</label>

        <div class="col-md-6">
            <select class="form-control" id="idModule" name="idModule">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($modules as $module)
                <option value="{{$module['id']}}"
                @if ($contractConditions['idModule'] == $module['id']) selected @endif>{{$module['moduleName']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="idPaymentUnit" class="col-md-4 col-form-label text-md-right">Unidad de pago</label>

        <div class="col-md-6">
            <select class="form-control" id="idPaymentUnit" name="idPaymentUnit">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($paymentUnits as $paymentUnit)
                <option value="{{$paymentUnit['id']}}"
                @if ($contractConditions['idPaymentUnit'] == $paymentUnit['id']) selected @endif>{{$paymentUnit['payment_units']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="idClient" class="col-md-4 col-form-label text-md-right">Cliente</label>

        <div class="col-md-6">
            <select class="form-control" id="idClient" name="idClient">
                <option value="" selected>Ninguno seleccionado</option>
                <!-- Clientes padres y hijos -->
                @foreach($clients as $client)
                <option value="{{$client['id']}}"
                @if ($contractConditions['idClient'] == $client['id']) selected @endif>{{$client['clientRazonSocial']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label id="labelPrecio" for="contractsConditions_Precio" class="col-md-4 col-form-label text-md-right">Precio</label>
        <div class="col-md-6">
            <input id="contractsConditions_Precio" type="number" class="form-control" name="contractsConditions_Precio" required value="{{$contractConditions['contractsConditions_Precio']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Modalidad" class="col-md-4 col-form-label text-md-right">Modalidad</label>

        <div class="col-md-6">
            <select class="form-control" id="contractsConditions_Modalidad" name="contractsConditions_Modalidad" onchange="getCurrentModalidad(this);>
                <option value="">Ninguno seleccionado</option>
                <option value="Fijo" @if ($contractConditions['contractsConditions_Modalidad'] == 'Fijo') selected @endif>Fijo</option>
                <option value="Variable" @if ($contractConditions['contractsConditions_Modalidad'] == 'Variable') selected @endif>Variable</option>
                <option value="Escalonado" @if ($contractConditions['contractsConditions_Modalidad'] == 'Escalonado') selected @endif>Escalonado</option>
                <option value="Adicional" @if ($contractConditions['contractsConditions_Modalidad'] == 'Adicional') selected @endif>Adicional</option>
                <option value="Descuento" @if ($contractConditions['contractsConditions_Modalidad'] == 'Descuento') selected @endif>Descuento</option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Cantidad" class="col-md-4 col-form-label text-md-right">Cantidad</label>
        <div class="col-md-6">
            <input id="contractsConditions_Cantidad" type="number" step="0.01" class="form-control" name="contractsConditions_Cantidad" required value="{{$contractConditions['contractsConditions_Cantidad']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_fechaInicio" class="col-md-4 col-form-label text-md-right">Fecha de inicio</label>
        <div class="col-md-6">
            <input id="contractsConditions_fechaInicio" type="date" class="form-control" name="contractsConditions_fechaInicio" required value="{{$contractConditions['contractsConditions_fechaInicio']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_fechaTermino" class="col-md-4 col-form-label text-md-right">Fecha de término</label>
        <div class="col-md-6">
            <input id="contractsConditions_fechaTermino" type="date" class="form-control" name="contractsConditions_fechaTermino" value="{{$contractConditions['contractsConditions_fechaTermino']}}">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar condicion contractual
            </button>
            <a class="btn btn-secondary" href="{{ route('contracts.conditions', $contractConditions['idContract']) }}" role="button">Cancelar</a>
        </div>
    </div>
</form>

<script> 
    function getCurrentModalidad(inputModalidad) {
        if (inputModalidad.value == 'Fijo' || inputModalidad.value == 'Descuento') {
            document.getElementById('contractsConditions_Cantidad').value = 1;
            document.getElementById('contractsConditions_Cantidad').readOnly = true;
            //Cambiar nombre del label del precio a dcto si es dcto
            if (inputModalidad.value == 'Descuento') {
                document.getElementById('labelPrecio').innerHTML = 'Porcentaje de descuento';
            } else {
                document.getElementById('labelPrecio').innerHTML = 'Precio';
            }
        } else {
            document.getElementById('contractsConditions_Cantidad').readOnly = false;
            document.getElementById('labelPrecio').innerHTML = 'Precio';
        }
    }
</script>
@endsection
