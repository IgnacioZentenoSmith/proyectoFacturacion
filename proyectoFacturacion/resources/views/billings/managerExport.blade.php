@extends('billings.layout')
@section('billingsContent')

<div class="row justify-content-center">
  <div class="col-auto">
  <div class="col-12">
  <form method="GET" id="inputPeriodoForm" action="{{ route('billings.managerExport', $periodo) }}">
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
    <br>
    <div class="my-3">
        <div class="alert alert-info" role="alert">
            Ingrese el número <strong>inicial</strong> en que comienza el <strong>Número de Factura:</strong>
        <div class="form-group row">
            <div class="col-md-3">
                <input id="inputNumFact" type="number" class="form-control" name="inputNumFact">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-3">
                <button type="button" class="btn btn-secondary" onclick="assignNumeroFacturas()">
                    Asignar números de facturas
                </button>
            </div>
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
            {{-- <th scope="col" data-field="PERIODO_DE_FACTURA" data-sortable="true">PERÍODO DE FACTURA</th>
            <th scope="col" data-field="N_DE_FACTURA" data-sortable="true">N° DE FACTURA</th>
            <th scope="col" data-field="CLIENTE" data-sortable="true">CLIENTE</th>
            <th scope="col" data-field="RUT_A_FACTURAR" data-sortable="true">RUT A FACTURAR</th>
            <th scope="col" data-field="GIRO" data-sortable="true">GIRO</th>
            <th scope="col" data-field="TELEFONO" data-sortable="true">TELÉFONO</th>
            <th scope="col" data-field="ADMINISTRADOR_CLIENTE" data-sortable="true">ADMINISTRADOR CLIENTE</th>
            <th scope="col" data-field="EJECUTIVO_PLANOK" data-sortable="true">EJECUTIVO PLANOK</th>
            <th scope="col" data-field="MONTO_A_COBRAR" data-sortable="true">MONTO A COBRAR</th>
            <th scope="col" data-field="MODULOS_CONTRATADOS" data-sortable="true">MÓDULOS CONTRATADOS</th>
            <th scope="col" data-field="SUB MODULOS_CONTRATADOS" data-sortable="true">SUB MÓDULOS CONTRATADOS</th>
            <th scope="col" data-field="COMPOSICIÓN_DE_MONTO" data-sortable="true">COMPOSICIÓN DE MONTO</th>
            <th scope="col" data-field="RAZON_SOCIAL_A_FACTURAR" data-sortable="true">RAZÓN SOCIAL A FACTURAR</th>
            <th scope="col" data-field="DISTRIBUCIÓN_DE_COBRO" data-sortable="true">DISTRIBUCIÓN DE COBRO</th>
            <th scope="col" data-field="N_OC_CLIENTE" data-sortable="true">N° OC (CLIENTE)</th>
            <th scope="col" data-field="N_HES_CLIENTE" data-sortable="true">N° HES (CLIENTE)</th>
            <th scope="col" data-field="N_ESTADO_DE_PAGO_CLIENTE" data-sortable="true">N° ESTADO DE PAGO (CLIENTE)</th> --}}

            <th scope="col" data-field="TIPODOC" data-sortable="true">TIPODOC</th>
            <th scope="col" data-field="ESELECTR" data-sortable="true">ESELECTR</th>
    	    <th scope="col" data-field="HOLDING" data-sortable="true">HOLDING</th>
            <th scope="col" data-field="RUT_CLIE" data-sortable="true">RUT CLIE</th>
            <th scope="col" data-field="RUT FACT" data-sortable="true">RUT FACT</th>
            <th scope="col" data-field="FECHA_CREAC" data-sortable="true">FECHA</th>
            <th scope="col" data-field="NUMFACT" data-sortable="true">NUMFACT</th>
            <th scope="col" data-field="GRUPO" data-sortable="true">GRUPO</th>
            <th scope="col" data-field="FECHA_MES_EXTRA" data-sortable="true">FECHA</th>
            <th scope="col" data-field="MONEDA" data-sortable="true">MONEDA</th>
            <th scope="col" data-field="DESCTO" data-sortable="true">DESCTO</th>
            <th scope="col" data-field="TIPO_DESCUENTO" data-sortable="true">TIPO DESCUENTO</th>

            <th scope="col" data-field="CODIGO_PRODUCTO" data-sortable="true">CODIGO PRODUCTO</th>
            <th scope="col" data-field="CANTIDAD" data-sortable="true">CANTIDAD</th>
            <th scope="col" data-field="PRECIO" data-sortable="true">PRECIO</th>
            <th scope="col" data-field="DESCTO_ITEM" data-sortable="true">DESCTO ITEM</th>
            <th scope="col" data-field="BODEGA" data-sortable="true">BODEGA</th>
            <th scope="col" data-field="CTA" data-sortable="true">CTA</th>
            <th scope="col" data-field="CC" data-sortable="true">CC</th>
            <th scope="col" data-field="OBSERVACION" data-sortable="true">OBSERVACION</th>
            <th scope="col" data-field="OC" data-sortable="true">OC</th>
            <th scope="col" data-field="PATENTE" data-sortable="true">PATENTE</th>

            <th scope="col" data-field="ID_SERVICIO" data-sortable="true">ID SERVICIO</th>
            <th scope="col" data-field="SUCURSAL" data-sortable="true">SUCURSAL</th>
            <th scope="col" data-field="AFECTO" data-sortable="true">AFECTO</th>
            <th scope="col" data-field="NRO_REF" data-sortable="true">NRO REF (NCV)</th>
            <th scope="col" data-field="TIPO_DOC" data-sortable="true">TIPO DOC</th>
            <th scope="col" data-field="RAZON" data-sortable="true">RAZON</th>
            <th scope="col" data-field="GDV" data-sortable="true">GDV</th>
            <th scope="col" data-field="REBAJA_STOCK" data-sortable="true">REBAJA STOCK</th>
            <th scope="col" data-field="COMISION" data-sortable="true">COMISION</th>
            <th scope="col" data-field="CONCEPTO" data-sortable="true">CONCEPTO</th>

            <th scope="col" data-field="VENDEDOR" data-sortable="true">VENDEDOR</th>
            <th scope="col" data-field="COMISIONISTA" data-sortable="true">COMISIONISTA</th>
            <th scope="col" data-field="TRASLADO" data-sortable="true">TRASLADO</th>
            <th scope="col" data-field="COD_BODEGA" data-sortable="true">COD BODEGA</th>
            <th scope="col" data-field="FORMA_DE_PAGO" data-sortable="true">FORMA DE PAGO</th>
            <th scope="col" data-field="GLOSA_DE_PAGO" data-sortable="true">GLOSA DE PAGO</th>
            <th scope="col" data-field="GLOSACONT" data-sortable="true">GLOSACONT</th>
            <th scope="col" data-field="LOTE_DEL_PROD" data-sortable="true">LOTE DEL PROD</th>
            <th scope="col" data-field="VENC_LOTE" data-sortable="true">VENC LOTE</th>
            <th scope="col" data-field="NUMERO_SERIE" data-sortable="true">NUMERO SERIE</th>

            <th scope="col" data-field="FACTURABLE" data-sortable="true">FACTURABLE</th>
            <th scope="col" data-field="TRASLADO_ELECTRONICO" data-sortable="true">TRASLADO ELECTRONICO</th>
            <th scope="col" data-field="OBRA" data-sortable="true">OBRA</th>
            <th scope="col" data-field="NUMERO_OC" data-sortable="true">NUMERO_OC</th>
            <th scope="col" data-field="FECHA_OC" data-sortable="true">FECHA_OC</th>
            <th scope="col" data-field="VIGENCIA_OC" data-sortable="true">VIGENCIA_OC</th>
            <th scope="col" data-field="NUMERO_HES" data-sortable="true">NUMERO_HES</th>
            <th scope="col" data-field="FECHA_HES" data-sortable="true">FECHA_HES</th>
            <th scope="col" data-field="VIGENCIA_HES" data-sortable="true">VIGENCIA_HES</th>
            <th scope="col" data-field="NUMERO_GD" data-sortable="true">NUMERO_GD</th>
            <th scope="col" data-field="FECHA_GD" data-sortable="true">FECHA_GD</th>
            <th scope="col" data-field="NUMERO_CONTR" data-sortable="true">NUMERO_CONTR</th>

            <th scope="col" data-field="FECHA_CONTR" data-sortable="true">FECHA_CONTR</th>
            <th scope="col" data-field="NUMERO_PED" data-sortable="true">NUMERO_PED</th>
            <th scope="col" data-field="FECHA_PED" data-sortable="true">FECHA_PED</th>
            <th scope="col" data-field="ENTREGA_DIREC" data-sortable="true">ENTREGA_DIREC</th>
            <th scope="col" data-field="ENTREGA_COMUNA" data-sortable="true">ENTREGA_COMUNA</th>
            <th scope="col" data-field="ENTREGA_CIUDAD" data-sortable="true">ENTREGA_CIUDAD</th>
        </tr>

        </thead>
        <tbody>
          @foreach ($dataFinal as $manager)
            <tr>
              {{-- TIPODOC --}}
              <td>1</td>
              {{-- ESELECTR --}}
              <td>1</td>
              {{-- HOLDING --}}
	          <td>{{$manager->holdingRazonSocial}}</td>
              {{-- RUT_CLIE --}}
              <td>{{$manager->clientRUT}}</td>
              {{-- RUT_FACT --}}
              <td>{{$manager->clientRUT}}</td>
              {{-- FECHA_CREAC --}}
              <td>{{date ("d-m-Y", strtotime($manager->created_at))}}</td>
              {{-- NUMFACT --}}
              <td></td>
              {{-- GRUPO --}}
              <td>{{$manager->invoices_grupo}}</td>
              {{-- FECHA_MES_EXTRA --}}
              <td>{{date ("d-m-Y", strtotime("+1 month", strtotime($manager->created_at)))}}</td>
              {{-- MONEDA --}}
              <td>UF</td>
              {{-- DESCTO --}}
              <td>0</td>
              {{-- TIPO_DESCUENTO --}}
              <td>1</td>

              {{-- CODIGO_PRODUCTO --}}
              <td>
                  @if ($manager->idModule == 1) 31101GCI
                  @elseif ($manager->idModule  == 2) 31201PVI
                  @elseif ($manager->idModule  == 3) 31301DTP
                  @elseif ($manager->idModule  == 4) 31601ET
                  @elseif ($manager->idModule  == 12) 31401LIC
                  @endif
              </td>
              {{-- CANTIDAD --}}
              <td>{{$manager->invoices_neto}}</td>
              {{-- PRECIO --}}
              <td>1</td>
              {{-- DESCTO_ITEM --}}
              <td>0</td>
              {{-- BODEGA --}}
              <td>b1</td>
              {{-- CTA --}}
              <td>310101</td>
              {{-- CC --}}
              <td>
                @if ($manager->idModule == 1) 1005
                @elseif ($manager->idModule == 2) 1002
                @elseif ($manager->idModule == 3) 1021
                @elseif ($manager->idModule == 4) 1006
                @elseif ($manager->idModule == 12) 1030
                @endif
              </td>
              {{-- OBSERVACION --}}
              <td>
                  ^^
                  <br>
                  Periodo del 26-{{$periodoManager1}} al 25-{{$periodoManager2}}^
                  <br>
                 {{$manager->contractPaymentDetails_description}}
              </td>
              {{-- OC --}}
              <td></td>
              {{-- PATENTE --}}
              <td></td>

              {{-- ID_SERVICIO --}}
              <td></td>
              {{-- SUCURSAL --}}
              <td>1</td>
              {{-- AFECTO --}}
              <td>1</td>
              {{-- NRO_REF --}}
              <td></td>
              {{-- TIPO_DOC --}}
              <td></td>
              {{-- RAZON --}}
              <td></td>
              {{-- GDV --}}
              <td></td>
              {{-- REBAJA_STOCK --}}
              <td></td>
              {{-- COMISION --}}
              <td>0</td>
              {{-- CONCEPTO --}}
              <td></td>

              {{-- VENDEDOR --}}
              <td></td>
              {{-- COMISIONISTA --}}
              <td></td>
              {{-- TRASLADO --}}
              <td></td>
              {{-- COD_BODEGA --}}
              <td>0</td>
              {{-- FORMA_DE_PAGO --}}
              <td></td>
              {{-- GLOSA_DE_PAGO --}}
              <td>1</td>
              {{-- GLOSACONT --}}
              <td>1</td>
              {{-- LOTE_DEL_PROD --}}
              <td></td>
              {{-- VENC_LOTE --}}
              <td></td>
              {{-- NUMERO_SERIE --}}
              <td></td>

              {{-- FACTURABLE --}}
              <td></td>
              {{-- TRASLADO_ELECTRONICO --}}
              <td></td>
              {{-- OBRA --}}
              <td></td>
              {{-- NUMERO_OC --}}
              <td>{{$manager->invoices_numeroOC}}</td>
              {{-- FECHA_OC --}}
              <td>{{$manager->invoices_fechaOC}}</td>
              {{-- VIGENCIA_OC --}}
              <td>{{$manager->invoices_vigenciaOC}}</td>
              {{-- NUMERO_HES --}}
              <td>{{$manager->invoices_numeroHES}}</td>
              {{-- FECHA_HES --}}
              <td>{{$manager->invoices_fechaHES}}</td>
              {{-- VIGENCIA_HES --}}
              <td>{{$manager->invoices_vigenciaHES}}</td>
              {{-- NUMERO_GD --}}
              <td>{{$manager->contractsNumeroCliente}}</td>
              {{-- FECHA_GD --}}
              <td></td>
              {{-- NUMERO_CONTR --}}
              <td></td>

              {{-- FECHA_CONTR --}}
              <td></td>
              {{-- NUMERO_PED --}}
              <td></td>
              {{-- FECHA_PED --}}
              <td></td>
              {{-- ENTREGA_DIREC --}}
              <td></td>
              {{-- ENTREGA_COMUNA --}}
              <td></td>
              {{-- ENTREGA_CIUDAD --}}
              <td></td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="{{ asset('js/components/initBTtables.js')}}"></script>
<script src="{{ asset('js/components/getCurrentDate.js')}}"></script>

<script>
function assignNumeroFacturas() {
    let numeroInicial = parseInt(document.getElementById('inputNumFact').value);
    if (Number.isNaN(numeroInicial) || numeroInicial < 0) {
        document.getElementById('inputNumFact').value = "";
    } else {

        const table = document.getElementById('btTable');
        const numRows = table.tBodies[0].rows.length;
        for (let i = 0; i < numRows; i++) {
            // HOLDING = 2, NUMFACT = 6, GRUPO = 7
            // holding: table.tBodies[0].rows[i].children[2].innerHTML;
            // numfact: table.tBodies[0].rows[i].children[6].innerHTML;
            // grupo: table.tBodies[0].rows[i].children[7].innerHTML;
            // Primera iteración
            if (i == 0) {
                table.tBodies[0].rows[i].children[6].innerHTML = numeroInicial;
            } else {
                // Si son el mismo holding
                if (table.tBodies[0].rows[i-1].children[2].innerHTML == table.tBodies[0].rows[i].children[2].innerHTML) {
                    // Si la iteración anterior tiene el mismo grupo
                    if (table.tBodies[0].rows[i-1].children[7].innerHTML == table.tBodies[0].rows[i].children[7].innerHTML) {
                        table.tBodies[0].rows[i].children[6].innerHTML = numeroInicial;
                    } else {
                        numeroInicial += 1;
                        table.tBodies[0].rows[i].children[6].innerHTML = numeroInicial;
                    }
                }
                // Si es otro holding
                else {
                    numeroInicial += 1;
                    table.tBodies[0].rows[i].children[6].innerHTML = numeroInicial;
                }
            }

        }
    }
}

</script>
@endsection
