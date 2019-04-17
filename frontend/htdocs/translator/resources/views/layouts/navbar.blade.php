@guest
        @else
        <nav class="navbar navbar-light navbar-laravel">
            <div class="full-with">
                <a class="navbar-brand" href="{{ url('/login') }}">
                        <img src="{{ asset('public/image/star.png') }}" class="logo-star" alt="star">
                        <img src="{{ asset('public/image/logo.png') }}" class="main-logo" alt="Bitexchange" style="filter: invert(100%);">
                </a>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        
                          
                        
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
                                       <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    </form>
                                </div>
                            </li>
                        
                    </ul>
                </div>
                <button class="navbar-toggler nav-menu-hide" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
            </div>
        </nav>

        @endguest