@extends('contracts.layout')
@section('contractsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaContracts" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID contrato</th>
                        <th scope="col" data-field="idClient" data-sortable="true">Holding</th>
                        <th scope="col" data-field="contractsNombre" data-sortable="true">Nombre del contrato</th>
                        <th scope="col" data-field="contractsNumero" data-sortable="true">Número del contrato</th>
                        <th scope="col" data-field="contractsMoneda" data-sortable="true">Moneda del contrato</th>
                        <th scope="col" data-field="contract_clientEjecutivoName" data-sortable="true">Atención del holding</th>
                        <th scope="col" data-field="contractsFecha" data-sortable="true">Fecha</th>
                        <th scope="col" data-field="contractsEstado" data-sortable="true">Estado</th>

                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                    <tr>
                        <td>{{$contract['id']}}</td>
                        <td>{{$contract['contract_clientName']}}</td>
                        <td>{{$contract['contractsNombre']}}</td>
                        <td>{{$contract['contractsNumero']}}</td>
                        <td>{{$contract['contractsMoneda']}}</td>
                        <td>{{$contract['contract_clientEjecutivoName']}}</td>
                        <td>{{$contract['contractsFecha']}}</td>
                        <td class="text-white @if ($contract['contractsEstado'] == false) bg-secondary @else bg-success @endif">
                            @if ($contract['contractsEstado'] == false) Inactivo
                            @else Activo 
                            @endif

                        <td>
                            @if(in_array(7, $authPermisos))
                                <a class="btn btn-primary" href="{{ route('contracts.conditions', $contract['id']) }}"
                                role="button">Ingresar condiciones</a>
                            @endif

                            @if(in_array(7, $authPermisos))
                                <a class="btn btn-primary" href="{{ route('contracts.quantities', [$contract['id'], $periodo]) }}"
                                role="button">Ingresar cantidades</a>
                            @endif

                            @if(in_array(7, $authPermisos))
                                <a class="btn btn-light" href="{{ route('contracts.editContractStatus', $contract['id']) }}"
                                role="button">
                                @if ($contract['contractsEstado'] == false) Activar
                                @else Desactivar @endif
                            </a>
                            @endif

                            @if(in_array(10, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('contracts.edit', $contract['id']) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(11, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('contracts.destroy', $contract['id']) }}"
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
    $('#tablaContracts').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
    document.onsubmit=function(){
           return confirm('¿Está seguro de esta operación? Esto es irreversible.');
       }

</script>
@endsection
