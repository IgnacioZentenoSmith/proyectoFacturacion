@extends('users.layout')
@section('usersContent')


<div class="table-responsive">
    <table id="tablaDetalle" class="table table-hover w-auto text-nowrap" data-show-export="true" data-pagination="true"
        data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
        data-live-search="true" data-buttons-align="left" data-search-align="right" data-server-sort="false">
        <thead>
            <tr>
                <th scope="col" data-field="ID" data-sortable="true">ID</th>
                <th scope="col" data-field="Nombre" data-sortable="true">Nombre</th>
                <th scope="col" data-field="Email" data-sortable="true">Email</th>
                <th scope="col" data-field="Role" data-sortable="true">Rol</th>
                <th scope="col" data-field="Accion" data-sortable="true">Accion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{$usuario['id']}}</td>
                <td>{{$usuario['name']}}</td>
                <td>{{$usuario['email']}}</td>
                <td>{{$usuario['role']}}</td>
                <td>
                    <a class="btn btn-secondary" href="{{ route('users.edit', $usuario['id']) }}"
                        role="button">Editar</a>
                        <a class="btn btn-warning" href="{{ route('users.editPermisos', $usuario['id']) }}"
                        role="button">Permisos</a>
                    <form style="display: inline-block;" action="{{ route('users.destroy', $usuario['id']) }}"
                        method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaDetalle').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
</script>
@endsection
