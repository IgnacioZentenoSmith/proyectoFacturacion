@extends('admin.layout')
@section('adminContent')

<h5 class="card-title mb-5">Editar permisos de usuario</h5>
<form method="POST" action="{{route('admin.updatePermisos', $usuario['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <fieldset class="form-group">
      <div class="row">
        <div class="col-12 text-center">
          @foreach($acciones as $accion)
          <div class="pretty p-switch p-fill">
              <input type="checkbox" name="acciones[]" value="{{$accion['idActions']}}" class="form-check" 
              @if (in_array($accion['idActions'], $permisosUsuario)) checked @endif />
              <div class="state p-success">
                  <label>{{$accion['actionName']}}</label>
              </div>
          </div>
          @endforeach
        </div>
      </div>
    </fieldset>

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
