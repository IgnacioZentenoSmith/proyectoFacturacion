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
                        <th scope="col" data-field="ID" data-sortable="true">ID</th>
                        <th scope="col" data-field="Nombre" data-sortable="true">Nombre</th>
                        <th scope="col" data-field="Email" data-sortable="true">Email</th>
                        <th scope="col" data-field="Role" data-sortable="true">Rol</th>
                        <th scope="col" data-field="Status" data-sortable="true">Status</th>
                        <th scope="col" data-field="isVerified" data-sortable="true">Email verificado</th>
                        <th scope="col" data-field="Verified" data-sortable="true">Fecha verificación</th>
                        <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{$usuario['id']}}</td>
                        <td>{{$usuario['name']}}</td>
                        <td>{{$usuario['email']}}</td>
                        <td>{{$usuario['role']}}</td>
                        @if ($usuario['status'] == 'Activo')
                            <td class="bg-primary text-center text-white">Activo</td>
                        @else
                            <td class="bg-secondary text-center text-white">Inactivo</td>
                        @endif
                        @if ($usuario['email_verified_at'])
                            <td class="bg-success text-center text-white">Si</td>
                        @else
                            <td class="bg-info text-center text-white">No</td>
                        @endif
                        <td>{{$usuario['email_verified_at']}}</td>
                        <td>
                        @if(in_array(6, $authPermisos))
                            <a class="btn btn-secondary" href="{{ route('admin.edit', $usuario['id']) }}"
                            role="button">Editar</a>
                        @endif
                        @if(in_array(7, $authPermisos))
                            <a class="btn btn-warning" href="{{ route('admin.editPermisos', $usuario['id']) }}"
                            role="button">Permisos</a>
                        @endif
                        @if(in_array(9, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('admin.changeStatus', $usuario['id']) }}"
                                method="post">
                                @csrf
                                @method('POST')
                                <button class="btn btn-light" type="submit">
                                    @if ($usuario['status'] == 'Activo') Desactivar
                                    @else Activar
                                    @endif
                                </button>
                            </form>
                        @endif
                        @if(in_array(9, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('admin.resendVerification', $usuario['id']) }}"
                                method="post">
                                @csrf
                                @method('POST')
                                <button class="btn btn-warning" @if ($usuario['email_verified_at']) disabled @endif type="submit">
                                    Reenviar verificación
                                </button>
                            </form>
                        @endif
                        @if(in_array(8, $authPermisos))
                            <form style="display: inline-block;" action="{{ route('admin.destroy', $usuario['id']) }}"
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
