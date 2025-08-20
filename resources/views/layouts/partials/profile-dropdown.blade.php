<li class="nav-item dropdown pe-3">

    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">


        <!-- Implementasi -->
        <img src="{{ Auth::user()->avatar ?? asset('assets/img/fotoprofil.jpeg') }}" alt="Profile" class="perfect-circle">
        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name ?? '' }}</span>
    </a><!-- End Profile Image Icon -->

    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
        <li class="dropdown-header">
            <h6>{{ Auth::user()->name ?? '' }}</h6>
            <span>{{ Auth::user()->email ?? '' }}</span>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>

        {{-- <li>
            <a class="dropdown-item d-flex align-items-center" href="#!">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
            </a>
        </li> --}}
        <li>
            <hr class="dropdown-divider">
        </li>

        <li>
            <a class="dropdown-item d-flex align-items-center" href="#!">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
            </a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>

        <li>
            <a class="dropdown-item d-flex align-items-center" href="#!">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
            </a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>

        <li>
            <a class="dropdown-item d-flex align-items-center" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>

    </ul><!-- End Profile Dropdown Items -->
</li><!-- End Profile Nav -->
