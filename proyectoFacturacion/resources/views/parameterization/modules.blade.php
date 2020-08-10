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
                        <th scope="col" data-field="moduleName" data-sortable="true">Nombre</th>
                        <th scope="col" data-field="moduleParentId" data-sortable="true">Modulo principal</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>{{$module['moduleName']}}</td>
                        <td class="text-center">{{$module['moduleParentId']}}</td>

                        <td>

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenu_acciones{{$module['id']}}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$module['id']}}">


                                    @if(in_array(16, $authPermisos))
                                    <a class="dropdown-item"
                                        href="{{ route('parameterization.modulesEdit', $module['id']) }}"
                                        role="button">Editar</a>
                                    @endif
                                    @if(in_array(17, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <form style="display: inline-block;"
                                        action="{{ route('parameterization.modulesDestroy', $module['id']) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item" type="submit">Eliminar(DEBUG)</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
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
