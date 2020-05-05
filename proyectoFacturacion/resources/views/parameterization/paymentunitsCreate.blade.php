@extends('parameterization.layout')
@section('parameterizationContent')


<form method="POST" action="{{route('parameterization.paymentunitsStore')}}">
    @csrf
    <div class="form-group row">
        <label for="payment_units" class="col-md-4 col-form-label text-md-right">Nombre de la unidad de pago</label>
        <div class="col-md-6">
            <input id="payment_units" type="text" class="form-control" name="payment_units" required autofocus>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-primary">
                Crear unidad de pago
            </button>
            <a class="btn btn-secondary" href="{{route('parameterization.paymentunits')}}" role="button">Cancelar</a>
        </div>
    </div>
</form>
@endsection
