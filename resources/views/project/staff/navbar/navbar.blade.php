<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="{{ asset('css/staff/navbar.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
     
    </style>
</head>
<body class="d-flex">
    <div class="sidebar d-flex flex-column vh-100" id="sidebar">
        <button class="toggle-sidebar" id="toggleSidebar">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </button>
        
        <div class="logo">
            <h4>EVCIEERD</h4>
            <p class="logo-text">Databank</p>
        </div>
        
        <hr>
        
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ url('Staff/dashboard') }}" class="nav-link {{ request()->is('Staff/dashboard') ? 'active' : '' }}">
                    <ion-icon name="speedometer-outline"></ion-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <div class="project-dropdown-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url('/staff/researchproject') }}" class="nav-link {{ request()->is('/staff/researchproject') ? 'active' : '' }}">
                            <ion-icon name="list-outline"></ion-icon>
                            <span>Projects</span>
                        </a>

                        <button class="btn btn-link p-0 m-0 text-white chevron-btn" type="button">
                            <ion-icon name="chevron-down-outline" class="chevron"></ion-icon>
                        </button>
                    </div>

                    <div class="collapse {{ request()->is('staff/projects/*') ? 'show' : '' }}">
                        <div class="project-dropdown">
                            <div class="dropdown-title">FORMS</div>

                            <a href="{{ url('/staff/projects/pending') }}"
                               class="{{ request()->is('staff/projects/pending') ? 'active' : '' }}">
                                Pending
                            </a>

                            <a href="{{ url('/staff/projects/approved') }}"
                               class="{{ request()->is('staff/projects/approved') ? 'active' : '' }}">
                                Approved
                            </a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a href="{{ url('/staffevents') }}" class="nav-link {{ request()->is('staffevents') ? 'active' : '' }}">
                   <ion-icon name="calendar-clear-outline"></ion-icon>
                    <span>Calendar</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('/staff/review-assignments') }}" class="nav-link {{ request()->is('staff/review-assignments') ? 'active' : '' }}">
                   <ion-icon name="newspaper-outline"></ion-icon>
                    <span>Reviewer Assign</span>
                </a>
            </li>
        </ul>
        
        <div class="mt-auto p-3">
            <div class="text-center text-white-50 small">
                <p>&copy; 2023 EVCIEERD</p>
                <p>Version 2.1</p>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <nav class="navbar navbar-expand-lg navbar-light topbar">
            <div class="container-fluid">
                <button class="navbar-toggler mobile-toggle" type="button" id="mobileToggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex ms-auto me-3 search-box" action="{{ route('staffglobal.search') }}" method="GET">
                        <input class="form-control me-2" type="search" name="query" placeholder="Search projects, events, users...">
                        <button class="btn text-white" type="submit">
                            <ion-icon name="search-outline"></ion-icon>
                        </button>
                    </form>
                    
                    <div class="dropdown user-dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                        id="userDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            <img src="{{ Auth::user()?->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode(session('firstname')).'&background=d62828&color=fff' }}" 
                                 alt="Profile Picture" width="40" height="40" class="rounded-circle me-2">
                            <span class="username">{{ session('firstname') }} {{ session('lastname') ?? '' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li class="user-profile text-center p-3">
                                <img src="{{ Auth::user()?->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode(session('firstname')).'&background=d62828&color=fff&size=80' }}" 
                                     alt="Profile Picture" width="80" height="80" class="rounded-circle mb-2">
                                <span class="d-block fw-bold">{{ session('firstname') }} {{ session('lastname') ?? '' }}</span>
                                <small class="text-white-80">Staff Member</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('staffprofile.show') }}"><ion-icon name="person-outline" class="me-2"></ion-icon>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('staffprofile.edit') }}"><ion-icon name="settings-outline" class="me-2"></ion-icon>Update Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('staff.notifications') }}"><ion-icon name="notifications-outline" class="me-2"></ion-icon>Notifications</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <ion-icon name="log-out-outline" class="me-2"></ion-icon>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <ion-icon name="warning-outline" class="me-2"></ion-icon>
                            Confirm Logout
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <p class="mb-0 fs-5">
                            Are you sure you want to logout?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger px-4" onclick="document.getElementById('logoutForm').submit();">
                            Yes, Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            @yield('content')
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSidebar = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileToggle = document.getElementById('mobileToggle');

            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    const icon = this.querySelector('ion-icon');
                    icon.setAttribute('name', sidebar.classList.contains('collapsed') ? 'chevron-forward-outline' : 'chevron-back-outline');
                });
            }

            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 992) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnMobileToggle = mobileToggle.contains(event.target);
                    if (!isClickInsideSidebar && !isClickOnMobileToggle && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            document.querySelectorAll('.project-dropdown-wrapper .chevron-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const collapseEl = this.closest('.project-dropdown-wrapper').querySelector('.collapse');
                    const chevron = this.querySelector('.chevron');
                    new bootstrap.Collapse(collapseEl, { toggle: true });
                    chevron.classList.toggle('rotate');
                });
            });
        });
    </script>
</body>
</html>
