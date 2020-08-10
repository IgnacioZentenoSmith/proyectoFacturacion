@extends('admin.layout')
@section('adminContent')
<div class="row justify-content-center">
    <div class="col-auto">
        <div class="table-responsive">
            <table id="tablaAdmin" class="table table-hover w-auto text-nowrap btTable" data-show-export="true" data-pagination="true"
                data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
                data-live-search="true" data-buttons-align="left" data-search-align="right" data-server-sort="false">
                <thead>
                    <tr>
                        <th scope="col" data-field="Nombre" data-sortable="true">Nombre</th>
                        <th scope="col" data-field="Email" data-sortable="true">Email</th>
                        <th scope="col" data-field="Role" data-sortable="true">Rol</th>
                        <th scope="col" data-field="Status" data-sortable="true">Status</th>
                        <th scope="col" data-field="binnacleNotifications" data-sortable="true">Notificaciones</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{$usuario['name']}}</td>
                        <td>{{$usuario['email']}}</td>
                        <td>{{$usuario['role']}}</td>
                        <td>
                            @if ($usuario['status'] == 'Activo')
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if ($usuario['binnacleNotifications'])
                               <span class="badge badge-success">Si</span>
                           @else
                               <span class="badge badge-dark">No</span>
                           @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu_acciones{{$usuario['id']}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$usuario['id']}}">


                                    @if(in_array(6, $authPermisos))
                                        <a class="dropdown-item" href="{{ route('admin.edit', $usuario['id']) }}"
                                        role="button">Editar</a>
                                    @endif

                                    @if(in_array(7, $authPermisos))
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.editPermisos', $usuario['id']) }}"
                                    role="button">Permisos</a>
                                @endif
                                @if(in_array(9, $authPermisos))
                                <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.changeStatus', $usuario['id']) }}"
                                        method="post">
                                        @csrf
                                        @method('POST')
                                        <button class="dropdown-item" type="submit">
                                            @if ($usuario['status'] == 'Activo') Desactivar
                                            @else Activar
                                            @endif
                                        </button>
                                    </form>
                                @endif
                                @if(in_array(9, $authPermisos))
                                <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.resendVerification', $usuario['id']) }}"
                                        method="post">
                                        @csrf
                                        @method('POST')
                                        <button class="dropdown-item" @if ($usuario['email_verified_at']) disabled @endif type="submit">
                                            Reenviar verificación
                                        </button>
                                    </form>
                                @endif
                                @if(in_array(8, $authPermisos))
                                <div class="dropdown-divider"></div>
                                    <form action="{{ route('admin.destroy', $usuario['id']) }}"
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
@endsection
