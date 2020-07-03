@extends('clients.layout')
@section('clientContent')

<div class="row justify-content-center">
    <div class="col-auto">

    <div class="col-12">
    <div class="alert alert-info" role="alert">
        Razones sociales del holding: <strong>{{$holding['clientRazonSocial']}}</strong>
      </div>
      <a class="btn btn-primary" href="{{ route('clients.childrenCreate', $holding['id']) }}"role="button">Nueva razón social</a>
    
    </div>
        <div class="table-responsive">
            <table id="tablaChildren" class="table table-hover w-auto text-nowrap" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="clientRazonSocial" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="clientRUT" data-sortable="true">RUT</th>
                        <th scope="col" data-field="clientContactEmail" data-sortable="true">Email de contacto</th>
                        <th scope="col" data-field="clientPhone" data-sortable="true">Teléfono del cliente</th>
                        <th scope="col" data-field="clientDirection" data-sortable="true">Dirección del cliente</th>
                        <th scope="col" data-field="clientBusinessActivity" data-sortable="true">Giro del cliente</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $child)
                    <tr>
                        <td>{{$child['id']}}</td>
                        <td>{{$child['clientRazonSocial']}}</td>
                        <td>{{$child['clientRUT']}}</td>
                        <td>{{$child['clientContactEmail']}}</td>
                        <td>{{$child['clientPhone']}}</td>
                        <td>{{$child['clientDirection']}}</td>
                        <td>{{$child['clientBusinessActivity']}}</td>
                        <td>
                            @if(in_array(10, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('clients.childrenEdit', [$holding['id'], $child['id']]) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(11, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('clients.childrenDestroy', [$holding['id'], $child['id']]) }}"
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
    $('#tablaChildren').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
    document.onsubmit=function(){
           return confirm('¿Está seguro de esta operación? Esto es irreversible.');
       }

</script>
@endsection
