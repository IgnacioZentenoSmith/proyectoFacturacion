@extends('parameterization.layout')
@section('parameterizationContent')


<form method="POST" action="{{route('parameterization.modulesUpdate', $module['id'])}}">
    @csrf
    {{ method_field('PUT') }}
    <div class="form-group row">
        <label for="moduleName" class="col-md-4 col-form-label text-md-right">Nombre del modulo</label>
        <div class="col-md-6">
            <input id="moduleName" type="text" class="form-control" name="moduleName" required autofocus value="{{$module['moduleName']}}">
        </div>
    </div>


  <div class="form-group row">
        <label for="hasParent" class="col-md-4 col-form-label text-md-right">Tiene padre?</label>
        <div class="col-md-6 form-check">
            <div class="pretty p-switch">
                <input type="radio" name="hasParent" @if ($module['moduleParentId'] == null) checked @endif value="no"
                    onchange="document.getElementById('parentClient').style.visibility = 'hidden';" />
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="hasParent" @if ($module['moduleParentId'] != null) checked @endif value="si"
                    onchange="document.getElementById('parentClient').style.visibility = 'visible';" />
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row" id="parentClient" 
    @if ($module['moduleParentId'] != null) style="visibility:visible"
    @elseif ($module['moduleParentId'] == null) style="visibility:hidden"
    @endif>
        <label for="moduleParentId" class="col-md-4 col-form-label text-md-right">Modulo padre</label>

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
@endsection
