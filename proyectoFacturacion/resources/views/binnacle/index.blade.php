@extends('binnacle.layout')
@section('binnacleContent')

<div class="row justify-content-center">
  <div class="col-auto">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Filtros</h5>
            </div>
            <div class="card-body">


                <form method="POST" action="{{route('binnacle.index')}}">
                    @csrf

                    <div class="form-group row">
                        <label for="filter_usuarios" class="col-md-4 col-form-label text-md-right">Usuario: </label>
                        <div class="col-md-6">
                            <select class="form-control" id="filter_usuarios" name="filter_usuarios">
                                <option value="" selected>Ninguno seleccionado</option>
                                <!-- Permitir solo clientes padres desde Backend -->
                                @foreach($uniqueUsers as $uniqueUser)
                                <option value="{{$uniqueUser['idUser']}}">{{$uniqueUser['userName']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="filter_actions" class="col-md-4 col-form-label text-md-right">Acción: </label>
                        <div class="col-md-6">
                            <select class="form-control" id="filter_actions" name="filter_actions">
                                <option value="" selected>Ninguno seleccionado</option>
                                <!-- Permitir solo clientes padres desde Backend -->
                                @foreach($uniqueActions as $uniqueAction)
                                <option value="{{$uniqueAction['binnacle_action']}}">{{$uniqueAction['binnacle_action']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="filter_tables" class="col-md-4 col-form-label text-md-right">Tabla: </label>
                        <div class="col-md-6">
                            <select class="form-control" id="filter_tables" name="filter_tables">
                                <option value="" selected>Ninguno seleccionado</option>
                                <!-- Permitir solo clientes padres desde Backend -->
                                @foreach($uniqueTables as $uniqueTable)
                                <option value="{{$uniqueTable['binnacle_tableName']}}">{{$uniqueTable['binnacle_tableName']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="filter_fecha_desde" class="col-md-4 col-form-label text-md-right">Desde: </label>
                        <div class="col-md-6">
                            <input id="inputPeriodo" type="date" class="form-control" name="filter_fecha_desde">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="filter_fecha_hasta" class="col-md-4 col-form-label text-md-right">Hasta: </label>
                        <div class="col-md-6">
                            <input id="inputPeriodo" type="date" class="form-control" name="filter_fecha_hasta">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
      <table id="btTable" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
        data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
        data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
        data-server-sort="false">
        <thead>
          <tr>
            <th scope="col" data-field="userName" data-sortable="true">Usuario</th>
            <th scope="col" data-field="created_at" data-sortable="true">Fecha</th>
            <th scope="col" data-field="binnacle_action" data-sortable="true">Acción</th>
            <th scope="col" data-field="binnacle_tableName" data-sortable="true">Tabla</th>
            <th scope="col" data-field="binnacle_tableId" data-sortable="true">Identificador</th>
            <!-- <th scope="col" data-field="binnacle_tablePreValues" data-sortable="true">Valores anteriores</th> -->
            <!-- <th scope="col" data-field="binnacle_tablePostValues" data-sortable="true">Valores posteriores</th> -->
            <th scope="col" data-field="action" data-sortable="true">Acciónes</th>


          </tr>
        </thead>
        <tbody>
          @foreach ($binnacles as $binnacle)
            <tr>
              <td>{{$binnacle['userName']}}</td>
              <td>{{$binnacle['created_at']}}</td>
              <td>{{$binnacle['binnacle_action']}}</td>
              <td>{{$binnacle['binnacle_tableName']}}</td>
              <td>{{$binnacle['binnacle_tableId']}}</td>
              <td>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#binnacleModal_{{$binnacle['id']}}">
                    Ver detalles
                </button>
              </td>





            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    @foreach ($binnacles as $binnacle)
    <div class="modal fade" id="binnacleModal_{{$binnacle['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la bitácora {{$binnacle['id']}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                @if ($binnacle['binnacle_tablePreValues'] != null)

                <div class="table-responsive my-3">
                    <table class="table table-hover w-auto text-nowrap btTable"
                      data-click-to-select="true" data-show-columns="true" data-sortable="true"
                      data-search="true" data-live-search="true"  data-search-align="right">
                      <thead>
                        <tr>
                            @foreach ($binnacle['binnacle_tablePreValues'] as $preKey => $preValue)
                                <th scope="col" data-field="pre_{{$preKey}}" data-sortable="true">{{$preKey}}</th>
                            @endforeach
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            @foreach ($binnacle['binnacle_tablePreValues'] as $preKey => $preValue)
                                <td>{{$preValue}}</td>
                            @endforeach
                        </tr>
                      </tbody>
                    </table>
                </div>

                @endif
                @if ($binnacle['binnacle_tablePostValues'] != null)

                <div class="table-responsive my-3">
                    <table class="table table-hover w-auto text-nowrap btTable"
                      data-click-to-select="true" data-show-columns="true" data-sortable="true"
                      data-search="true" data-live-search="true"  data-search-align="right">
                      <thead>
                        <tr>
                            @foreach ($binnacle['binnacle_tablePostValues'] as $postKey => $postValue)
                                <th scope="col" data-field="post_{{$postKey}}" data-sortable="true">{{$postKey}}</th>
                            @endforeach
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            @foreach ($binnacle['binnacle_tablePostValues'] as $postKey => $postValue)
                                <td>{{$postValue}}</td>
                            @endforeach
                        </tr>
                      </tbody>
                    </table>
                </div>

                @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach



  </div>
</div>

<script src="{{ asset('js/components/initBTtables.js')}}"></script>
@endsection

