<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar" style="width: 280px;">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4 ml-3">
                <h4>EVCIEERD</h4>
                    <p>Databank</p>
            </span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ url('/reviewerDashboard') }}" class="nav-link text-white active" aria-current="page">
                    <ion-icon name="home-outline" class="me-2"></ion-icon>
                    Home
                </a>
            </li>
        
             <li>
                <a href="{{ url('/reviewerResearches') }}" class="nav-link text-white">
                    <ion-icon name="list-outline" class="me-2"></ion-icon>
                    Projects
                </a>
            </li>
            
            <li>
                <a href="{{ url('/reviewerDashboard') }}" class="nav-link text-white">
                   <ion-icon name="calendar-clear-outline" class="me-2"></ion-icon>
                    Calendar
                </a>
            </li>
          
        </ul>
    </div>

    <div class="main-content flex-grow-1">
        <nav class="navbar navbar-expand-lg topbar">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <ion-icon name="menu-outline"></ion-icon>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex ms-auto me-3 search-box">
                        <input class="form-control me-2" type="search" placeholder="Search here">
                        <button class="btn btn-outline-primary" type="submit">
                            <ion-icon name="search-outline"></ion-icon>
                        </button>
                    </form>
                    
                    <div class="dropdown user-dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                        id="userDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            <img src="{{ Auth::user()?->profile_picture_url ?? asset('images/default-profile.png') }}" alt="Profile Picture" width="40" height="40" class="rounded-circle me-2">
                            <span class="username text-uppercase">{{ session('firstname') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li class="user-profile text-center p-3">
                                <img src="{{ Auth::user()?->profile_picture_url ?? asset('images/default-profile.png') }}" alt="Profile Picture" width="80" height="80" class="rounded-circle mb-2">
                                <span class="username d-block text-uppercase">{{ session('firstname') }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('userprofile.show') }}"><ion-icon name="person-outline" class="me-2"></ion-icon>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><ion-icon name="settings-outline" class="me-2"></ion-icon>Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}"><ion-icon name="log-out-outline" class="me-2"></ion-icon>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    
        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>
@push('scripts')    
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
</body>
</html>