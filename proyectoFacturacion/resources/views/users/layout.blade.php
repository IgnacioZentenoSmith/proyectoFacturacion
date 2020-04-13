@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('users')) ? 'active' : '' }}" href="{{route('users.index')}}">Listar usuarios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('users/create')) ? 'active' : '' }}" href="{{route('users.create')}}">Crear usuario</a>
      </li>
    </ul>
  </div>

  <div class="card-body py-3 my-3">

    @yield('usersContent')



  </div>
</div>
@endsection