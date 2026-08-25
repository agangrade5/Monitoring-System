<!--begin::Sidebar-->
<aside
    class="app-sidebar bg-body-secondary shadow"
    data-bs-theme="dark"
>
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <!--begin::Brand Image-->
            {{-- <img
                src=""
                alt="{{config('app.name')}} Logo"
                class="brand-image opacity-75 shadow"
            /> --}}
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">{{config('app.name')}}</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="false"
                id="navigation"
            >
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
