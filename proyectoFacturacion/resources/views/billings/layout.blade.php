@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link @if (request()->path() =='billings/0') active @endif" href="{{route('billings.index', 0)}}">Lista de documentos tributarios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if (request()->path() =='billings/manager/export/0') active @endif" href="{{route('billings.managerExport',0)}}">Exportar a Manager</a>
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
