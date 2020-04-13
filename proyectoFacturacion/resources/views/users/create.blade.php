@extends('users.layout')
@section('usersContent')

<form method="POST" action="{{route('users.store')}}">
    @csrf

    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">Nombre</label>
        <div class="col-md-6">
            <input id="name" type="text" class="form-control" name="name" value="Andres Fuentes" required
                autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
        <div class="col-md-6">
            <input id="email" type="email" class="form-control" name="email" value="ejemplo@gmail.com" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="role" class="col-md-4 col-form-label text-md-right">Rol</label>

        <div class="col-md-6">
            <select class="form-control" id="role" name="role" required>
                <option value="">Sin rol</option>
                <option value="Administrador">
                    Administrador</option>
                <option value="Vendedor">Vendedor</option>
                <option value="Ejecutivo">Ejecutivo</option>
            </select>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear
            </button>
            <a class="btn btn-secondary" href="{{route('users.index')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection