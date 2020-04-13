<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

  	<!-- CSS STYLES -->
	<link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pretty-checkbox/3.0.0/pretty-checkbox.min.css" integrity="sha256-KCHcsGm2E36dSODOtMCcBadNAbEUW5m+1xLId7xgLmw=" crossorigin="anonymous" />
	<!-- JS -->
	<!-- Font Awesome JS -->
	<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
	<!-- jQuery JS -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
	<!-- Popper JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<!-- Bootstrap JS -->
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

	<!-- Bootstrap Tables-->
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.css">
	<script src="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.js"></script>
	<!-- Bootstrap Tables Export Extension -->
	<script src="https://unpkg.com/bootstrap-table@1.15.5/dist/extensions/export/bootstrap-table-export.min.js"></script>
	<!-- JS Exports plugin Bootstrap Tables-->
	<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
	<script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF/jspdf.min.js"></script>
	<script src="https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>

    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow">
            <div class="container-fluid">
                <ul class="navbar-nav mr-auto text-center">
                    <a class="navbar-brand">
                        Proyecto facturaciones
                    </a>
                </ul>   
                <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent"> -->
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav m-auto text-center">
                    @guest
                    @else
                        <li class="nav-item mx-2 {{ (request()->is('/')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('home.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-home"></i>
                                </span><br>
                                Home
                            </a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('admin')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('admin.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-shield-alt"></i>
                                </span><br>
                                Admin
                            </a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('users')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('users.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-users"></i>
                                </span><br>
                                Usuarios
                            </a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('clients')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('clients.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-user-tie"></i>
                                </span><br>
                                Clientes 
                            </a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('contracts')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('contracts.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-handshake"></i>
                                </span><br>
                                Contratos
                            </a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('billings')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('billings.index')}}">
                                <span style="font-size: 1.5em;">
                                    <i class="fas fa-dollar-sign"></i>
                                </span><br>
                                Facturas
                            </a>
                        </li>
   

                    @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }} <i class="fas fa-sign-in-alt"></i></a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user"></i> {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }} 
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
            </div>
        </nav>

        <main class="py-4">

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
    <footer id="sticky-footer" class="text-white fixed-bottom bg-dark">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><img src="{{asset('img/footer/').'/'.'phone.svg'}}" alt=""> Mesa de Ayuda:</p>
                                <p class="mt-0"><img src="{{asset('img/footer/').'/'.'chile.svg'}}" alt=""> (+56 2) 2439 6900
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><img src="{{asset('img/footer/').'/'.'peru.svg'}}" alt=""> (+51 1) 493 6344</p>
                                <p class="mt-0"><img src="{{asset('img/footer/').'/'.'colombia.svg'}}" alt=""> (+57 3) 112253011
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1 text-center">
                        <img src="{{asset('img/footer/').'/'.'planok.svg'}}" alt="" style="width:150px; height:30px;">
                        <hr>
                        <p class="mt-1">© 2020 PLANOK S.A. Todos los derechos reservados.</p>
                    </div>
                    <div class="col-sm-3 mt-2">
                        <div class="row">
                            <div class="col-sm-6">&nbsp;</div>
                            <div class="col-sm-6">
                                <p class="mb-1"><img src="{{asset('img/footer/').'/'.'mail.svg'}}" alt=""> Mail de contacto:</p>
                                <p class="mt-0">&nbsp;&nbsp;&nbsp;&nbsp;izenteno@planok.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
