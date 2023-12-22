<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white"
  id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      <img src="{{ asset('assets/img/circle-logo.png') }}" class="navbar-brand-img h-100" alt="...">
      <span class="ms-3">SAMs </span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
    @if(auth()->check() && auth()->user()->admin)
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('dashboard') ? 'active' : '') }}" href="{{ url('dashboard') }}">
          <div>
            <img src="{{ asset('assets/img/Dashboard.png') }}" class="navbar-brand-img h-100" alt="...">
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('student.search') ? 'active' : '') }}" href="{{ url('/') }}">
        <div>
            <img src="{{ asset('assets/img/Attendance.png') }}" class="navbar-brand-img h-100" alt="...">
          </div>
          <span class="nav-link-text ms-1">Attendance</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('attendances') ? 'active' : '') }}" href="{{ url('attendances') }}">
        <div>
            <img src="{{ asset('assets/img/Attendancelist.png') }}" class="navbar-brand-img h-100" alt="...">
          </div>
          <span class="nav-link-text ms-1">Attendance List</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('events') ? 'active' : '') }}" href="{{ url('events') }}">
        <div>
            <img src="{{ asset('assets/img/import.png') }}" class="navbar-brand-img h-100" alt="...">
          </div>
          <span class="nav-link-text ms-1">Events</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ (Request::is('import-students') ? 'active' : '') }}" href="{{ url('import-students') }}">
        <div>
            <img src="{{ asset('assets/img/import.png') }}" class="navbar-brand-img h-100" alt="...">
          </div>
          <span class="nav-link-text ms-1">Import</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('student.search') ? 'active' : '') }}" href="{{ url('/logout') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fa fa-sign-out" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Log Out</span>
        </a>
      </li>
    </ul>


    </ul>
    @else
    <!-- Content visible to non-admin users -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('dashboard') ? 'active' : '') }}" href="{{ url('dashboard') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fas fa-globe" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('student.search') ? 'active' : '') }}" href="{{ url('/') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fas fa-globe" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Attendance</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('attendances') ? 'active' : '') }}" href="{{ url('attendances') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fas fa-globe" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Attendance List</span>
        </a>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('import-students') ? 'active' : '') }}" href="{{ url('import-students') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fa fa-cloud" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Import</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('events') ? 'active' : '') }}" href="{{ url('events') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fas fa-globe" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Events</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('student.search') ? 'active' : '') }}" href="{{ url('/logout') }}">
          <div
            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">

            <i class="fas fa-globe" style="font-size: 18px;"></i>
          </div>
          <span class="nav-link-text ms-1">Log Out</span>
        </a>
      </li>
    </ul>
    @endif
  </div>

</aside>