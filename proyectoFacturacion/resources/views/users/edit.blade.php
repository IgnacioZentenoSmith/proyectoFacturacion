@extends('layouts.app')

@section('content')
<div class="card shadow">
    <h5 class="card-header">Editar usuario</h5>
    <div class="card-body">
        <h5 class="card-title">Objetivo</h5>
        <p class="card-text">Modulo encargado de editar los datos no criticos del usuario.</p>

        <br>
        <hr>

      <?php echo print_r($usuario); ?>


  


    </div>
</div>
@endsection
