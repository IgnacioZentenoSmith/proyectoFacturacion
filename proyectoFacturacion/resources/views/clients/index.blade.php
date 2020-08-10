@extends('clients.layout')
@section('clientContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaClients" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="clientRazonSocial" data-sortable="true">Nombre</th>
                        <th scope="col" data-field="ejecutivoNombre" data-sortable="true">Ejecutivo</th>
                        <th scope="col" data-field="clientContactEmail" data-sortable="true">Email de contacto</th>

                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{$cliente['clientRazonSocial']}}</td>
                        <td>{{$cliente['ejecutivoNombre']}}</td>
                        <td>{{$cliente['clientContactEmail']}}</td>
                        <td>

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenu_acciones{{$cliente['id']}}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$cliente['id']}}">


                                    <!-- ver detalle del holding -> sus clientes -->
                                    <a class="dropdown-item" href="{{ route('clients.childrenIndex', $cliente['id']) }}"
                                        role="button">Razones sociales <span
                                            class="badge badge-secondary">{{$cliente['clientChildrenCount']}}</span>
                                    </a>

                                    @if(in_array(10, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('clients.edit', $cliente['id']) }}"
                                        role="button">Editar</a>
                                    @endif
                                    @if(in_array(11, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <form style="display: inline-block;"
                                        action="{{ route('clients.destroy', $cliente['id']) }}" method="post">
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
@endsection
