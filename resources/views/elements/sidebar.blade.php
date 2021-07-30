<nav id="sidebar" class="sidebar sidebar-dark bg-primary">
    <div class="profile text-center">
        <img src="{{ url('images/logo.svg') }}" class="mb-3 mCS_img_loaded" height="96">
        <h4 class="mb-0"><?php echo auth()->user()->nice_name; ?></h4>
        <p class="mb-0"><?php echo auth()->user()->role->name; ?></p>
    </div>

    <ul class="mt-3 sidebar-nav flex-column">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
        @role(['test-taker'])
        <li class="nav-item"><a class="nav-link" href="/reports/">Quizzes</a></li>
        <li class="nav-item"><a class="nav-link" href="/reports/">Results</a></li>
        @endrole
        @role(['testgiver', 'admin', 'developer'])
        <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-quizzes">Quizzes</a>
            <ul class="nav-sub collapse" id="sidebar-quizzes">
                <li class="nav-item"><a class="nav-link" href="{{ route('quizzes.quizzes.index') }}">Quizzes</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('quizzes.results.index') }}">Results</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('quizzes.types.sections.index') }}">Section Types</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('quizzes.types.problems.index') }}">Problem Types</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
            </ul>
        </li>
        @endrole
        @role(['admin', 'developer'])
        <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-test-takers">Roster</a>
            <ul class="nav-sub collapse" id="sidebar-test-takers">
                <li class="nav-item"><a class="nav-link" href="{{ route('roster.testgivers.index') }}">Test Givers</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('roster.testtakers.index') }}">Test Takers</a></li>
            </ul>
        </li>
        @endrole
        @role(['testgiver', 'admin', 'developer'])
        <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-mediamanager">Media Manager</a>
            <ul class="nav-sub collapse" id="sidebar-mediamanager">
                <li class="nav-item"><a class="nav-link" href="{{ route('mediamanager.index') }}">Media Manager</a></li>
            </ul>
        </li>
        @endrole
        @role(['admin', 'developer'])
        <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-admin">Administration</a>
            <ul class="nav-sub collapse" id="sidebar-admin">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.roles.index') }}">Roles</a></li>
            </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="/reports/">Reports</a></li>
        <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-system">System</a>
            <ul class="nav-sub collapse" id="sidebar-system">
                <li class="nav-item"><a class="nav-link" href="/logs/">Audit Log</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Notifications</a></li>
                <li class="nav-item nav-has-child"><a class="nav-link collapsed" href="#" onclick="return false;" data-toggle="collapse" data-target="#sidebar-systemsystemstatistics">Statistics</a>
                    <ul class="nav-sub collapse" id="sidebar-systemsystemstatistics">
                        <li class="nav-item"><a class="nav-link" href="/statistics/modules/">Modules</a></li>
                        <li class="nav-item"><a class="nav-link" href="/statistics/actions/">Actions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Activities</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="/settings/">Settings</a></li>
            </ul>
        </li>
        @endrole
    </ul>
    <hr>
    <ul class="sidebar-nav flex-column">
        <li class="nav-item nav-item-main">
            <a class="nav-link" href="/profile">Profile</a>
        </li>
        <li class="nav-item nav-item-main">
            <a class="nav-link" href="{{ route('logout') }}">Logout</a>
        </li>
    </ul>
    <hr>
</nav>
