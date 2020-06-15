@extends('clients.layout')
@section('clientContent')


<form method="POST" action="{{route('clients.childrenUpdate', [$holding['id'], $hijo['id']])}}">
    @csrf
    {{ method_field('PUT') }}

    <div class="form-group row">
        <label for="clientRazonSocial" class="col-md-4 col-form-label text-md-right">Razon social</label>
        <div class="col-md-6">
            <input id="clientRazonSocial" type="text" class="form-control" name="clientRazonSocial" required value="{{$hijo['clientRazonSocial']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientRUT" class="col-md-4 col-form-label text-md-right">RUT del cliente</label>
        <div class="col-md-6">
            <input id="clientRUT" type="text" class="form-control" name="clientRUT" required value="{{$hijo['clientRUT']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientContactEmail" class="col-md-4 col-form-label text-md-right">Email de contacto</label>
        <div class="col-md-6">
            <input id="clientContactEmail" type="email" class="form-control" name="clientContactEmail" value="{{$hijo['clientContactEmail']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientPhone" class="col-md-4 col-form-label text-md-right">Teléfono del cliente</label>
        <div class="col-md-6">
            <input id="clientPhone" type="text" class="form-control" name="clientPhone" value="{{$hijo['clientPhone']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientDirection" class="col-md-4 col-form-label text-md-right">Dirección del cliente</label>
        <div class="col-md-6">
            <input id="clientDirection" type="text" class="form-control" name="clientDirection" value="{{$hijo['clientDirection']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientBusinessActivity" class="col-md-4 col-form-label text-md-right">Giro del cliente</label>
        <div class="col-md-6">
            <input id="clientBusinessActivity" type="text" class="form-control" name="clientBusinessActivity" value="{{$hijo['clientBusinessActivity']}}">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar cliente
            </button>
            <a class="btn btn-secondary" href="{{ route('clients.childrenIndex', $holding['id']) }}" role="button">Cancelar</a>
        </div>
    </div>
</form>

@endsection
