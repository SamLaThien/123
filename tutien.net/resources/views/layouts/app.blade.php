<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <!-- <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> -->
    <!-- <script src="{{ asset('js/popper.min.js') }}"></script> -->
    <!-- <script src="{{ asset('js/bootstrap.min.js') }}"></script> -->
    <script src="{{ asset('js/pusher.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
    <!-- <script src="{{ asset('js/datatables.min.js') }}"></script> -->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/fh-3.2.2/datatables.min.css" />

    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/fh-3.2.2/datatables.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <style>
        .vo-ta-team {
            color: #0033FF;
        }

        .vo-ta-team:before {
            content: url("https://tutien.net/images/votateam.gif");
        }

        .an-vu-thon {
            color: #00ee00;
        }

        .an-vu-thon:before {
            content: url("https://tutien.net/images/phannon.gif");
        }

        .am-duong-diem {
            color: #2caae3;
        }

        .am-duong-diem:before {
            content: url("https://tutien.net/images/memkute.gif");
        }

        .tieu-ngao-nhan-gian {
            color: #ff6633;
        }

        .tieu-ngao-nhan-gian:before {
            content: url("https://tutien.net/images/tieungao.gif");
        }

        .thien-nhai-hai-giac {
            color: #9999FF;
        }

        .thien-nhai-hai-giac:before {
            content: url("https://tutien.net/images/thiennhai.gif");
        }

        .lac-tuyet-tieu-dao {
            color: #990099;
        }

        .lac-tuyet-tieu-dao:before {
            content: url("https://tutien.net/images/lactuyet2.gif");
        }

        .de-thien-mon {
            color: #DC143C;
        }

        .de-thien-mon:before {
            content: url("https://tutien.net/images/dethienmon.gif?v=2");
        }

        .ma-than-dien {
            color: #FF9933;
        }

        .ma-than-dien:before {
            content: url("https://tutien.net/images/thanmacung.gif");
        }

        .tu-la-ma-dien {
            color: #FF0099;
        }

        .tu-la-ma-dien:before {
            content: url("https://tutien.net/images/thanhtien.gif");
        }

        .vo-cuc-ma-tong {
            color: #026466;
        }

        .vo-cuc-ma-tong:before {
            content: url("https://tutien.net/images/vcmt.gif");
        }

        .vinh-hang-dien {
            color: #00CC99;
        }

        .vinh-hang-dien:before {
            content: url("https://tutien.net/images/mynhandong2.gif");
        }
    </style>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet"> -->

    <script>
        // try {
        //     window.$ = window.jQuery = $;
        // } catch (e) {
        // }

        /**
         * We'll load the axios HTTP library which allows us to easily issue requests
         * to our Laravel back-end. This library automatically handles sending the
         * CSRF token as a header based on the value of the "XSRF" token cookie.
         */

        window.axios = axios;

        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        /**
         * Next we will register the CSRF Token as a common header with Axios so that
         * all outgoing HTTP requests automatically have it attached. This is just
         * a simple convenience so we don't have to attach every token manually.
         */

        let token = document.head.querySelector('meta[name="csrf-token"]');

        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        } else {
            console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
        }


        window.selectAccountId = 0;
        toastr.options = {
            "closeButton": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = false;

        var pusher = new Pusher('dab3ef72535208679621', {
            cluster: 'ap1',
            authEndpoint: '/broadcasting/auth'
        });

        @auth
        var channel = pusher.subscribe('private-can-dan.{!! auth()->user()->id !!}');
        channel.bind('can-dan-event', function(data) {
            const message = data.message;
            console.log(message);
            toastr.info(message);

            if (message.includes('thành công') || message.includes('Tạch')) {
                toastr.info('Cập nhật lại thông tin account!');
                setTimeout(() => {
                    const id = data.id;
                    axios.get(`/accounts/${id}`)
                        .then(res => {
                            const account = res.data.data;
                            $('#accountInfo').text(`${account.account_name} - ${account.progress}`);
                            $('#account_progress_' + account.account_id).text(account.progress);
                            $('#account_ts_' + account.account_id).text(account.tai_san);
                        });
                }, 2500);
            }
        });
        @endauth
    </script>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <ul class="navbar-nav">
                            @auth
                            <li class="nav-item active">
                                <a class="nav-link" href="/accounts">Account</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="/can-dan">Cắn Đan</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="/setting">Settings</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="/export">Export</a>
                            </li>
                            @endauth
                        </ul>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            @if (Route::has('register'))
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        </li>
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
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
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>

</html>