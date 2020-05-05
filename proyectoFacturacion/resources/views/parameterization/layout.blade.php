@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
    @if(in_array(13, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('parameterization/modules')) ? 'active' : '' }}" href="{{route('parameterization.modules')}}">Lista de modulos</a>
      </li>
    @endif
    @if(in_array(14, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('parameterization/paymentunits')) ? 'active' : '' }}" href="{{route('parameterization.paymentunits')}}">Lista de unidades de pago</a>
      </li>
    @endif
    @if(in_array(15, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('parameterization/modulesCreate')) ? 'active' : '' }}" href="{{route('parameterization.modulesCreate')}}">Crear modulo</a>
      </li>
    @endif
    @if(in_array(18, $authPermisos)) 
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('parameterization/paymentunitsCreate')) ? 'active' : '' }}" href="{{route('parameterization.paymentunitsCreate')}}">Crear unidad de pago</a>
      </li>
    @endif
    </ul>
  </div>

  <div class="card-body py-3 my-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          @yield('parameterizationContent')
        </div>
      </div>
    </div>  


  </div>
</div>
@endsection