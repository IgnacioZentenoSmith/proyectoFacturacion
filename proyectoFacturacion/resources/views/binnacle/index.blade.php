@extends('binnacle.layout')
@section('binnacleContent')

<div class="row justify-content-center">
  <div class="col-auto">
    <div class="col-12">

    </div>

    <div class="table-responsive">
      <table id="btTable" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
        data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
        data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
        data-server-sort="false">
        <thead>
          <tr>
            <th scope="col" data-field="ID" data-sortable="true">ID</th>
            <th scope="col" data-field="userName" data-sortable="true">Nombre usuario</th>
            <th scope="col" data-field="userEmail" data-sortable="true">Email usuario</th>
            <th scope="col" data-field="created_at" data-sortable="true">Fecha</th>
            <th scope="col" data-field="binnacle_action" data-sortable="true">Acción</th>
            <th scope="col" data-field="binnacle_tableName" data-sortable="true">Nombre tabla</th>
            <th scope="col" data-field="binnacle_tableId" data-sortable="true">ID alterado</th>
            <!-- <th scope="col" data-field="binnacle_tablePreValues" data-sortable="true">Valores anteriores</th> -->
            <!-- <th scope="col" data-field="binnacle_tablePostValues" data-sortable="true">Valores posteriores</th> -->
            <th scope="col" data-field="action" data-sortable="true">Acciónes</th>


          </tr>
        </thead>
        <tbody>
          @foreach ($binnacles as $binnacle)
            <tr>
              <td>{{$binnacle['id']}}</td>
              <td>{{$binnacle['userName']}}</td>
              <td>{{$binnacle['userEmail']}}</td>
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

