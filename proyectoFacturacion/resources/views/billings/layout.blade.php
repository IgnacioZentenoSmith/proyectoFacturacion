@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('billings/*')) ? 'active' : '' }}" href="{{route('billings.index', 0)}}">Lista de documentos tributarios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('billings.manager')) ? 'active' : '' }}" href="">Exportar a Manager</a>
      </li>
    </ul>
  </div>

  <div class="card-body py-3 my-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          @yield('billingsContent')
        </div>
      </div>
    </div>


  </div>
</div>
@endsection
