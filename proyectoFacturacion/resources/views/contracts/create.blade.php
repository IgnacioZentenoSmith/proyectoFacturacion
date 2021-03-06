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

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsRecepcionMunicipal" value="1"/>
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsRecepcionMunicipal" value="0" checked/>
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
                <input type="radio" name="contractsManualContract" value="1"/>
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="contractsManualContract" value="0" checked/>
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
            <input id="contractsNombre" type="text" class="form-control" name="contractsNombre" required autofocus>
        </div>
    </div> --}}

    <div class="form-group row">
        <label for="contractsNumero" class="col-md-4 col-form-label text-md-right">Número del contrato PlanOK</label>
        <div class="col-md-6">
            <input id="contractsNumero" type="text" class="form-control" name="contractsNumero" required>
        </div>
    </div>
    <div class="form-group row">
        <label for="contractsNumeroCliente" class="col-md-4 col-form-label text-md-right">Número del contrato Cliente</label>
        <div class="col-md-6">
            <input id="contractsNumeroCliente" type="text" class="form-control" name="contractsNumeroCliente" required>
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
