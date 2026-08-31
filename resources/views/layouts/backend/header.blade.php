<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a
                    class="nav-link"
                    data-lte-toggle="sidebar"
                    href="#"
                    role="button"
                    aria-label="Toggle sidebar"
                >
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <!-- <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toggle fullscreen"><i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i><i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i></a>
            </li> -->
            <!--end::Fullscreen Toggle-->

            <!--begin::Color Mode Toggle-->
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link btn border-0 bg-transparent"
                    id="theme-toggle-btn"
                    aria-label="Toggle color scheme"
                >
                    <i class="bi bi-sun-fill" id="theme-icon-light"></i><i class="bi bi-moon-fill d-none" id="theme-icon-dark"></i>
                </button>
            </li>
            <!--end::Color Mode Toggle-->

            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a
                    href="#"
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                >
                    <img
                        src="{{ asset('assets/images/backend/user2-160x160.jpg') }}"
                        class="user-image rounded-circle shadow"
                        alt="{{ auth()->user()->name }}"
                    />
                    <span class="d-none d-md-inline"
                        >{{ auth()->user()->name }}</span
                    >
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-lg dropdown-menu-end"
                >
                    <!--begin::User Image-->
                    <li class="user-header text-bg-primary">
                        <img
                            src="{{ asset('assets/images/backend/user2-160x160.jpg') }}"
                            class="rounded-circle shadow"
                            alt="Alexander Pierce"
                        />
                        <p>
                            {{ auth()->user()->name }}
                            
                        </p>
                    </li>
                    <!--end::User Image-->
                    <!--begin::Menu Footer-->
                    <li class="user-footer">
                        <a
                            href="#"
                            class="btn btn-outline-secondary"
                            >Profile</a
                        >
                        <form
                            id="logout-form"
                            method="POST"
                            action="{{ route('logout') }}"
                            class="d-none"
                        >
                            @csrf
                        </form>

                        <button
                            type="button"
                            class="btn btn-outline-danger float-end"
                            id="logout-button"
                            data-confirm-title="Logout?"
                            data-confirm-text="Are you sure you want to logout?"
                            data-confirm-button="Yes, Logout"
                        >
                            Logout
                        </button>
                    </li>
                    <!--end::Menu Footer-->
                </ul>
            </li>
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
<!--end::Header-->
