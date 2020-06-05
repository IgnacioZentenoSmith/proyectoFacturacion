@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.quantitiesUpdate', [$quantity->id, $contract['id']])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="idContractCondition" class="col-md-4 col-form-label text-md-right">ID condición contractual</label>
        <div class="col-md-6">
            <select class="form-control" id="idContractCondition" name="idContractCondition" disabled>
                <option value="{{$contractCondition->id}}" selected>{{$contractCondition->id}}</option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Módulo</label>

        <div class="col-md-6">
          <input id="idModule" type="text" class="form-control" name="idModule" required disabled value="{{$contractCondition->contractCondition_moduleName}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="idPaymentUnit" class="col-md-4 col-form-label text-md-right">Unidad de pago</label>

        <div class="col-md-6">
          <input id="idPaymentUnit" type="text" class="form-control" name="idPaymentUnit" required disabled value="{{$contractCondition->contractCondition_paymentUnitName}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="idClient" class="col-md-4 col-form-label text-md-right">Cliente</label>

        <div class="col-md-6">
          <input id="idClient" type="text" class="form-control" name="idClient" required disabled value="{{$contractCondition->contractCondition_clientName}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="idContract" class="col-md-4 col-form-label text-md-right">Contrato</label>

        <div class="col-md-6">
          <input id="idContract" type="text" class="form-control" name="idContract" required disabled value="{{$contractCondition->contractCondition_contractName}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Moneda" class="col-md-4 col-form-label text-md-right">Tipo de moneda</label>

        <div class="col-md-6">
            <input id="contractsConditions_Moneda" type="text" class="form-control" name="contractsConditions_Moneda" required disabled value="{{$contractCondition->contractsConditions_Moneda}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Modalidad" class="col-md-4 col-form-label text-md-right">Modalidad</label>

        <div class="col-md-6">
            <input id="contractsConditions_Modalidad" type="text" class="form-control" name="contractsConditions_Modalidad" required disabled value="{{$contractCondition->contractsConditions_Modalidad}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="quantitiesCantidad" class="col-md-4 col-form-label text-md-right">Cantidad</label>
        <div class="col-md-6">
            <input id="quantitiesCantidad" type="number" class="form-control" name="quantitiesCantidad" required min="0" value="{{$quantity->quantitiesCantidad}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="quantitiesPeriodo" class="col-md-4 col-form-label text-md-right">Período</label>
        <div class="col-md-6">
            <input id="quantitiesPeriodo" type="month" class="form-control" name="quantitiesPeriodo" required value="{{$quantity->quantitiesPeriodo}}">
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar cantidad
            </button>
            <a class="btn btn-secondary" href="{{ route('contracts.quantities', [$contract['id'], 'todos']) }}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
