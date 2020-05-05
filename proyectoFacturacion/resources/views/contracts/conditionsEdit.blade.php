@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.conditionsUpdate', $contractConditions['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Modulo</label>

        <div class="col-md-6">
            <select class="form-control" id="idModule" name="idModule">
                <option value="" selected>Ninguno seleccionado</option>
                <!-- Permitir solo clientes padres desde Backend -->
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
                <!-- Permitir solo clientes padres desde Backend -->
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
                <!-- Permitir solo clientes padres desde Backend -->
                @foreach($clients as $client)
                <option value="{{$client['id']}}"
                @if ($contractConditions['idClient'] == $client['id']) selected @endif>{{$client['clientName']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Moneda" class="col-md-4 col-form-label text-md-right">Tipo de moneda</label>

        <div class="col-md-6">
            <select class="form-control" id="contractsConditions_Moneda" name="contractsConditions_Moneda">
                <option value="">Ninguno seleccionado</option>
                <option value="UF" @if ($contractConditions['contractsConditions_Moneda'] == 'UF') selected @endif>UF</option>
                <option value="CLP" @if ($contractConditions['contractsConditions_Moneda'] == 'CLP') selected @endif>Peso chileno</option>
                <option value="USD" @if ($contractConditions['contractsConditions_Moneda'] == 'USD') selected @endif>Dolar</option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Precio" class="col-md-4 col-form-label text-md-right">Precio</label>
        <div class="col-md-6">
            <input id="contractsConditions_Precio" type="number" class="form-control" name="contractsConditions_Precio" required value="{{$contractConditions['contractsConditions_Precio']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Modalidad" class="col-md-4 col-form-label text-md-right">Modalidad</label>

        <div class="col-md-6">
            <select class="form-control" id="contractsConditions_Modalidad" name="contractsConditions_Modalidad">
                <option value="">Ninguno seleccionado</option>
                <option value="Fijo" @if ($contractConditions['contractsConditions_Modalidad'] == 'Fijo') selected @endif>Fijo</option>
                <option value="Variable" @if ($contractConditions['contractsConditions_Modalidad'] == 'Variable') selected @endif>Variable</option>
                <option value="Adicional" @if ($contractConditions['contractsConditions_Modalidad'] == 'Adicional') selected @endif>Adicional</option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Cantidad" class="col-md-4 col-form-label text-md-right">Cantidad</label>
        <div class="col-md-6">
            <input id="contractsConditions_Cantidad" type="number" class="form-control" name="contractsConditions_Cantidad" required value="{{$contractConditions['contractsConditions_Cantidad']}}">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear condicion contractual
            </button>
            <a class="btn btn-secondary" href="{{ route('contracts.conditions', $contractConditions['idContract']) }}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
