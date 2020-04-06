@extends('layouts.app')

@section('content')
<div class="card shadow">
  <h5 class="card-header">Usuarios</h5>
  <div class="card-body">
    <h5 class="card-title">Objetivo</h5>
    <p class="card-text">Modulo encargado de crear, modificar y eliminar usuarios.</p>

    <br>

    <div class="table-responsive">
            <table id="tablaDetalle" class="table table-hover w-auto text-nowrap"
            data-show-export="true"
            data-pagination="true"
            data-click-to-select="true"
            data-show-columns="true"
            data-sortable="true"
            data-search="true" 
            data-live-search="true"
            data-buttons-align="left"
            data-search-align="right"
            data-server-sort="false">
        <thead>
          <tr>
            <th scope="col" data-field="ID" data-sortable="true">ID</th>
            <th scope="col" data-field="Nombre" data-sortable="true">Nombre</th>
            <th scope="col" data-field="Email" data-sortable="true">Email</th>
            <th scope="col" data-field="Password" data-sortable="true">Password</th>
            <th scope="col" data-field="Token" data-sortable="true">Token</th>
            <th scope="col" data-field="Accion" data-sortable="true">Accion</th>
          </tr>
        </thead>
      <tbody>
      @foreach($usuarios as $usuario)
        <tr>
          <td>{{$usuario['id']}}</td>
          <td>{{$usuario['name']}}</td>
          <td>{{$usuario['email']}}</td>
          <td>{{$usuario['password']}}</td>
          <td>{{$usuario['remember_token']}}</td>
          <td>
            <a class="btn btn-secondary" href="" role="button">Editar</a>
            <button class="btn btn-danger" type="submit">Eliminar</button>
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

$('#tablaDetalle').bootstrapTable({
    pageSize: 25,
    exportDataType: 'all',
});

</script>
@endsection