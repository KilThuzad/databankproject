<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Verify Your Email</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('message'))
                <div class="alert alert-info">{{ session('message') }}</div>
            @endif

            {{-- If the user is already verified, show a message and link to dashboard --}}
            @if(auth()->user()->hasVerifiedEmail())
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-1"></i> Your email is already verified.
                </div>
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house-door-fill me-1"></i> Go to Dashboard
                </a>
            @else
                <p>
                    Thank you for registering, <strong>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</strong>!
                    A verification link has been sent to <strong>{{ auth()->user()->email }}</strong>.
                </p>

                <p>If you did not receive the email, click the button below to resend it:</p>

                {{-- Use the correct route name: verification.resend --}}
                <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-envelope-fill me-1"></i> Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Optional: auto‑refresh after a few seconds? --}}
{{-- <script>
    // You could add a simple countdown or just let the user click the button.
</script> --}}

</body>
</html>