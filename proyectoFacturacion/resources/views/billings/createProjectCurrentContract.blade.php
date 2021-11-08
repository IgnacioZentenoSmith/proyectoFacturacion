@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Creando proyecto para el contrato: <strong>{{$contract['contractsNombre']}}</strong> de número:
                <strong>{{$contract['contractsNumero']}}</strong>
            </div>
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Este proyecto es un proyecto manual y solo existirá para este período: <u>{{$tributaryDocument['tributarydocuments_period']}}</u></strong>
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{route('billings.storeProjectCurrentContract', $tributaryDocument['id'])}}">
    @csrf
    <div class="form-group row">
        <label for="contractPaymentDetails_description" class="col-md-4 col-form-label text-md-right">Descripción</label>
        <div class="col-md-6">
            <input id="contractPaymentDetails_description" type="text" class="form-control" name="contractPaymentDetails_description" required autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractPaymentDetails_glosaProyecto" class="col-md-4 col-form-label text-md-right">Glosa</label>
        <div class="col-md-6">
            <input id="contractPaymentDetails_glosaProyecto" type="text" class="form-control" name="contractPaymentDetails_glosaProyecto" required autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractPaymentDetails_units" class="col-md-4 col-form-label text-md-right">Unidades</label>
        <div class="col-md-6">
            <input id="contractPaymentDetails_units" type="number" class="form-control" name="contractPaymentDetails_units" required autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractPaymentDetails_recepcionMunicipal" class="col-md-4 col-form-label text-md-right">Fecha de recepción municipal</label>
        <div class="col-md-6">
            <input id="contractPaymentDetails_recepcionMunicipal" type="date" class="form-control" name="contractPaymentDetails_recepcionMunicipal">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear proyecto
            </button>
            <a class="btn btn-secondary" href="{{route('admin.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
