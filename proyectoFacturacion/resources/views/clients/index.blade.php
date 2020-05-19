@extends('clients.layout')
@section('clientContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaClients" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="clientRazonSocial" data-sortable="true">Razon social</th>
                        <th scope="col" data-field="clientRUT" data-sortable="true">RUT</th>
                        <th scope="col" data-field="clientContactEmail" data-sortable="true">clientContactEmail</th>
                        <th scope="col" data-field="clientPhone" data-sortable="true">clientPhone</th>
                        <th scope="col" data-field="clientDirection" data-sortable="true">clientDirection</th>
                        <th scope="col" data-field="clientBusinessActivity" data-sortable="true">clientBusinessActivity</th>
                        <th scope="col" data-field="clientParentId" data-sortable="true">Cliente padre</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{$cliente['id']}}</td>
                        <td>{{$cliente['clientRazonSocial']}}</td>
                        <td>{{$cliente['clientRUT']}}</td>
                        <td>{{$cliente['clientContactEmail']}}</td>
                        <td>{{$cliente['clientPhone']}}</td>
                        <td>{{$cliente['clientDirection']}}</td>
                        <td>{{$cliente['clientBusinessActivity']}}</td>
                        <td class="text-center">{{$cliente['clientParentId']}}</td>
                        <td>
                            @if(in_array(10, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('clients.edit', $cliente['id']) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(11, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('clients.destroy', $cliente['id']) }}"
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
    $('#tablaClients').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
    document.onsubmit=function(){
           return confirm('¿Está seguro de esta operación? Esto es irreversible.');
       }

</script>
@endsection
