<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-table.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap-table.min.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap-table-es-CL.min.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap-table-export.min.js') }}" defer></script>
    <script src="{{ asset('js/jspdf.min.js') }}" defer></script>
    <script src="{{ asset('js/jspdf.plugin.autotable.min.js') }}" defer></script>
    <script src="{{ asset('js/tableExport.min.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow">
            <div class="container-fluid">
                <a class="navbar-brand">
                    Proyecto facturaciones
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav m-auto">
                    @guest
                    @else
                        <li class="nav-item mx-2 {{ (request()->is('/')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('home.index')}}">Home</a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('admin')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('admin.index')}}">Admin</a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('users')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('users.index')}}">Usuarios</a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('clients')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('clients.index')}}">Clientes</a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('contracts')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('contracts.index')}}">Contratos</a>
                        </li>
                        <li class="nav-item mx-2 {{ (request()->is('billings')) ? 'active' : '' }}">
                            <a class="nav-link" href="{{route('billings.index')}}">Facturas</a>
                        </li>
   

                    @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
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
