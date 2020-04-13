@extends('layouts.app')
@section('content')

<div class="card shadow">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('admin')) ? 'active' : '' }}" href="{{route('admin.index')}}">Permisos usuarios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (request()->is('admin/roles')) ? 'active' : '' }}" href="{{route('admin.roles')}}">Permisos roles</a>
      </li>
    </ul>
  </div>

  <div class="card-body py-3 my-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          @yield('adminContent')
        </div>
      </div>
    </div>  


  </div>
</div>
@endsection