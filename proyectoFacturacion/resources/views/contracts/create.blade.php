@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.store')}}">
    @csrf

    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Módulo del contrato</label>

        <div class="col-md-6">
            <select class="form-control" id="idModule" name="idModule">
                <option value="" selected>Ninguno seleccionado</option>
                <!-- Permitir solo clientes padres desde Backend -->
                @foreach($modules as $module)
                <option value="{{$module['id']}}">{{$module['moduleName']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsRecepcionMunicipal" class="col-md-4 col-form-label text-md-right">Existe recepción municipal</label>

        <div class="col-md-6">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="inlineRadio1" name="contractsRecepcionMunicipal" value="1">
                <label class="form-check-label" for="inlineRadio1">Si</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="inlineRadio2" name="contractsRecepcionMunicipal" value="0" checked>
                <label class="form-check-label" for="inlineRadio2">No</label>
            </div>
        </div>
    </div>

    <!-- Nombre del contrato -->
    {{-- <div class="form-group row">
        <label for="contractsNombre" class="col-md-4 col-form-label text-md-right">Nombre del contrato</label>
        <div class="col-md-6">
            <input id="contractsNombre" type="text" class="form-control" name="contractsNombre" required autofocus>
        </div>
    </div> --}}

    <div class="form-group row">
        <label for="contractsNumero" class="col-md-4 col-form-label text-md-right">Numero del contrato</label>
        <div class="col-md-6">
            <input id="contractsNumero" type="text" class="form-control" name="contractsNumero" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsMoneda" class="col-md-4 col-form-label text-md-right">Tipo de moneda</label>

        <div class="col-md-6">
            <select class="form-control" id="contractsMoneda" name="contractsMoneda">
                <option value="UF" selected>UF</option>
                <option value="CLP">Peso chileno</option>
                <option value="USD">Dólar</option>
            </select>
        </div>
    </div>


    <div class="form-group row">
        <label for="contractsFecha" class="col-md-4 col-form-label text-md-right">Fecha del contrato</label>
        <div class="col-md-6">
            <input id="contractsFecha" type="date" class="form-control" name="contractsFecha" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="idClient" class="col-md-4 col-form-label text-md-right">Cliente</label>

        <div class="col-md-6">
            <select class="form-control" id="idClient" name="idClient">
                <option value="" selected>Ninguno seleccionado</option>
                <!-- Permitir solo clientes padres desde Backend -->
                @foreach($clients as $client)
                <option value="{{$client['id']}}">{{$client['clientRazonSocial']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear contrato
            </button>
            <a class="btn btn-secondary" href="{{route('contracts.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
