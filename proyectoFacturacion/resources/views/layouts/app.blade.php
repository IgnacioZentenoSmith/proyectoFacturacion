<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

  	<!-- CSS STYLES -->
    <link href="{{ asset('css/libs/familyTitilliumWeb.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
	<!-- Bootstrap CSS -->
    <link href="{{ asset('css/libs/bootstrap.min.css') }}" rel="stylesheet">
	<!-- Font Awesome CSS -->
    <link href="{{ asset('css/libs/fontAwesomeAll.css') }}" rel="stylesheet">
    <link href="{{ asset('css/libs/pretty-checkbox.min.css') }}" rel="stylesheet">
	<!-- JS -->
	<!-- jQuery JS -->
    <script src="{{ asset('js/libs/jquery-3.4.1.min.js')}}"></script>
	<!-- Popper JS -->
    <script src="{{ asset('js/libs/popper.min.js')}}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('js/libs/bootstrap.min.js')}}"></script>
    <!-- Font Awesome JS -->
    <script src="{{ asset('js/libs/solid.js')}}"></script>
    <script src="{{ asset('js/libs/fontawesome.js')}}"></script>
	<!-- Bootstrap Tables-->
    <link href="{{ asset('css/libs/bootstrap-table.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/libs/bootstrap-table.min.js')}}"></script>
	<!-- Bootstrap Tables Export Extension -->
    <script src="{{ asset('js/libs/bootstrap-table-export.min.js')}}"></script>
	<!-- JS Exports plugin Bootstrap Tables-->
    <script src="{{ asset('js/libs/tableExport.min.js')}}"></script>
    <script src="{{ asset('js/libs/jspdf.min.js')}}"></script>
    <script src="{{ asset('js/libs/jspdf.plugin.autotable.js')}}"></script>

</head>
<body>
    <div id="app" class="d-flex flex-column">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow d-flex align-items-start">
            <div class="container-fluid">
                <ul class="navbar-nav mr-auto text-center">
                    <a class="navbar-brand">
                        Sistema de facturación
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
                        @if(in_array(1, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('admin*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('admin.index')}}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-shield-alt"></i>
                                    </span><br>
                                    Admin
                                </a>
                            </li>
                        @endif
                        @if(in_array(2, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('clients*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('clients.index')}}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-user-tie"></i>
                                    </span><br>
                                    Clientes
                                </a>
                            </li>
                        @endif
                        @if(in_array(3, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('contracts*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('contracts.index')}}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-handshake"></i>
                                    </span><br>
                                    Contratos
                                </a>
                            </li>
                        @endif
                        @if(in_array(12, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('parameterization*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('parameterization.index')}}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-cogs"></i>
                                    </span><br>
                                    Parametrizaciones
                                </a>
                            </li>
                        @endif
                        @if(in_array(4, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('billings*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('billings.index', 0) }}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span><br>
                                    Facturas
                                </a>
                            </li>
                        @endif
                        @if(in_array(4, $authPermisos))
                            <li class="nav-item mx-2 {{ (request()->is('binnacle*')) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('binnacle.index', 0) }}">
                                    <span style="font-size: 1.5em;">
                                        <i class="fas fa-book"></i>
                                    </span><br>
                                    Bitácora
                                </a>
                            </li>
                        @endif



                    @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }} <i class="fas fa-sign-in-alt"></i></a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user"></i> {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}" id="logout">
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
        @include('flashMessages')

    <main class="py-4 my-4 d-flex align-items-center">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                @guest
                    <!-- NO ESTA DENTRO DEL SISTEMA -->
                    @yield('content')
                @else

                    <!-- ESTA DENTRO DEL SISTEMA -->
                    @if (Auth::user() && Auth::user()->status == 'Inactivo')
                        <div class="alert alert-danger text-center shadow" role="alert">
                            <p class="font-weight-bold">Usted es un usuario inactivo de nuestro sistema, contáctese con un administrador para que sea activado.</p>
                        </div>
                    @elseif (Auth::user() && Auth::user()->status == 'Activo')
                        @yield('content')
                    @endif

                @endguest

                </div>
            </div>
        </div>
    </main>

    <footer class="text-white d-flex align-items-end bg-dark mt-auto">
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
                                    <p class="mt-0">izenteno@planok.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<script src="{{ asset('js/components/logout.js')}}"></script>
</body>
</html>
