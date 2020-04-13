@extends('admin.layout')
@section('adminContent')

<div class="table-responsive">
    <table id="tablaAdministracionUsers" class="table table-hover w-auto text-nowrap" data-show-export="true" data-pagination="true"
        data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
        data-live-search="true" data-buttons-align="left" data-search-align="right" data-server-sort="false">
        <thead>
            <tr>
                <th scope="col" data-field="Nombre" data-sortable="true">Nombre</th>
                <th scope="col" data-field="Email" data-sortable="true">Email</th>
                <th scope="col" data-field="Role" data-sortable="true">Rol</th>
                @foreach($acciones as $accion)
                <th scope="col" data-field="{{$accion['actionName']}}">{{$accion['actionName']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{$usuario['name']}}</td>
                <td>{{$usuario['email']}}</td>
                <td>{{$usuario['role']}}</td>
                @foreach($acciones as $accion)
                <td>
                  <div class="pretty p-switch p-fill">
                  @foreach($permisos as $permiso)
                    @if($permiso['idUser'] == $usuario['id'] && $permiso['idActions'] == $accion['idActions']) 
                      <input type="checkbox" name="{{$usuario['id']}}" value="{{$accion['actionName']}}" checked/>
                    @endif
                  @endforeach
                  <input type="checkbox" name="{{$usuario['id']}}" value="{{$accion['actionName']}}"/>
                    <div class="state p-success">
                        <label>{{$accion['actionName']}}</label>
                    </div>
                  </div>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<button class="btn btn-primary" onclick="ajaxSubmit()">Guardar</button>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaAdministracionUsers').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });

    function ajaxSubmit() {
        const selection = getSelection();
        console.log(selection);
        $.ajax({
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: 'admin/ajax/userUpdate',
            data: {'data': selection},
            success: (d) => {
                console.log(d);
            },
            error: (e) => {
                console.log(e);
            }
        });
    }

    function getSelection() {
        const selected = [];
        $('#tablaAdministracionUsers input:checked').each(function () {
            selected.push({
                'idUser': $(this).attr('name'),
                'accion': $(this).attr('value')
            });
        });
        return selected;
    }
    
</script>
@endsection
