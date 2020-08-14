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
                  <input id="inputPeriodo" type="month" class="form-control" name="inputPeriodo" required value="{{$periodo}}">
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
      <form method="POST" id="inputDocumentType" action="{{ route('billings.generateDocuments', $periodo) }}">
          @csrf
          <div class="form-group row">
              <div class="col-md-6">
                    <button type="submit" class="btn btn-secondary btn-lg">Generar facturas</button>
              </div>
          </div>
      </form>
    </div>

    <div class="table-responsive">
      <table id="btTable" class="table table-hover w-auto text-nowrap btTable" data-show-export="true"
        data-pagination="true" data-click-to-select="true" data-show-columns="true" data-sortable="true"
        data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right"
        data-server-sort="false">
        <thead>
          <tr>
            <th scope="col" data-field="contractNumber" data-sortable="true">Contrato</th>
            <th scope="col" data-field="tributarydocuments_period" data-sortable="true">Periodo</th>
            <th scope="col" data-field="tributarydocuments_documentType" data-sortable="true">Documento</th>
            <th scope="col" data-field="tributarydocuments_totalAmount" data-sortable="true">Neto</th>
            <th scope="col" data-field="tributarydocuments_IVA" data-sortable="true">IVA</th>
            <th scope="col" data-field="tributarydocuments_AmountIVA" data-sortable="true">Total</th>
            <th scope="col" data-field="Accion" data-sortable="true">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($documentosTributarios as $documentosTributario)
            <tr @if ($documentosTributario['tributarydocuments_totalAmount'] == 0) class="bg-light" @endif>
              <td>{{$documentosTributario['documentoTributario_contractName']}}</td>
              <td>{{$documentosTributario['tributarydocuments_period']}}</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_documentType']}}</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_totalAmount']}} UF</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_tax']}}%</td>
              <td class="text-right">{{$documentosTributario['tributarydocuments_totalAmountTax']}} UF</td>
              <td>

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu_acciones{{$documentosTributario['id']}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Acciones
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu_acciones{{$documentosTributario['id']}}">
                        @if(in_array(7, $authPermisos))
                <a @if ($documentosTributario['tributarydocuments_totalAmount'] == 0) class="dropdown-item disabled"
                @else class="dropdown-item" href="{{ route('billings.paymentDetails', $documentosTributario['id']) }}" @endif
                role="button">Ver detalles</a>
                @endif

                @if(in_array(7, $authPermisos))
                <div class="dropdown-divider"></div>
                    <a @if ($documentosTributario['tributarydocuments_documentType'] != 'Factura' || $documentosTributario['tributarydocuments_totalAmount'] == 0) class="dropdown-item disabled"
                    @else class="dropdown-item" href="{{ route('billings.redistribute', $documentosTributario['id']) }}" @endif
                    role="button">Redistribución</a>
                @endif

                @if(in_array(7, $authPermisos))
                <div class="dropdown-divider"></div>
                  <form style="display: inline-block;" action="{{ route('billings.documentDestroy', $documentosTributario['id']) }}"
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
<script src="{{ asset('js/components/getCurrentDate.js')}}"></script>
@endsection
