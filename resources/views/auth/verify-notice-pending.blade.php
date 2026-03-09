<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm border-danger">
        <div class="card-header bg-danger text-white">
            <h3 class="mb-0">Verify Your Email</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <p>We've sent a verification link to your email address. Please check your inbox (and spam folder) and click the link to complete your registration.</p>

            <p>If you didn't receive the email, you can request a new one below.</p>

            <form method="POST" action="{{ route('verification.resend.pending') }}" class="d-inline">
                @csrf
                <label for="email" class="form-label">Enter your email:</label>
                <div class="input-group mb-3">
                    <input type="email" name="email" id="email" class="form-control border-danger" placeholder="your@email.com" required>
                    <button class="btn btn-danger" type="submit">Resend Verification</button>
                </div>
            </form>

            <p class="mt-3"><a href="{{ route('register') }}" class="text-danger">Go back to registration</a></p>
        </div>
    </div>
</div>
</body>
</html>