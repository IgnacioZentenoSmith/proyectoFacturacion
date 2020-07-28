@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">Home</div>

                <div class="card-body">



                    <div class="table-responsive">
                        <table id="tablaFpena" class="table table-hover w-auto text-nowrap" data-show-export="true"
                            data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
                            data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
                            data-server-sort="false">
                            <thead>
                                <tr>

                                    <th scope="col" data-field="id_holding" data-sortable="true">id_holding</th>
                                    <th scope="col" data-field="nombre_holding" data-sortable="true">nombre_holding</th>
                                    <th scope="col" data-field="id_razon_social" data-sortable="true">id_razon_social</th>
                                    <th scope="col" data-field="rut_razon_social" data-sortable="true">rut_razon_social</th>
                                    <th scope="col" data-field="nombre_razon_social" data-sortable="true">nombre_razon_social</th>
                                    <th scope="col" data-field="n_contrato" data-sortable="true">n_contrato</th>
                                    <th scope="col" data-field="modulo_base_contrato" data-sortable="true">modulo_base_contrato</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($razonesSociales as $razonSocial)
                                <tr>
                                    <td>{{$razonSocial['id_holding']}}</td>
                                    <td>{{$razonSocial['nombre_holding']}}</td>
                                    <td>{{$razonSocial['id_razon_social']}}</td>
                                    <td>{{$razonSocial['rut_razon_social']}}</td>
                                    <td>{{$razonSocial['nombre_razon_social']}}</td>

                                    <td>{{$razonSocial['n_contrato']}}</td>
                                    <td>{{$razonSocial['modulo_base_contrato']}}</td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaFpena').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });

</script>
@endsection
