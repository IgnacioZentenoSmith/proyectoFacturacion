@extends('contracts.layout')
@section('contractsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaContracts" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
                data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="idClient" data-sortable="true">Holding</th>
                        <th scope="col" data-field="idModule" data-sortable="true">Módulo</th>

                        <th scope="col" data-field="contractsManualContract" data-sortable="true">Manual</th>
                        <th scope="col" data-field="contractsRecepcionMunicipal" data-sortable="true">Recepción
                            municipal</th>

                        <th scope="col" data-field="contractsNumero" data-sortable="true">Número</th>
                        <th scope="col" data-field="contractsMoneda" data-sortable="true">Moneda</th>
                        <th scope="col" data-field="contract_clientEjecutivoName" data-sortable="true">Ejecutivo</th>
                        <th scope="col" data-field="contractsFecha" data-sortable="true">Fecha</th>
                        <th scope="col" data-field="contractsEstado" data-sortable="true">Estado</th>

                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                    <tr>
                        <td>{{$contract['contract_clientName']}}</td>
                        <td>{{$contract['contract_moduleName']}}</td>

                        <td><span
                                class="badge @if ($contract['contractsManualContract']) badge-warning @else badge-secondary @endif">Manual</span>
                        </td>
                        <td><span
                                class="badge @if ($contract['contractsRecepcionMunicipal']) badge-info @else badge-secondary @endif">Recepción
                                municipal</span></td>

                        <td>{{$contract['contractsNumero']}}</td>
                        <td>{{$contract['contractsMoneda']}}</td>
                        <td>{{$contract['contract_clientEjecutivoName']}}</td>
                        <td>{{$contract['contractsFecha']}}</td>
                        <td>
                            @if ($contract['contractsEstado'])
                            <span class="badge badge-success">Activo</span>
                            @else
                            <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        <td>

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    id="dropdownMenu_acciones{{$contract['id']}}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$contract['id']}}">


                                    @if(in_array(7, $authPermisos))
                                    <a class="dropdown-item"
                                        href="{{ route('contracts.distributions', $contract['id']) }}"
                                        role="button">Distribución de cobro</a>
                                    @endif

                                    @if(in_array(7, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('contracts.conditions', $contract['id']) }}"
                                        role="button">Ingresar condiciones</a>
                                    @endif

                                    {{-- quantities comentado
                            @if(in_array(7, $authPermisos))
                                <a class="btn btn-primary" href="{{ route('contracts.quantities', [$contract['id'], $periodo]) }}"
                                    role="button">Ingresar cantidades</a>
                                    @endif
                                    --}}

                                    @if(in_array(7, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item"
                                        href="{{ route('contracts.editContractStatus', $contract['id']) }}"
                                        role="button">
                                        @if ($contract['contractsEstado'] == false) Activar
                                        @else Desactivar @endif
                                    </a>
                                    @endif

                                    @if(in_array(10, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('contracts.edit', $contract['id']) }}"
                                        role="button">Editar</a>
                                    @endif
                                    @if(in_array(11, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <form style="display: inline-block;"
                                        action="{{ route('contracts.destroy', $contract['id']) }}" method="post">
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
