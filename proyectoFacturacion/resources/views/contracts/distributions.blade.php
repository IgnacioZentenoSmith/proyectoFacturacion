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
                    <select class="form-control" id="contractDistribution_massAssign" name="contractDistribution_massAssign">
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
                                <input id="contractDistribution_percentage[{{$contractDistribution['id']}}]" type="number"
                                class="form-control" name="contractDistribution_percentage[]" step="0.01" @if($contractDistribution['contractDistribution_type'] != "Porcentaje") readonly @endif
                                value="{{$contractDistribution['contractDistribution_percentage']}}">
                            </td>
                            <td>
                                <input id="contractDistribution_discount[{{$contractDistribution['id']}}]" type="number"
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
                <div class="col-md-6 mt-4">
                    <input type="hidden" id="distributionsType" name="distributionsType" required>
                </div>

                <div class="col-md-6 mt-4">
                    <button type="submit" class="btn btn-primary float-right">
                        Guardar
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
<script src="{{ asset('js/components/distributions.js')}}"></script>
@endsection
