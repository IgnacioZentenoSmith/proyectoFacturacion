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
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="clientRazonSocial" data-sortable="true">Nombre del holding</th>
                        <th scope="col" data-field="ejecutivoNombre" data-sortable="true">Ejecutivo asociado</th>
                        <th scope="col" data-field="clientContactEmail" data-sortable="true">Email de contacto</th>
                        <th scope="col" data-field="clientPhone" data-sortable="true">Teléfono del holding</th>
                        <th scope="col" data-field="clientDirection" data-sortable="true">Dirección del holding</th>
                        <th scope="col" data-field="clientBusinessActivity" data-sortable="true">Giro del holding</th>
                        <th scope="col" data-field="clientChildrenCount" data-sortable="true">Cantidad de clientes</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{$cliente['id']}}</td>
                        <td>{{$cliente['clientRazonSocial']}}</td>
                        <td>{{$cliente['ejecutivoNombre']}}</td>
                        <td>{{$cliente['clientContactEmail']}}</td>
                        <td>{{$cliente['clientPhone']}}</td>
                        <td>{{$cliente['clientDirection']}}</td>
                        <td>{{$cliente['clientBusinessActivity']}}</td>
                        <td class="text-right">{{$cliente['clientChildrenCount']}}</td>
                        <td>
                            <!-- ver detalle del holding -> sus clientes -->
                            <a class="btn btn-primary" href="{{ route('clients.childrenIndex', $cliente['id']) }}"
                                role="button">Razones sociales</a>

                            @if(in_array(10, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('clients.edit', $cliente['id']) }}"
                                role="button">Editar</a>
                            @endif
                            @if(in_array(11, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('clients.destroy', $cliente['id']) }}"
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
@endsection
