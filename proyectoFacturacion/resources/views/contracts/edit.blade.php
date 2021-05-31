@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.update', $contract['id'])}}">
    @csrf
    {{ method_field('PUT') }}

    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Módulo</label>

        <div class="col-md-6">
            <select class="form-control" id="idModule" name="idModule">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($modules as $module)
                <option value="{{$module['id']}}"
                @if ($contract['idModule'] == $module['id']) selected @endif>{{$module['moduleName']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsRecepcionMunicipal" class="col-md-4 col-form-label text-md-right">Existe recepción municipal</label>

        <div class="col-md-6">

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsRecepcionMunicipal" value="1" @if ($contract['contractsRecepcionMunicipal']) checked @endif/>
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsRecepcionMunicipal" value="0" @if (!$contract['contractsRecepcionMunicipal']) checked @endif/>
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>

        </div>
    </div>

    <div class="form-group row">
        <label for="contractsManualContract" class="col-md-4 col-form-label text-md-right">Es un contrato especial (carga manual)</label>

        <div class="col-md-6">

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsManualContract" value="1" @if ($contract['contractsManualContract']) checked @endif/>
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsManualContract" value="0" @if (!$contract['contractsManualContract']) checked @endif/>
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>


        </div>
    </div>

    <!-- Nombre del contrato -->
    {{-- <div class="form-group row">
        <label for="contractsNombre" class="col-md-4 col-form-label text-md-right">Nombre del contrato</label>
        <div class="col-md-6">
            <input id="contractsNombre" type="text" class="form-control" name="contractsNombre" required autofocus value="{{$contract['contractsNombre']}}">
        </div>
    </div> --}}

    <div class="form-group row">
        <label for="contractsNumero" class="col-md-4 col-form-label text-md-right">Número del contrato PlanOK</label>
        <div class="col-md-6">
            <input id="contractsNumero" type="text" class="form-control" name="contractsNumero" required value="{{$contract['contractsNumero']}}">
        </div>
    </div>
    <div class="form-group row">
        <label for="contractsNumero" class="col-md-4 col-form-label text-md-right">Número del contrato Cliente</label>
        <div class="col-md-6">
            <input id="contractsNumeroCliente" type="text" class="form-control" name="contractsNumeroCliente" required value="{{$contract['contractsNumeroCliente']}}">
        </div>
    </div>


    <div class="form-group row">
        <label for="contractsMoneda" class="col-md-4 col-form-label text-md-right">Tipo de moneda</label>

        <div class="col-md-6">
            <select class="form-control" id="contractsMoneda" name="contractsMoneda">
                <option value="UF" @if ($contract['contractsMoneda'] == 'UF') selected @endif>UF</option>
                <option value="CLP" @if ($contract['contractsMoneda'] == 'CLP') selected @endif>Peso chileno</option>
                <option value="USD" @if ($contract['contractsMoneda'] == 'USD') selected @endif>Dolar</option>
            </select>
        </div>
    </div>


    <div class="form-group row">
        <label for="contractsFecha" class="col-md-4 col-form-label text-md-right">Fecha del contrato</label>
        <div class="col-md-6">
            <input id="contractsFecha" type="date" class="form-control" name="contractsFecha" required value="{{$contract['contractsFecha']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="idClient" class="col-md-4 col-form-label text-md-right">Cliente</label>

        <div class="col-md-6">
            <select class="form-control" id="idClient" name="idClient">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($clients as $client)
                <option value="{{$client['id']}}"
                @if ($client['id'] == $contract['idClient']) selected @endif>{{$client['clientRazonSocial']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar contrato
            </button>
            <a class="btn btn-secondary" href="{{route('contracts.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
