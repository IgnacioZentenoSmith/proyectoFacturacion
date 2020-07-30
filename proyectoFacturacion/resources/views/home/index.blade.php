@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">Home</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (Auth::user()->email === 'izenteno@planok.com')
                        <div class="alert alert-danger" role="alert">
                            Testing de APIs
                        </div>

                        <a class="btn btn-primary" role="button" href="{{ route('home.gci') }}">GCI</a>
                        <a class="btn btn-secondary" role="button" href="{{ route('home.pvi') }}">PVI</a>
                        <a class="btn btn-warning" role="button" href="{{ route('home.etdtp') }}">ET/DTP</a>
                        <a class="btn btn-success" role="button" href="{{ route('home.licita') }}">LICITA</a>
                        <a class="btn btn-light" role="button" href="{{ route('home.apiQuantities') }}">apiQuantities</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
