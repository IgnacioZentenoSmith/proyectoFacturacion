@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('clients')) ? 'active' : '' }}" href="{{route('clients.index')}}">Lista de clientes</a>
      </li>
    @if(in_array(9, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('clients/create')) ? 'active' : '' }}" href="{{route('clients.create')}}">Crear cliente</a>
      </li>
    @endif
    </ul>
  </div>

  <div class="card-body py-3 my-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          @yield('clientContent')
        </div>
      </div>
    </div>  


  </div>
</div>
@endsection