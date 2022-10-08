<!--
                Helper classes

                Adding .sidebar-mini-hide to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
                Adding .sidebar-mini-show to an element will make it visible (opacity: 1) when the sidebar is in mini mode
                    If you would like to disable the transition, just add the .sidebar-mini-notrans along with one of the previous 2 classes

                Adding .sidebar-mini-hidden to an element will hide it when the sidebar is in mini mode
                Adding .sidebar-mini-visible to an element will show it only when the sidebar is in mini mode
                    - use .sidebar-mini-visible-b if you would like to be a block when visible (display: block)
            -->
<nav id="sidebar">
    <!-- Sidebar Content -->
    <div class="sidebar-content">
        <!-- Side Header -->
        <div class="content-header content-header-fullrow px-15">
            <!-- Mini Mode -->
            <div class="content-header-section sidebar-mini-visible-b">
                <!-- Logo -->
                <span class="content-header-item font-w700 font-size-xl float-left animated fadeIn">
                     <span class="text-primary">S</span><span class="text-dual-primary-dark">A</span>
                </span>
                <!-- END Logo -->
            </div>
            <!-- END Mini Mode -->

            <!-- Normal Mode -->
            <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                <!-- Close Sidebar, Visible only on mobile screens -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-circle btn-dual-secondary d-lg-none align-v-r" data-toggle="layout"
                    data-action="sidebar_close">
                    <i class="fa fa-times text-danger"></i>
                </button>
                <!-- END Close Sidebar -->

                <!-- Logo -->
                <div class="content-header-item">
                    <a class="link-effect font-w700" href="/dashboard">
                        <i class="si si-fire text-primary"></i>
                         <span class="font-size-xl text-primary">SiS</span><span class="font-size-xl text-dual-primary-dark">Cloud</span>
                    </a>
                </div>
                <!-- END Logo -->
            </div>
            <!-- END Normal Mode -->
        </div>
        <!-- END Side Header -->

        <!-- Side User -->
        <div class="content-side content-side-full content-side-user px-10 align-parent">
            <!-- Visible only in mini mode -->
            <div class="sidebar-mini-visible-b align-v animated fadeIn">
                <img class="img-avatar img-avatar32" src="{{ asset('media/avatars/avatar15.jpg') }}" alt="">
            </div>
            <!-- END Visible only in mini mode -->

            <!-- Visible only in normal mode -->
            <div class="sidebar-mini-hidden-b text-center">
                <a class="img-link" href="javascript:void(0)">
                    <img class="img-avatar" src="{{ asset('media/avatars/avatar15.jpg') }}" alt="">
                </a>
                <ul class="list-inline mt-10">
                    <li class="list-inline-item">
                        <a class="link-effect text-dual-primary-dark font-size-xs font-w600 text-uppercase"
                            href="javascript:void(0)">{{auth()->user()->username}}</a>
                    </li>
                    <li class="list-inline-item">
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <a class="link-effect text-dual-primary-dark" data-toggle="layout"
                            data-action="sidebar_style_inverse_toggle" href="javascript:void(0)">
                            <i class="si si-drop"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="link-effect text-dual-primary-dark" href="/logout">
                            <i class="si si-logout"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- END Visible only in normal mode -->
        </div>
        <!-- END Side User -->

        <!-- Side Navigation -->
        <div class="content-side content-side-full">
            <ul class="nav-main">
                <li>
                    <a class="{{ request()->is('home') ? ' active' : '' }}" href="{{ route('home') }}">
                        <i class="si si-cup"></i><span class="sidebar-mini-hide">Home</span>
                    </a>
                </li>
                @permissions('create.openTenants')
                <li>
                    <a class="{{ request()->is('OpenTenants') ? ' active' : '' }}" href="{{ route('openTenants.index') }}">
                        <i class="fa fa-cloud"></i><span class="sidebar-mini-hide">EDGE Cluster</span>
                    </a>
                </li>
                @endpermissions
                @anypermissions('read.log_api|del.log_api')
                <li>
                    <a class="{{ request()->is('logApi') ? ' active' : '' }}" href="{{ route('log_api.index') }}">
                        <i class="fa fa-telegram"></i><span class="sidebar-mini-hide">Log Tenants</span>
                    </a>
                </li>
                @endanyppermissions
                @anypermissions('create.api_users|read.api_users|update.api_users|del.api_users|update.user_map')
                 <li>
                    <a class="{{ request()->is('api_users') ? ' active' : '' }}" href="{{ route('api_users.index') }}">
                        <i class="fa fa-user"></i><span class="sidebar-mini-hide">EDGE Users</span>
                    </a>
                </li>
                @endanyppermissions
                @anypermissions('create.users|read.users|update.users|del.users')
                <li>
                    <a class="{{ request()->is('users') ? ' active' : '' }}" href="{{ route('users.index') }}">
                        <i class="si si-users"></i><span class="sidebar-mini-hide">Users</span>
                    </a>
                </li>
                @endanyppermissions
                @anypermissions('create.roles|read.roles|update.roles|del.roles')
                 <li>
                    <a class="{{ request()->is('roles') ? ' active' : '' }}" href="{{ route('roles.index') }}">
                        <i class="fa fa-expeditedssl"></i><span class="sidebar-mini-hide">Roles</span>
                    </a>
                </li>
                 @endanyppermissions
                 @anypermissions('read.config|update.config')
                 <li>
                    <a class="{{ request()->is('config') ? ' active' : '' }}" href="{{ route('config.index') }}">
                        <i class="fa fa-cogs"></i><span class="sidebar-mini-hide">Config</span>
                    </a>
                </li>
                 @endanyppermissions
                @role('developer')
                 <li>
                    <a class="{{ request()->is('permissions') ? ' active' : '' }}" href="{{ route('permissions.index') }}">
                        <i class="fa fa-lock"></i><span class="sidebar-mini-hide">Permissions</span>
                    </a>
                </li>
                 <li>
                    <a class="{{ request()->is('users_permissions') ? ' active' : '' }}" href="{{ route('users_permissions.index') }}">
                        <i class="fa fa-user-secret"></i><span class="sidebar-mini-hide">Users Permissions</span>
                    </a>
                </li>
                @endrole
                @anypermissions('read.syslog')
                <li>
                    <a class="{{ request()->is('view-logs') ? ' active' : '' }}" href="{{ route('view_logs.sysLogs') }}">
                        <i class="fa fa-sticky-note"></i><span class="sidebar-mini-hide">View Logs System</span>
                    </a>
                </li>
                 @endanyppermissions
            </ul>
        </div>
        <!-- END Side Navigation -->
        <div class=" fixed-bottom p-4">
            <a class="font-w600" target="_bank" href="{{ asset('version_control.html') }}">{{ config('app.version', 'v 1.02.01') }}</a>
        </div>
    </div>
    <!-- Sidebar Content -->
</nav>