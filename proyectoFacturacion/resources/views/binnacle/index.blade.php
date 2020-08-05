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
            <th scope="col" data-field="binnacle_tablePreValues" data-sortable="true">Valores anteriores</th>
            <th scope="col" data-field="binnacle_tablePostValues" data-sortable="true">Valores posteriores</th>


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
              <td>{{$binnacle['binnacle_tablePreValues']}}</td>


            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="{{ asset('js/components/initBTtables.js')}}"></script>
@endsection

