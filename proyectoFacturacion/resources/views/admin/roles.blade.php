@extends('admin.layout')
@section('adminContent')


<div class="table-responsive">
    <table id="tablaAdministracionRoles" class="table table-hover w-auto text-nowrap" data-show-export="true" data-pagination="true"
        data-click-to-select="true" data-show-columns="true" data-sortable="true" data-search="true"
        data-live-search="true" data-buttons-align="left" data-search-align="right" data-server-sort="false">
        <thead>
            <tr>
                <th scope="col" data-field="Role" data-sortable="true">Rol</th>
                @foreach($acciones as $accion)
                <th scope="col" data-field="{{$accion['actionName']}}">{{$accion['actionName']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Administrador</td>
                @foreach($acciones as $accion)
                <td>
                  <div class="pretty p-switch p-fill">
                    <input type="checkbox" name="Administrador" value="{{$accion['actionName']}}"/>
                    <div class="state p-success">
                        <label>{{$accion['actionName']}}</label>
                    </div>
                  </div>
                </td>
                @endforeach
            </tr>

            <tr>
                <td>Vendedor</td>
                @foreach($acciones as $accion)
                <td>
                  <div class="pretty p-switch p-fill">
                    <input type="checkbox" name="Vendedor" value="{{$accion['actionName']}}"/>
                    <div class="state p-success">
                        <label>{{$accion['actionName']}}</label>
                    </div>
                  </div>
                </td>
                @endforeach
            </tr>

            <tr>
                <td>Ejecutivo</td>
                @foreach($acciones as $accion)
                <td>
                  <div class="pretty p-switch p-fill">
                    <input type="checkbox" name="Ejecutivo" value="{{$accion['actionName']}}"/>
                    <div class="state p-success">
                        <label>{{$accion['actionName']}}</label>
                    </div>
                  </div>
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>
<button class="btn btn-primary" onclick="ajaxSubmit()">Guardar</button>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaAdministracionRoles').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });
  function ajaxSubmit() {
    const selection = getSelection();

  }
  function getSelection() {
    const selected = [];
    $('#tablaAdministracionRoles input:checked').each(function() {
      selected.push({'rol': $(this).attr('name'), 'accion': $(this).attr('value')});
    });
    console.log(selected);
    return selected;
  }
</script>
@endsection
