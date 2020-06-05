@extends('contracts.layout')
@section('contractsContent')


<form method="POST" action="{{route('contracts.quantitiesStore', $contract['id'])}}">
    @csrf
    <div class="form-group row">
        <label for="idContractCondition" class="col-md-4 col-form-label text-md-right">ID condición contractual</label>
        <div class="col-md-6">
            <select class="form-control" id="idContractCondition" name="idContractCondition" onchange="getCurrentValue(this);">
                <option value="" selected>Ninguno seleccionado</option>
                <!-- Permitir solo clientes padres desde Backend -->
                @foreach($contractConditions as $contractCondition)
                <option value="{{$contractCondition['id']}}">{{$contractCondition['id']}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="idModule" class="col-md-4 col-form-label text-md-right">Módulo</label>

        <div class="col-md-6">
          <input id="idModule" type="text" class="form-control" name="idModule" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="idPaymentUnit" class="col-md-4 col-form-label text-md-right">Unidad de pago</label>

        <div class="col-md-6">
          <input id="idPaymentUnit" type="text" class="form-control" name="idPaymentUnit" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="idClient" class="col-md-4 col-form-label text-md-right">Cliente</label>

        <div class="col-md-6">
          <input id="idClient" type="text" class="form-control" name="idClient" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="idContract" class="col-md-4 col-form-label text-md-right">Contrato</label>

        <div class="col-md-6">
          <input id="idContract" type="text" class="form-control" name="idContract" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Moneda" class="col-md-4 col-form-label text-md-right">Tipo de moneda</label>

        <div class="col-md-6">
            <input id="contractsConditions_Moneda" type="text" class="form-control" name="contractsConditions_Moneda" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="contractsConditions_Modalidad" class="col-md-4 col-form-label text-md-right">Modalidad</label>

        <div class="col-md-6">
            <input id="contractsConditions_Modalidad" type="text" class="form-control" name="contractsConditions_Modalidad" required disabled>
        </div>
    </div>

    <div class="form-group row">
        <label for="quantitiesCantidad" class="col-md-4 col-form-label text-md-right">Cantidad</label>
        <div class="col-md-6">
            <input id="quantitiesCantidad" type="number" class="form-control" name="quantitiesCantidad" required min="0">
        </div>
    </div>

    <div class="form-group row">
        <label for="quantitiesPeriodo" class="col-md-4 col-form-label text-md-right">Período</label>
        <div class="col-md-6">
            <input id="quantitiesPeriodo" type="month" class="form-control" name="quantitiesPeriodo" required>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear cantidad
            </button>
            <a class="btn btn-secondary" href="{{ route('contracts.quantities', [$contract['id'], 'todos']) }}" role="button">Cancelar</a>
        </div>
    </div>
</form>

<script>    
    const allContractConditions = {!! json_encode($allContractConditions->toArray(), JSON_HEX_TAG) !!};
    function getCurrentValue(idCondicionContractual) {
      contractConditions.forEach((contractCondition) => {
        if (idCondicionContractual.value == contractCondition['id']) {
          document.getElementById('idModule').value = contractCondition['contractCondition_moduleName'];
          document.getElementById('idPaymentUnit').value = contractCondition['contractCondition_paymentUnitName'];
          document.getElementById('idClient').value = contractCondition['contractCondition_clientName'];
          document.getElementById('idContract').value = contractCondition['contractCondition_contractName'];
          document.getElementById('contractsConditions_Moneda').value = contractCondition['contractsConditions_Moneda'];
          document.getElementById('contractsConditions_Modalidad').value = contractCondition['contractsConditions_Modalidad'];
        }
      });
    }
</script>
@endsection
