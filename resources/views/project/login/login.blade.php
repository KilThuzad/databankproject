<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVCIEERD - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<div class="login-container">
 
    <div class="brand-bar">
        <a href="{{ url('/') }}" class="brand-link">
            <i class="fas fa-microscope"></i> EVCIEERD
        </a>
        <p>Eastern Visayas Research Databank System</p>
    </div>

    <div class="login-card">
        <div class="card-body">
            <h1 class="login-title">Welcome Back</h1>

            {{-- Display session messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" name="username" placeholder="Enter your username" value="{{ old('username') }}" required autofocus>
                </div>

                <div class="input-icon password-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="togglePassword" title="Show/Hide Password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-login" id="loginButton">Sign In to EVCIEERD</button>

                <div class="register-link">
                    <p>New to EVCIEERD? <a href="{{ url('/register') }}">Create an account</a></p>
                </div>

                {{-- If email not verified --}}
                @if(session('email_not_verified'))
                    <div class="alert alert-warning mt-3">
                        <strong>Email not verified.</strong>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Resend Verification Email</button>
                        </form>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="back-home">
        <a href="{{ url('/') }}"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const passwordInput = document.querySelector('#password');
    const togglePassword = document.getElementById('togglePassword');
    togglePassword.addEventListener('click', () => {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        togglePassword.querySelector('i').classList.toggle('fa-eye');
        togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>