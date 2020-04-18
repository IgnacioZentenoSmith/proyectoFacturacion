@extends('admin.layout')
@section('adminContent')

<h5 class="card-title mb-5">Editar permisos de usuario</h5>
<form method="POST" action="{{route('admin.updatePermisos', $usuario['id'])}}">
    @csrf
    {{ method_field('PUT') }}

      <div class="row justify-content-center">
        <div class="col-auto">
        @if (Auth::user()->id == $usuario['id'])
          <div class="alert alert-warning" role="alert">
            No puede editar sus propios permisos.
          </div>
        @endif

        <div class="table-responsive">
          <table id="tablaPermisos" class="table table-hover w-auto text-nowrap table-striped table-bordered">
              <thead class="thead-dark">
                  <tr>
                      <th scope="col" data-field="actionName" data-sortable="true">Nombre</th>
                      <th scope="col" data-field="actionType" data-sortable="true">Tipo</th>
                      <th scope="col" data-field="id" data-sortable="true">Codigo</th>
                      <th scope="col" data-field="actionParentId" data-sortable="true">Codigo padre</th>
                      <th scope="col" data-field="permission" data-sortable="true">Permiso</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($acciones as $accion)
                  <tr>
                      <td>{{$accion['actionName']}}</td>
                      <td>{{$accion['actionType']}}</td>
                      <td>{{$accion['id']}}</td>
                      <td>{{$accion['actionParentId']}}</td>
                      <td class="text-left">
                        <div class="pretty p-switch p-fill">
                          <input type="checkbox" name="acciones[]" value="{{$accion['id']}}" class="form-check" 
                          @if (in_array($accion['id'], $permisosUsuario)) checked @endif 
                          @if (Auth::user()->id == $usuario['id']) disabled @endif/>
                          <div class="state p-success">
                            <label>{{$accion['actionName']}}</label>
                          </div>
                        </div>
                      </td>
                    </tr>

                  @endforeach
              </tbody>
          </table>
        </div>

        </div>
      </div>


    <div class="form-group row mb-0">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary">
                Editar
            </button>
            <a class="btn btn-secondary" href="{{route('admin.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>

@endsection
