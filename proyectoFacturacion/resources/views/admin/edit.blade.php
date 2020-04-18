@extends('admin.layout')
@section('adminContent')


<form method="POST" action="{{route('admin.update', $usuario['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">Nombre</label>
        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="{{$usuario['name']}}" required
                autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
        <div class="col-md-6">
            <input id="email" type="email" class="form-control" name="email" value="{{$usuario['email']}}" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="role" class="col-md-4 col-form-label text-md-right">Rol</label>

        <div class="col-md-6">
            <select class="form-control" id="role" name="role">
                <option value="" @if ($usuario['role']==='' ) selected @endif>Sin rol</option>
                <option value="Administrador" @if ($usuario['role']==='Administrador' ) selected @endif>
                    Administrador</option>
                <option value="Vendedor" @if ($usuario['role']==='Vendedor' ) selected @endif>Vendedor</option>
                <option value="Ejecutivo" @if ($usuario['role']==='Ejecutivo' ) selected @endif>Ejecutivo</option>
            </select>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar
            </button>
            <a class="btn btn-secondary" href="{{route('admin.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>

@endsection
