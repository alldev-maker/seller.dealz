<nav id="navbar" class="navbar navbar-expand navbar-dark bg-primary fixed-top d-flex align-content-center ng-scope">
    <button id="sidebar-trigger" class="btn btn-sm btn-bars btn-outline-light mr-3 sidebar-trigger" type="button">
        <i class="fas fa-bars fw"></i>
    </button>
    <a class="navbar-brand mr-3" href="{{ route('home') }}"><?php echo settings('site.title'); ?></a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fw fa-lg"></i> <span ng-show="unreadCount > 0" class="badge badge-unread badge-danger ng-binding ng-hide" ng-bind="unreadCount"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-alert" style="width: 35rem">
                <ul class="list-unstyled dropdown-alert">
                    <!-- ngRepeat: alert in alerts -->
                    <li class="text-center d-flex">
                        <a class="w-50" href="#" ng-click="markAllAsRead($event)">Mark All Read</a>
                        <a class="w-50" href="/alerts/">More Alerts</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user fw fa-lg"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="/profile">Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
            </div>
        </li>
    </ul>
</nav>