@guest
        @else
<nav class="navbar navbar-expand-md bg-light nav-min-width">
  <ul class="navbar-nav">
   <!--  <li class="nav-item">
       <a class="nav-link {{{ (Request::is('language') ? 'nav-active' : '') }}} "" href="{{ route('language') }}"><i class="fa fa-home" aria-hidden="true"></i>    Dashboard</a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link {{{ (Request::is('language') ? 'nav-active' : '') }}} "" href="{{ route('language') }}"><i class="fa fa-language" aria-hidden="true"></i>    Language</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{{ (Request::is('page') ? 'nav-active' : '') }}} " " href="{{ route('page') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i>    Pages</a>
    </li>
    
    <li class="nav-item">
      <a class="nav-link {{{ (Request::is('menu') ? 'nav-active' : '') }}} "" href="{{ route('menu') }}"><i class="fa fa-book" aria-hidden="true"></i>    Menu</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{{ (Request::is('footer') ? 'nav-active' : '') }}} "" href="{{ route('footer') }}"><i class="fa fa-bars" aria-hidden="true"></i>    Footer</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{{ (Request::is('media') ? 'nav-active' : '') }}} "" href="{{ route('media') }}"><i class="fa fa-link" aria-hidden="true"></i>   Social Media</a>
    </li>
  </ul>
</nav>

@endguest