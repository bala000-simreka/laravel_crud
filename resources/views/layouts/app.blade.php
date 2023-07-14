<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script src="{{ asset('js/jquery-3.6.1.min.js') }}"></script>
    <!--<script src="https://cdn.plot.ly/plotly-2.24.1.min.js" charset="utf-8"></script>-->
    <script src="{{ asset('js/plotly-latest.min.js') }}"></script>

    <!-- use version 0.20.0 -->
    <script lang="javascript" src="{{ asset('js/xlsx.full.min.js') }}"></script>
    <!-- x-spreadsheet stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/xspreadsheet.css') }}"/>
    <!-- x-spreadsheet library -->
    <script src="{{ asset('js/xspreadsheet.js') }}"></script>
    <script src="{{ asset('js/xlsxspread.js') }}"></script>

    <!-- clusterize js -->
    <link href="{{ asset('css/clusterize.css') }}" rel="stylesheet">
    <script src="{{ asset('js/clusterize.min.js') }}"></script>   

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: #fff;
            text-align: center;
        }
  </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('plotly.index') }}">{{ __('Plotly') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('employees.index') }}">{{ __('Employees') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item logout" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="width: 1600px; margin: 0 auto">
            <!-- Modal -->
            <div id="sessionTimeoutModal" class="modal">
                <div class="modal-content">
                    <h3>You will be logged out in <span id="timer">60</span> seconds.</h3>
                </div>
            </div>
            @yield('content')
        </main>

        <script>
        $(document).ready(function() {
            var isAuthenticated = "{{ Auth::check() ? 'true' : 'false' }}";
            if(isAuthenticated == 'true'){
                var sessionTimeout = 5 * 60 * 1000; // Session timeout in milliseconds (3 minutes)
                var popupTime = 1 * 60 * 1000; // Time to show the popup before session timeout (1 minute)
                var warningTime = sessionTimeout - popupTime; // Time remaining before showing the popup (2 minutes)
                var logoutTimer;
                var warningTimer;
                var broadcastChannel = new BroadcastChannel('session-channel');
                var remainingTime;
                var countdownTimer;
                var timerElement = document.getElementById('timer');
                var modalElement = document.getElementById('sessionTimeoutModal');
                

                function updateTimer() {
                    if (remainingTime > 0) {
                        timerElement.textContent = remainingTime;
                        remainingTime--;
                    } else {
                        clearInterval(countdownTimer);
                        //logout();
                    }
                }

                function showAlertWithTimer() {
                    modalElement.style.display = 'block';
                    remainingTime = 60;
                    updateTimer();
                    countdownTimer = setInterval(updateTimer, 1000);
                }

                // Function to show the logout popup
                function showPopup() {
                    //$('#sessionTimeoutModal').show();
                    if (document.visibilityState === 'visible') {
                        showAlertWithTimer();
                    }
                }

                // Function to broadcast the logout message to other tabs
                function broadcastLogout() {
                    broadcastChannel.postMessage('logout');
                }

                // Function to logout the user
                function logout() {
                    modalElement.style.display = 'none';
                    
                    $.ajax({
                        url: '/logout',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response, ) {
                            window.location.href = '/login';
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            //console.log(xhr.status);
                        }
                    }).always(function (jqXHR) {
                        if(jqXHR.status == '419'){
                            window.location.href = '/';
                        }
                    });
                }

                // Start the session timeout countdown
                function startSessionTimeout() {
                    logoutTimer = setTimeout(function(){
                        broadcastLogout();
                        logout();
                    }, sessionTimeout);
                    warningTimer = setTimeout(showPopup, warningTime);
                }

                // Reset the session timeout on user activity
                function resetSessionTimeout() {
                    clearTimeout(logoutTimer);
                    clearTimeout(warningTimer);
                    startSessionTimeout();                
                    modalElement.style.display = 'none';
                    clearInterval(countdownTimer);
                }

                // Start the session timeout when the page loads
                startSessionTimeout();

                // Bind events to reset session timeout on user activity
                $(document).on('click mousemove keypress', function(){
                    resetSessionTimeout();
                    broadcastChannel.postMessage('resetLogout');
                });

                // Listen for logout message from other tabs
                broadcastChannel.onmessage = function(event) {
                    if (event.data === 'logout') {
                        logout();
                    }
                    if (event.data === 'resetLogout') {
                        resetSessionTimeout();
                    }
                };
            }            
        });
        </script>
    </div>
</body>
</html>
