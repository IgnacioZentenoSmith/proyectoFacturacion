@extends('clients.layout')
@section('clientContent')


<form method="POST" action="{{route('clients.update', $cliente['id'])}}">
    @csrf
    {{ method_field('PUT') }}

    <div class="form-group row">
        <label for="clientRazonSocial" class="col-md-4 col-form-label text-md-right">Razon social</label>
        <div class="col-md-6">
            <input id="clientRazonSocial" type="text" class="form-control" name="clientRazonSocial" required value="{{$cliente['clientRazonSocial']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientRUT" class="col-md-4 col-form-label text-md-right">RUT del cliente</label>
        <div class="col-md-6">
            <input id="clientRUT" type="text" class="form-control" name="clientRUT" required value="{{$cliente['clientRUT']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientContactEmail" class="col-md-4 col-form-label text-md-right">Email de contacto</label>
        <div class="col-md-6">
            <input id="clientContactEmail" type="email" class="form-control" name="clientContactEmail" required value="{{$cliente['clientContactEmail']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientPhone" class="col-md-4 col-form-label text-md-right">Teléfono del cliente</label>
        <div class="col-md-6">
            <input id="clientPhone" type="text" class="form-control" name="clientPhone" required value="{{$cliente['clientPhone']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientDirection" class="col-md-4 col-form-label text-md-right">Dirección del cliente</label>
        <div class="col-md-6">
            <input id="clientDirection" type="text" class="form-control" name="clientDirection" required value="{{$cliente['clientDirection']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="clientBusinessActivity" class="col-md-4 col-form-label text-md-right">Giro del cliente</label>
        <div class="col-md-6">
            <input id="clientBusinessActivity" type="text" class="form-control" name="clientBusinessActivity" required value="{{$cliente['clientBusinessActivity']}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="hasParent" class="col-md-4 col-form-label text-md-right">¿Pertenece a un holding?</label>
        <div class="col-md-6 form-check">
            <div class="pretty p-switch">
                <input type="radio" name="hasParent" @if ($cliente['clientParentId'] == null) checked @endif value="no"
                    onchange="document.getElementById('parentClient').style.visibility = 'hidden';" />
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="hasParent" @if ($cliente['clientParentId'] != null) checked @endif value="si"
                    onchange="document.getElementById('parentClient').style.visibility = 'visible';" />
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row" id="parentClient" 
    @if ($cliente['clientParentId'] != null) style="visibility:visible"
    @elseif ($cliente['clientParentId'] == null) style="visibility:hidden"
    @endif>
        <label for="clientParentId" class="col-md-4 col-form-label text-md-right">ID Cliente padre</label>

        <div class="col-md-6">
            <select class="form-control" id="clientParentId" name="clientParentId">
                <option value="">Ninguno seleccionado</option>
                @foreach($clientesPadre as $clientePadre)
                <!-- Cliente no puede ser su propio hijo -->
                  @if ($clientePadre['id'] != $cliente['id'])
                  <!-- Si es un hijo, seleccionar su padre -->
                    <option value="{{$clientePadre['id']}}" 
                    @if ($clientePadre['id'] == $cliente['clientParentId']) selected @endif>
                      {{$clientePadre['clientRazonSocial']}}
                    </option>
                  @endif
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar cliente
            </button>
            <a class="btn btn-secondary" href="{{route('clients.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>

@endsection
