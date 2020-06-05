@extends('contracts.layout')
@section('contractsContent')

<div class="row justify-content-center">
    <div class="col-auto">
    <div class="col-12">
      <div class="alert alert-info" role="alert">
        Condiciones contractuales del contrato: <strong>{{$contract['contractsNumero']}}</strong> de la razon social: <strong>{{$contract['contract_clientName']}}</strong>
      </div>
      <a class="btn btn-primary" href="{{ route('contracts.conditionsCreate', $contract['id']) }}"role="button">Nueva condicion</a>
    </div>
        <div class="table-responsive">
            <table id="tablaContractsConditions" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="idModule" data-sortable="true">Modulo</th>
                        <th scope="col" data-field="idPaymentUnit" data-sortable="true">Unidad de pago</th>
                        <th scope="col" data-field="idClient" data-sortable="true">Cliente</th>
                        <th scope="col" data-field="idContract" data-sortable="true">Contrato</th>
                        <th scope="col" data-field="contractsConditions_Moneda" data-sortable="true">Moneda</th>
                        <th scope="col" data-field="contractsConditions_Precio" data-sortable="true">Precio</th>
                        <th scope="col" data-field="contractsConditions_Modalidad" data-sortable="true">Modalidad</th>
                        <th scope="col" data-field="contractsConditions_Cantidad" data-sortable="true">Cantidad</th>
                        <th scope="col" data-field="contractsConditions_fechaInicio" data-sortable="true">Fecha de inicio</th>
                        <th scope="col" data-field="contractsConditions_fechaTermino" data-sortable="true">Fecha de término</th>

                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contractConditions as $contractCondition)
                    <tr>
                        <td>{{$contractCondition['id']}}</td>
                        <td>{{$contractCondition['contractCondition_moduleName']}}</td>
                        <td>{{$contractCondition['contractCondition_paymentUnitName']}}</td>
                        <td>{{$contractCondition['contractCondition_clientName']}}</td>
                        <td>{{$contractCondition['contractCondition_contractName']}}</td>
                        <td>{{$contractCondition['contractsConditions_Moneda']}}</td>
                        <td>{{$contractCondition['contractsConditions_Precio']}}</td>
                        <td>{{$contractCondition['contractsConditions_Modalidad']}}</td>
                        <td>{{$contractCondition['contractsConditions_Cantidad']}}</td>
                        <td>{{$contractCondition['contractsConditions_fechaInicio']}}</td>
                        <td>{{$contractCondition['contractsConditions_fechaTermino']}}</td>
                        <td>
                            @if(in_array(10, $authPermisos))
                            <!-- ID de la condicion -->
                            <a class="btn btn-secondary" href="{{ route('contracts.conditionsEdit', $contractCondition['id']) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(11, $authPermisos))
                            <!-- ID de la condicion -->
                            <form style="display: inline-block;" action="{{ route('contracts.conditionsDestroy', $contractCondition['id']) }}"
                                method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Eliminar(DEBUG)</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaContractsConditions').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
    document.onsubmit=function(){
           return confirm('¿Está seguro de esta operación? Esto es irreversible.');
       }

</script>
@endsection