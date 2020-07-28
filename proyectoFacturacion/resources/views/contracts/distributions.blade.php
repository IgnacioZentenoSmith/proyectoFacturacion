@extends('contracts.layout')
@section('contractsContent')

<div class="row justify-content-center">
    <div class="col-auto">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Distribuciones de cobro del contrato: <strong>{{$contract['contractsNombre']}}</strong> de número: <strong>{{$contract['contractsNumero']}}</strong>
            </div>

            <div class="form-group row">
                <label for="contractDistribution_massAssign" class="col-auto col-form-label">Asignar distribución:</label>

                <div class="col-md-4">
                    <select class="form-control" id="contractDistribution_massAssign" name="contractDistribution_massAssign" onchange="getMassAssign(this);">
                        <option value="No asignado" selected>No asignado</option>
                        <option value="Porcentaje">Porcentaje</option>
                        <option value="Unidad de cobro">Unidad de cobro</option>
                    </select>
                </div>
            </div>

        </div>
        <form method="POST" action="{{route('contracts.distributionsUpdate', $contract['id'])}}">
            @csrf
            {{ method_field('PUT') }}
            <div class="table-responsive">
                <table id="tablaDistributions" class="table table-hover w-auto text-nowrap" data-show-export="true"
                    data-click-to-select="true" data-show-columns="true" data-sortable="true"
                    data-search="true" data-live-search="true" data-buttons-align="left" data-search-align="right">
                    <thead>
                        <tr>
                            <th scope="col" data-field="ID" data-sortable="true">ID</th>
                            <th scope="col" data-field="contractDistribution_clientName" data-sortable="true">Razon social</th>
                            <th scope="col" data-field="contractDistribution_type" data-sortable="true">Tipo de distribución</th>
                            <th scope="col" data-field="contractDistribution_percentage" data-sortable="true">Porcentaje de distribución</th>
                            <th scope="col" data-field="contractDistribution_discount" data-sortable="true">Porcentaje de descuento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contractDistributions as $contractDistribution)
                        <tr>
                            <td>
                            {{$contractDistribution['id']}}
                                <input type="hidden" name="contractDistribution_id[]" required
                                value="{{$contractDistribution['id']}}">
                            </td>
                            <td>{{$contractDistribution['contractDistribution_clientName']}}</td>

                            <td>
                                <input id="contractDistribution_type[{{$contractDistribution['id']}}]" type="text" class="form-control" name="contractDistribution_type[]"
                                required readonly value="{{$contractDistribution['contractDistribution_type']}}">

                            </td>
                            <td>
                                <input id="contractDistribution_percentage[{{$contractDistribution['id']}}]" type="number" onchange="getPercentage(this);"
                                class="form-control" name="contractDistribution_percentage[]" step="0.01" @if($contractDistribution['contractDistribution_type'] != "Porcentaje") readonly @endif
                                value="{{$contractDistribution['contractDistribution_percentage']}}">
                            </td>
                            <td>
                                <input id="contractDistribution_discount[{{$contractDistribution['id']}}]" type="number" onchange="getDiscount(this);"
                                class="form-control" name="contractDistribution_discount[]" step="0.01"
                                value="{{$contractDistribution['contractDistribution_discount']}}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-group row" id="totalPercentage" style="display: none">
                <label for="contractDistribution_totalPercentage" class="col-auto col-form-label">Porcentaje total</label>

                <div class="col-md-4">
                    <input id="contractDistribution_totalPercentage" type="number"
                        class="form-control" name="contractDistribution_totalPercentage" step="0.01" readonly
                        value="0">
                </div>
            </div>

            <input type="hidden" id="distributionsTableLength" name="distributionsTableLength" required value="0">
            <div class="form-group row">
                <input type="hidden" id="distributionsType" name="distributionsType" required>
            <div class="form-group row">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    //Inicializa la tabla "detalles" del dashboard
    $('#tablaDistributions').bootstrapTable({
        pageSize: 100,
        exportDataType: 'all',
    });
    const distributionsTableLength = $('#tablaDistributions').bootstrapTable('getData').length;
    document.getElementById('distributionsTableLength').value = distributionsTableLength;

    function getMassAssign(massAssignInput) {
        let massValue = massAssignInput.value;
        //NO ASIGNADO
        if (massValue === "No asignado") {
            //Asignar a los tipos
            document.getElementsByName('contractDistribution_type[]').forEach(input => {
                input.value = "No asignado";
            });
            //Asignar a los porcentajes
            MassAssignPercentage(true, 0);
            getTotalPorcentaje();
            toggleTotalPercentage("none");
            document.getElementById("distributionsType").value = "No asignado";
        //PORCENTAJE
        } else if (massValue === "Porcentaje") {
            //Asignar a los tipos
            document.getElementsByName('contractDistribution_type[]').forEach(input => {
                input.value = "Porcentaje";
            });
            //Asignar a los porcentajes
            const equalPercentage = Math.floor(10000/distributionsTableLength)/100;
            MassAssignPercentage(false, equalPercentage);
            getTotalPorcentaje();
            toggleTotalPercentage("block");
            document.getElementById("distributionsType").value = "Porcentaje";
        //UNIDAD DE COBRO
        } else if (massValue === "Unidad de cobro") {
            //Asignar a los tipos
            document.getElementsByName('contractDistribution_type[]').forEach(input => {
                input.value = "Unidad de cobro";
            });
            //Asignar a los porcentajes
            MassAssignPercentage(true, 0);
            getTotalPorcentaje();
            toggleTotalPercentage("none");
            document.getElementById("distributionsType").value = "Unidad de cobro";
        }
    }

    function getPercentage(percentageInput) {
        //Entradas no validas
        toggleTotalPercentage("block");
        if (parseFloat(percentageInput.value) > 100) {
            percentageInput.value = parseFloat(0);
        }
        else if (parseFloat(percentageInput.value) < 0) {
            percentageInput.value = parseFloat(0);
        }
        //Entradas validas
        else {
            if (getTotalPorcentaje() > 100) {
                percentageInput.value = parseFloat(0);
                getTotalPorcentaje();
            }
            else if (getTotalPorcentaje() < 0) {
                percentageInput.value = parseFloat(0);
                getTotalPorcentaje();
            }
        }
    }

    function MassAssignPercentage(booleanValue, percentageValue) {
        document.getElementsByName('contractDistribution_percentage[]').forEach(input => {
                input.readOnly = booleanValue;
                input.value = percentageValue;
            });
    }
    function getTotalPorcentaje() {
        let totalPercentage = 0;
        document.getElementsByName('contractDistribution_percentage[]').forEach(input => {
                totalPercentage += parseFloat(input.value);
            });
        document.getElementById('contractDistribution_totalPercentage').value = parseFloat(totalPercentage).toFixed(2);
        return totalPercentage;
    }

    function toggleTotalPercentage(displayValue) {
        //"block", "none"
        var toggleElement = document.getElementById("totalPercentage");
        toggleElement.style.display = displayValue;
    }

    //Evitar que ponga valores incorrectos
    function getDiscount(discountInput) {
        if (parseFloat(discountInput.value) > 100) {
            discountInput.value = parseFloat(0);
        }
        else if (parseFloat(discountInput.value) < 0) {
            discountInput.value = parseFloat(0);
        }
    }
    getTotalPorcentaje();

    //Asignar el valor de los tipos al selector
    document.getElementsByName('contractDistribution_type[]').forEach(input => {
        document.getElementById('contractDistribution_massAssign').value = input.value;
        document.getElementById("distributionsType").value = input.value;
    });
</script>
@endsection
