@extends('parameterization.layout')
@section('parameterizationContent')


<form method="POST" action="{{route('parameterization.modulesStore')}}">
    @csrf
    <div class="form-group row">
        <label for="moduleName" class="col-md-4 col-form-label text-md-right">Nombre del modulo</label>
        <div class="col-md-6">
            <input id="moduleName" type="text" class="form-control" name="moduleName" required autofocus>
        </div>
    </div>

    <div class="form-group row">
        <label for="hasParent" class="col-md-4 col-form-label text-md-right">Tiene padre?</label>
        <div class="col-md-6 form-check">
            <div class="pretty p-switch">
                <input type="radio" name="hasParent" checked value="no"
                    onchange="document.getElementById('parentModule').style.visibility = 'hidden';" />
                <div class="state p-success">
                    <label>No</label>
                </div>
            </div>

            <div class="pretty p-switch p-fill">
                <input type="radio" name="hasParent" value="si"
                    onchange="document.getElementById('parentModule').style.visibility = 'visible';" />
                <div class="state p-success">
                    <label>Si</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row" id="parentModule" style="visibility:hidden">
        <label for="moduleParentId" class="col-md-4 col-form-label text-md-right">Modulo padre</label>

        <div class="col-md-6">
            <select class="form-control" id="moduleParentId" name="moduleParentId">
                <option value="" selected>Ninguno seleccionado</option>
                @foreach($modulesPadres as $modulePadre)
                <option value="{{$modulePadre['id']}}">{{$modulePadre['moduleName']}}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear modulo
            </button>
            <a class="btn btn-secondary" href="{{route('parameterization.modules')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
