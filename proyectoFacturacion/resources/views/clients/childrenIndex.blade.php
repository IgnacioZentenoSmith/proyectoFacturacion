@extends('clients.layout')
@section('clientContent')

<div class="row justify-content-center">
    <div class="col-auto">

        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Razones sociales del holding: <strong>{{$holding['clientRazonSocial']}}</strong>
                @if ($holding['clientPhone'] != null)
                <br>
                Teléfono: <strong>{{$holding['clientPhone']}}</strong>
                @endif
                @if ($holding['clientDirection'] != null)
                <br>
                Dirección: <strong>{{$holding['clientDirection']}}</strong>
                @endif
                @if ($holding['clientBusinessActivity'] != null)
                <br>
                Giro: <strong>{{$holding['clientBusinessActivity']}}</strong>
                @endif
            </div>
            <a class="btn btn-primary" href="{{ route('clients.childrenCreate', $holding['id']) }}" role="button">Nueva
                razón social</a>

        </div>
        <div class="table-responsive">
            <table id="tablaChildren" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="clientRazonSocial" data-sortable="true">Razón social</th>
                        <th scope="col" data-field="clientRUT" data-sortable="true">RUT</th>
                        <th scope="col" data-field="clientContactEmail" data-sortable="true">Email de contacto</th>
                        <th scope="col" data-field="clientPhone" data-sortable="true">Teléfono</th>
                        <th scope="col" data-field="clientDirection" data-sortable="true">Dirección</th>
                        <th scope="col" data-field="clientBusinessActivity" data-sortable="true">Giro</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $child)
                    <tr>
                        <td>{{$child['clientRazonSocial']}}</td>
                        <td>{{$child['clientRUT']}}</td>
                        <td>{{$child['clientContactEmail']}}</td>
                        <td>{{$child['clientPhone']}}</td>
                        <td>{{$child['clientDirection']}}</td>
                        <td>{{$child['clientBusinessActivity']}}</td>
                        <td>

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenu_acciones{{$holding['id']}}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$holding['id']}}">

                                    @if(in_array(10, $authPermisos))
                                    <a class="dropdown-item"
                                        href="{{ route('clients.childrenEdit', [$holding['id'], $child['id']]) }}"
                                        role="button">Editar</a>
                                    @endif
                                    @if(in_array(11, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <form style="display: inline-block;"
                                        action="{{ route('clients.childrenDestroy', [$holding['id'], $child['id']]) }}"
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
