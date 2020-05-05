@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('contracts')) ? 'active' : '' }}" href="{{route('contracts.index')}}">Lista de contratos</a>
      </li>
    @if(in_array(9, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('contracts/create')) ? 'active' : '' }}" href="{{route('contracts.create')}}">Crear contrato</a>
      </li>
    @endif
    </ul>
  </div>

  <div class="card-body py-3 my-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          @yield('contractsContent')
        </div>
      </div>
    </div>  


  </div>
</div>
@endsection