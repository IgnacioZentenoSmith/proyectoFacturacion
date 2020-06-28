@extends('clients.layout')
@section('clientContent')


<form method="POST" action="{{route('clients.update', $cliente['id'])}}">
    @csrf
    {{ method_field('PUT') }}

    <div class="form-group row">
        <label for="clientRazonSocial" class="col-md-4 col-form-label text-md-right">Nombre del holding</label>
        <div class="col-md-6">
            <input id="clientRazonSocial" type="text" class="form-control" name="clientRazonSocial" required value="{{$cliente['clientRazonSocial']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="idEjecutivo" class="col-md-4 col-form-label text-md-right">Atención del holding</label>

        <div class="col-md-6">
            <select class="form-control" id="idEjecutivo" name="idEjecutivo">
                <option>Ninguno seleccionado</option>
                <!-- Permitir solo ejecutivos -->
                @foreach($ejecutivos as $ejecutivo)
                    <option value="{{$ejecutivo['id']}}" @if ($ejecutivo['id'] == $cliente['idUser']) selected @endif>{{$ejecutivo['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientContactEmail" class="col-md-4 col-form-label text-md-right">Email de contacto</label>
        <div class="col-md-6">
            <input id="clientContactEmail" type="email" class="form-control" name="clientContactEmail" value="{{$cliente['clientContactEmail']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientPhone" class="col-md-4 col-form-label text-md-right">Teléfono del holding</label>
        <div class="col-md-6">
            <input id="clientPhone" type="text" class="form-control" name="clientPhone" value="{{$cliente['clientPhone']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientDirection" class="col-md-4 col-form-label text-md-right">Dirección del holding</label>
        <div class="col-md-6">
            <input id="clientDirection" type="text" class="form-control" name="clientDirection" value="{{$cliente['clientDirection']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientBusinessActivity" class="col-md-4 col-form-label text-md-right">Giro del holding</label>
        <div class="col-md-6">
            <input id="clientBusinessActivity" type="text" class="form-control" name="clientBusinessActivity" value="{{$cliente['clientBusinessActivity']}}">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar holding
            </button>
            <a class="btn btn-secondary" href="{{route('clients.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>

@endsection
