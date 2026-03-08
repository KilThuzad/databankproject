<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVCIEERD - Eastern Visayas Research Consortium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Color Palette - Refined */
        :root {
            --primary: #C1121F;
            --primary-dark: #A50D1A;
            --primary-darker: #8A0B15;
            --secondary: #780000;
            --accent: #FF4D6D;
            --accent-light: #FF8AA1;
            --background: #F8FAFC;
            --surface: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #475569;
            --text-tertiary: #64748B;
            --border-light: #E2E8F0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --gradient-primary: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 50%, var(--accent) 100%);
            --gradient-overlay: linear-gradient(45deg, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        /* Navbar - Enhanced */
        .navbar-custom {
            background: transparent;
            padding: 1.25rem 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-custom.scrolled {
            background: rgba(120, 0, 0, 0.98);
            backdrop-filter: blur(10px);
            padding: 0.75rem 0;
            box-shadow: var(--shadow-lg);
        }

        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            position: relative;
        }

        .navbar-custom .navbar-brand i {
            color: var(--accent-light);
            transition: transform 0.3s ease;
        }

        .navbar-custom .navbar-brand:hover i {
            transform: rotate(15deg);
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            margin: 0 0.25rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }

        .navbar-custom .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-custom .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .navbar-custom .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .navbar-custom .btn-outline-light:hover {
            background: white;
            color: var(--primary) !important;
            border-color: white;
            transform: translateY(-2px);
        }

        .navbar-custom .btn-primary {
            background: white;
            color: var(--primary) !important;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .navbar-custom .btn-primary:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Hero Section - Redesigned */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: var(--gradient-primary);
            color: white;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            background-size: 50px 50px;
            animation: slide 20s linear infinite;
        }

        @keyframes slide {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(50px) translateY(50px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 0 1.5rem;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.5rem;
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.25rem);
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-buttons .btn {
            padding: 0.875rem 2.5rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .hero-buttons .btn-primary {
            background: white;
            color: var(--primary);
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .hero-buttons .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .hero-buttons .btn-outline-light {
            border: 2px solid white;
            background: transparent;
        }

        .hero-buttons .btn-outline-light:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-4px);
        }

        /* Section Styles */
        section {
            padding: 6rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .section-header.light h2,
        .section-header.light p {
            color: white;
        }

        /* Cards - Unified Design */
        .card-feature {
            background: var(--surface);
            border-radius: 24px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-md);
        }

        .card-feature:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: transparent;
        }

        .card-feature i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-feature h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .card-feature p {
            color: var(--text-secondary);
            margin-bottom: 0;
            line-height: 1.6;
        }

        /* About Section */
        .about-section {
            background: white;
        }

        .about-image {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .about-image img {
            width: 100%;
            height: auto;
            transition: transform 0.7s ease;
        }

        .about-image:hover img {
            transform: scale(1.05);
        }

        .about-content h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .about-content p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .about-content .btn {
            padding: 0.875rem 2.5rem;
            border-radius: 12px;
            font-weight: 600;
            background: var(--primary);
            border: none;
            transition: all 0.3s ease;
        }

        .about-content .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Stats Section - Redesigned */
        .stats-section {
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .stat-item {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* How It Works */
        .step-card {
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .step-number {
            width: 64px;
            height: 64px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 2;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 32px;
            left: 60%;
            width: 80%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), transparent);
            z-index: 1;
        }

        .step-card:last-child::before {
            display: none;
        }

        .step-card h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .step-card p {
            color: var(--text-secondary);
            max-width: 250px;
            margin: 0 auto;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-darker), var(--secondary));
            color: white;
            text-align: center;
            padding: 5rem 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .cta-section p {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .cta-section .btn {
            background: white;
            color: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 1rem 3rem;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-xl);
        }

        .cta-section .btn:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 30px 40px -10px rgba(0, 0, 0, 0.3);
        }

        /* Footer - Refined */
        .footer {
            background: #0B1120;
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .footer-text {
            color: #94A3B8;
            max-width: 300px;
            margin: 1rem 0;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.2s ease;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-links h5 {
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            margin-bottom: 1.5rem;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #94A3B8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
            color: #94A3B8;
        }

        /* Tooltip */
        .tooltip-trigger {
            position: relative;
            cursor: help;
        }

        .tooltip-trigger:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            margin-bottom: 10px;
        }

        /* Animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            section {
                padding: 4rem 0;
            }

            .navbar-custom .navbar-collapse {
                background: var(--secondary);
                padding: 1rem;
                border-radius: 16px;
                margin-top: 1rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .step-card::before {
                display: none;
            }

            .hero-buttons .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom" id="mainNav">
        <div class="container">
            <a class="navbar-brand tooltip-trigger" href="#" data-tooltip="Eastern Visayas Consortium for Industry and Energy and Emerging Technology Research and Development">
                <i class="fas fa-microscope me-2"></i>EVCIEERD
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <span class="hero-badge">Research Innovation in Eastern Visayas</span>
            <h1>Research Databank System for EVCIEERD</h1>
            <p>A centralized platform for managing, tracking, and showcasing research projects across industry, energy, and emerging technologies in Region VIII.</p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary">Start Your Research</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light">Sign In</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="{{ asset('images/EASTERN-VISAYAS-STATE-UNIVERSITY-TACLOBAN-10262022.webp') }}" alt="San Juanico Bridge, Eastern Visayas" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2>About EVCIEERD</h2>
                        <p>The <strong>Eastern Visayas Consortium for Industry and Energy and Emerging Technology Research and Development (EVCIEERD)</strong> brings together academic institutions, research centers, and industry partners across Region VIII.</p>
                        <p>Our Research Databank System provides a centralized digital repository designed to store, manage, and evaluate research initiatives with special focus on industry innovation, energy solutions, and emerging technologies.</p>
                        <p>We support universities, researchers, reviewers, and administrators in promoting collaboration and driving regional development through cutting-edge research.</p>
                        <a href="#features" class="btn btn-primary">Explore Features</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="bg-light">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose EVCIEERD?</h2>
                <p>Powerful features designed for modern research management</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-database"></i>
                        <h3>Smart Repository</h3>
                        <p>Organized, searchable, and secure research storage with advanced categorization for industry and energy research projects.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-user-check"></i>
                        <h3>Fast Review Workflow</h3>
                        <p>Structured approval and tracking system with automated notifications for emerging technology research submissions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-chart-line"></i>
                        <h3>Real-Time Analytics</h3>
                        <p>Monitor research impact and statistics with interactive dashboards tailored for energy sector research.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-users"></i>
                        <h3>Collaboration Tools</h3>
                        <p>Connect researchers across institutions, share findings, and foster interdisciplinary partnerships.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-file-alt"></i>
                        <h3>Smart Submission</h3>
                        <p>Streamlined research submission process with metadata management and version control.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-feature">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Secure Access</h3>
                        <p>Role-based permissions and data encryption ensuring your research remains protected.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Research Projects</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">1,000+</div>
                        <div class="stat-label">Active Researchers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Partner Institutions</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Provinces Covered</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get started with EVCIEERD in three simple steps</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Create Account</h4>
                        <p>Register with your institutional email and verify your affiliation to join the consortium.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Submit Research</h4>
                        <p>Upload and manage your research projects with detailed metadata and documentation.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Review & Publish</h4>
                        <p>Collaborate with reviewers, track approval status, and share your findings.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Transform Research?</h2>
            <p>Join the EVCIEERD community today and be part of the research revolution in industry, energy, and emerging technologies.</p>
            <a href="{{ route('register') }}" class="btn">Create Your Account</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <a class="footer-brand tooltip-trigger" href="#" data-tooltip="Eastern Visayas Consortium for Industry and Energy and Emerging Technology Research and Development">EVCIEERD</a>
                    <p class="footer-text">Advancing research and innovation across Eastern Visayas through collaboration and technology.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-1 col-md-4">
                    <div class="footer-links">
                        <h5>Quick Links</h5>
                        <ul>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#features">Features</a></li>
                            <li><a href="#how-it-works">How It Works</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h5>Resources</h5>
                        <ul>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">Research Guidelines</a></li>
                            <li><a href="#">FAQs</a></li>
                            <li><a href="#">Support</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="footer-links">
                        <h5>Contact</h5>
                        <ul>
                            <li><i class="fas fa-map-marker-alt me-2"></i> Tacloban City, Philippines</li>
                            <li><i class="fas fa-envelope me-2"></i> info@evcieerd.ph</li>
                            <li><i class="fas fa-phone me-2"></i> +63 (53) 123 4567</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">© 2026 Eastern Visayas Consortium for Industry and Energy and Emerging Technology Research and Development. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe elements for fade-up animation
        document.querySelectorAll('.card-feature, .step-card, .section-header, .stat-item, .about-content, .about-image').forEach(el => {
            el.classList.add('fade-up');
            observer.observe(el);
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 300)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>