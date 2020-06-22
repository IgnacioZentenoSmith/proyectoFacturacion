@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.update', $contract['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="contractsNombre" class="col-md-4 col-form-label text-md-right">Nombre del contrato</label>
        <div class="col-md-6">
            <input id="contractsNombre" type="text" class="form-control" name="contractsNombre" required autofocus value="{{$contract['contractsNombre']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsNumero" class="col-md-4 col-form-label text-md-right">Numero del contrato</label>
        <div class="col-md-6">
            <input id="contractsNumero" type="text" class="form-control" name="contractsNumero" required value="{{$contract['contractsNumero']}}">
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

    <div class="form-group row">
        <label for="idEjecutivo" class="col-md-4 col-form-label text-md-right">Atencion del cliente</label>

        <div class="col-md-6">
            <select class="form-control" id="idEjecutivo" name="idEjecutivo">
                <option>Ninguno seleccionado</option>
                @foreach($users as $user)
                  @if ($user['role'] == 'Ejecutivo')
                    <option value="{{$user['id']}}"
                    @if ($user['id'] == $ejecutivoActual['idUser']) selected @endif>{{$user['name']}}</option>
                  @endif
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
