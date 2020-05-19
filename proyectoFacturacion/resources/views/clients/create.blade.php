@extends('clients.layout')
@section('clientContent')


<form method="POST" action="{{route('clients.store')}}">
    @csrf

    <div class="form-group row">
        <label for="clientRazonSocial" class="col-md-4 col-form-label text-md-right">Razon social</label>
        <div class="col-md-6">
            <input id="clientRazonSocial" type="text" class="form-control" name="clientRazonSocial" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientRUT" class="col-md-4 col-form-label text-md-right">RUT del cliente</label>
        <div class="col-md-6">
            <input id="clientRUT" type="text" class="form-control" name="clientRUT" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientContactEmail" class="col-md-4 col-form-label text-md-right">Email de contacto</label>
        <div class="col-md-6">
            <input id="clientContactEmail" type="email" class="form-control" name="clientContactEmail" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientPhone" class="col-md-4 col-form-label text-md-right">Teléfono del cliente</label>
        <div class="col-md-6">
            <input id="clientPhone" type="text" class="form-control" name="clientPhone" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientDirection" class="col-md-4 col-form-label text-md-right">Dirección del cliente</label>
        <div class="col-md-6">
            <input id="clientDirection" type="text" class="form-control" name="clientDirection" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="clientBusinessActivity" class="col-md-4 col-form-label text-md-right">Giro del cliente</label>
        <div class="col-md-6">
            <input id="clientBusinessActivity" type="text" class="form-control" name="clientBusinessActivity" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="hasParent" class="col-md-4 col-form-label text-md-right">¿Pertenece a un holding?</label>
        <div class="col-md-6 form-check">
            <div class="pretty p-switch">
                <input type="radio" name="hasParent" checked value="no"
                    onchange="document.getElementById('parentClient').style.visibility = 'hidden';" />
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="hasParent" value="si"
                    onchange="document.getElementById('parentClient').style.visibility = 'visible';" />
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row" id="parentClient" style="visibility:hidden">
        <label for="clientParentId" class="col-md-4 col-form-label text-md-right">Cliente padre</label>

        <div class="col-md-6">
            <select class="form-control" id="clientParentId" name="clientParentId">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($clientesPadre as $clientePadre)
                <option value="{{$clientePadre['id']}}">{{$clientePadre['clientRazonSocial']}}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear cliente
            </button>
            <a class="btn btn-secondary" href="{{route('clients.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
