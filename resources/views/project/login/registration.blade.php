<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EVCIEERD - Create Account</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
<div class="container">
    <div class="register-container">
        <div class="brand-bar">
            <a href="{{ url('/') }}" class="brand-link">
                <i class="fas fa-microscope"></i> EVCIEERD
            </a>
            <p>Eastern Visayas Research Databank System</p>
        </div>

        <div class="register-card">
            <div class="card-header">
                <h2>Create Account</h2>
                <p class="text-muted mt-2 mb-0">Join the EVCIEERD community today</p>
            </div>
            <div class="card-body">
                <div class="progress-steps">
                    <div class="step active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-label">Account Info</div>
                    </div>
                    <div class="step" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-label">Profile Picture</div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <!-- Step 1 -->
                    <div id="step1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" id="firstname" value="{{ old('firstname') }}" placeholder="First Name" required>
                                @error('firstname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" id="lastname" value="{{ old('lastname') }}" placeholder="Last Name" required>
                                @error('lastname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" value="{{ old('username') }}" placeholder="Choose a username" required>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" placeholder="your@email.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small class="text-muted">We'll send a verification link to this email.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Create password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-password"><i class="bi bi-eye"></i></button>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <small class="form-text text-muted"><i class="bi bi-info-circle me-1"></i> Minimum 8 characters, at least 1 letter & 1 number</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-password"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="member_agencies_id" class="form-label">Member Agency</label>
                            <select name="member_agencies_id" id="member_agencies_id" class="form-select @error('member_agencies_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select your member agency</option>
                                @foreach($memberAgencies as $agency)
                                    <option value="{{ $agency->id }}" {{ old('member_agencies_id')==$agency->id?'selected':'' }}>{{ $agency->name }}</option>
                                @endforeach
                            </select>
                            @error('member_agencies_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="login-link"><i class="bi bi-box-arrow-in-right me-1"></i> Already have an account? <a href="{{ route('login.form') }}">Login Now</a></div>
                            <button type="button" id="nextBtn" class="btn btn-primary">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div id="step2" style="display:none;">
                        <div class="profile-upload text-center">
                            <img id="profilePreview" src="https://via.placeholder.com/200" class="profile-preview mb-3" alt="Profile Preview">
                            <div class="upload-btn position-relative d-inline-block">
                                <span class="btn btn-primary"><i class="bi bi-camera me-2"></i>Choose Profile Picture</span>
                                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                            </div>
                            @error('profile_picture') <div class="invalid-feedback d-block mt-2 text-center">{{ $message }}</div> @enderror
                            <p class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i> Optional: Upload a profile picture (JPG, PNG, GIF)</p>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" id="backBtn" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Create Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 back-home">
            <a href="{{ url('/') }}"><i class="bi bi-house-door-fill"></i> Back to Home</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const nextBtn = document.getElementById('nextBtn');
const backBtn = document.getElementById('backBtn');
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step1Indicator = document.getElementById('step1-indicator');
const step2Indicator = document.getElementById('step2-indicator');

nextBtn.addEventListener('click', () => {
    // Basic validation
    const requiredFields = step1.querySelectorAll('input, select');
    let valid = true;
    requiredFields.forEach(field => { if(!field.checkValidity()) { field.reportValidity(); valid=false;} });
    if(!valid) return;

    step1.style.display='none';
    step2.style.display='block';
    step1Indicator.classList.remove('active'); step1Indicator.classList.add('completed');
    step2Indicator.classList.add('active');
});
backBtn.addEventListener('click', () => {
    step2.style.display='none';
    step1.style.display='block';
    step1Indicator.classList.add('active'); step1Indicator.classList.remove('completed');
    step2Indicator.classList.remove('active');
});

// Password toggle
document.querySelectorAll('.toggle-password').forEach(btn=>{
    btn.addEventListener('click', () => {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');
        if(input.type==='password'){ input.type='text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
        else { input.type='password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
    });
});

// Profile preview
document.getElementById('profile_picture').addEventListener('change', function(e){
    if(this.files && this.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){ document.getElementById('profilePreview').src = e.target.result; };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
</body>
</html>
