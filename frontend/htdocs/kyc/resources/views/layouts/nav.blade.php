<nav class="navbar navbar-expand-lg">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{asset('images/logo1.png') }}" class="img-responsive" alt="Bitexchange Exchange" style="/*filter: invert(100%);*/width: 200px;margin-top: 6px;"></a>
        <div class="collapse navbar-collapse justify-content-md-center" id="navbarToggler">

            <ul class="navbar-nav ml-auto">
                <li class="nav-item" style="margin-top: 10px;">
                    <span style="color: white;">Account Status - Verification &nbsp;&nbsp;&nbsp;</span>
                    @if(auth()->user()->approval == 'APPROVED')
                    <span class="badge badge-success">{{auth()->user()->approval}}</span>
                    @elseif(auth()->user()->approval == 'DECLINED')
                    <span class="badge badge-danger">{{auth()->user()->approval}}</span>
                    @else
                    <span class="badge badge-warning">{{auth()->user()->approval}}</span>
                    @endif

                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i></a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('home') }}">
                            <strong>Account</strong><br>
                            <span>{{auth()->user()->email}}</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
