@extends('parameterization.layout')
@section('parameterizationContent')


<form method="POST" action="{{route('parameterization.modulesUpdate', $module['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="moduleName" class="col-md-4 col-form-label text-md-right">Nombre del módulo</label>
        <div class="col-md-6">
            <input id="moduleName" type="text" class="form-control" name="moduleName" required autofocus value="{{$module['moduleName']}}">
        </div>
    </div>


    <div class="form-group row">
        <label for="moduleDetail" class="col-md-4 col-form-label text-md-right">Detalle del módulo</label>
        <div class="col-md-6">
            <input id="moduleDetail" type="text" class="form-control" name="moduleDetail" autofocus value="{{$module['moduleDetail']}}">
        </div>
    </div>
    <div class="form-group row">
        <label for="moduleCode" class="col-md-4 col-form-label text-md-right">Código del módulo</label>
        <div class="col-md-6">
            <input id="moduleCode" type="text" class="form-control" name="moduleCode" autofocus value="{{$module['moduleCode']}}">
        </div>
    </div>
    <div class="form-group row">
        <label for="moduleCC" class="col-md-4 col-form-label text-md-right">CC del módulo</label>
        <div class="col-md-6">
            <input id="moduleCC" type="text" class="form-control" name="moduleCC" autofocus value="{{$module['moduleCC']}}">
        </div>
    </div>


    <div class="form-group row align-items-center">
        <label for="hasParent" class="col-md-4 col-form-label text-md-right">Tiene padre?</label>
        <div class="col-md-6 form-check">

            <div class="pretty p-switch p-fill">
                <input type="checkbox" name="hasParent" id="hasParent"
                value="@if ($module['moduleParentId'] != null) si @elseif ($module['moduleParentId'] == null) no @endif" class="form-control"
                @if ($module['moduleParentId'] != null) checked @endif/>
                <div class="state p-success">
                  <label id="hasParentLabel">@if ($module['moduleParentId'] != null) Si @elseif ($module['moduleParentId'] == null) No @endif</label>
                </div>
              </div>
        </div>
    </div>

    <div class="form-group row" id="parentModule"
    @if ($module['moduleParentId'] != null) style="visibility:visible"
    @elseif ($module['moduleParentId'] == null) style="visibility:hidden"
    @endif>
        <label for="moduleParentId" class="col-md-4 col-form-label text-md-right">Módulo padre</label>

        <div class="col-md-6">
            <select class="form-control" id="moduleParentId" name="moduleParentId">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($modulesPadres as $modulePadre)
                  <!-- Modulo no puede ser su propio hijo -->
                  @if ($modulePadre['id'] != $module['id'])
                  <!-- Si es un hijo, seleccionar su padre -->
                    <option value="{{$modulePadre['id']}}"
                    @if ($modulePadre['id'] == $module['moduleParentId']) selected @endif>
                      {{$modulePadre['moduleName']}}
                    </option>
                  @endif

                @endforeach
            </select>
        </div>
    </div>


    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Editar modulo
            </button>
            <a class="btn btn-secondary" href="{{route('parameterization.modules')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>

<script src="{{ asset('js/components/toggleModuleParents.js')}}"></script>
@endsection
