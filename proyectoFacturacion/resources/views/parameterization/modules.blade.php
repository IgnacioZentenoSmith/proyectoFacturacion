@extends('parameterization.layout')
@section('parameterizationContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaModules" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="moduleName" data-sortable="true">Nombre del modulo</th>
                        <th scope="col" data-field="moduleParentId" data-sortable="true">Modulo padre</th>
                        <th scope="col" data-field="created_at" data-sortable="true">Fecha creacion</th>
                        <th scope="col" data-field="updated_at" data-sortable="true">Fecha modificacion</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>{{$module['id']}}</td>
                        <td>{{$module['moduleName']}}</td>
                        <td class="text-center">{{$module['moduleParentId']}}</td>
                        <td class="text-center">{{$module['created_at']}}</td>
                        <td class="text-center">{{$module['updated_at']}}</td>

                        <td>
                            @if(in_array(16, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('parameterization.modulesEdit', $module['id']) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(17, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('parameterization.modulesDestroy', $module['id']) }}"
                                method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Eliminar(DEBUG)</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('js/components/initBTtables.js')}}"></script>
<script src="{{ asset('js/components/insecureSubmit.js')}}"></script>
@endsection
