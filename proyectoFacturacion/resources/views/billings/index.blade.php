@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
  <div class="col-auto">
    <div class="col-12">
      <form method="GET" id="inputPeriodoForm" action="{{ route('billings.index', $periodo) }}">
          @csrf
          {{ method_field('GET') }}
          <div class="form-group row">
              <div class="col-md-3">
                  <input id="inputPeriodo" type="month" class="form-control" name="inputPeriodo" required value="{{$periodo}}" onchange="getCurrentDate(this);">
              </div>
          </div>

          <div class="form-group row">
              <div class="col-md-3">
                  <button type="submit" class="btn btn-primary">
                      Seleccionar período
                  </button>
              </div>
          </div>
      </form>
      <form method="POST" id="inputDocumentType" action="{{ route('billings.generateDocuments', [$periodo, 'Factura']) }}">
          @csrf
          <div class="form-group row">
              <div class="col-md-6">
                    <button type="submit" class="btn btn-secondary btn-lg">Generar facturas</button>
              </div>
          </div>
      </form>
    </div>

    <div class="table-responsive">
      <table id="tablaBillings" class="table table-hover w-auto text-nowrap" data-show-export="true"
        data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
        data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
        data-server-sort="false">
        <thead>
          <tr>
            <th scope="col" data-field="ID" data-sortable="true">ID</th>
            <th scope="col" data-field="clientName" data-sortable="true">Cliente</th>
            <th scope="col" data-field="contractNumber" data-sortable="true">Contrato</th>
            <th scope="col" data-field="tributarydocuments_period" data-sortable="true">Periodo</th>
            <th scope="col" data-field="tributarydocuments_documentType" data-sortable="true">Tipo de documento</th>
            <th scope="col" data-field="tributarydocuments_totalAmount" data-sortable="true">Monto</th>
            <th scope="col" data-field="tributarydocuments_IVA" data-sortable="true">IVA</th>
            <th scope="col" data-field="tributarydocuments_AmountIVA" data-sortable="true">Monto total</th>
            <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($documentosTributarios as $documentosTributario)
            <tr>
              <td>{{$documentosTributario['id']}}</td>
              <td>{{$documentosTributario['documentoTributario_clientName']}}</td>
              <td>{{$documentosTributario['documentoTributario_contractName']}}</td>
              <td>{{$documentosTributario['tributarydocuments_period']}}</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_documentType']}}</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_totalAmount']}} UF</td>
              <td class="text-right">{{$documentosTributario['documentoTributario_IVA']}}%</td>
              <td class="text-right">{{$documentosTributario['documentoTributario_MontoTotalIVA']}} UF</td>
              <td>
              <button class="btn btn-primary" disabled>Distribución de cobro</button>
                <a class="btn btn-secondary"
                  role="button" disabled>Ver detalles</a>
                  <form style="display: inline-block;" action=""
                    method="">

                    <button class="btn btn-warning" type="submit" disabled>Nota de crédito</button>
                  </form>
                  
                  <form style="display: inline-block;" action="{{ route('billings.documentDestroy', $documentosTributario['id']) }}"
                    method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Eliminar(DEBUG)</button>
                  </form>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
  </div>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaBillings').bootstrapTable({
        pageSize: 25,
        exportDataType: 'all',
    });

    function getCurrentDate(inputDate) {
        //Saca el valor del formulario de la fecha
        let formAction = document.getElementById('inputPeriodoForm').action;
        //Elimina su fecha inicial
        formAction = formAction.slice(0, -7);
        //Agrega la fecha del input
        formAction = formAction + inputDate.value;
        document.getElementById('inputPeriodoForm').action = formAction;
    }
</script>
@endsection