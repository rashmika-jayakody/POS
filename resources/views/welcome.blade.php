<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PosHere | Modern Retail Solutions</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --navy-dark: #0A1A3D;
            --navy-medium: #1A2B4D;
            --light-blue: #4A9EFF;
            --light-blue-light: #6BB4FF;
            --light-blue-bg: #F0F8FF;
            --accent-teal: #00C9B7;
            --accent-coral: #FF6B82;
            --white: #FFFFFF;
            --gray-light: #F8FAFC;
            --gray-medium: #64748B;
            --gray-dark: #334155;
            --shadow-sm: 0 2px 8px rgba(10, 26, 61, 0.08);
            --shadow-md: 0 8px 24px rgba(10, 26, 61, 0.12);
            --shadow-lg: 0 16px 48px rgba(10, 26, 61, 0.16);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 32px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--white);
            color: var(--navy-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Modern Header */
        header {
            background: var(--white);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo {
            height: 60px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--light-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text p {
            font-size: 0.75rem;
            color: var(--gray-medium);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--navy-medium);
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            padding: 8px 0;
            transition: all 0.3s ease;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--light-blue);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--light-blue);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .login-btn {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Hero Section - Modern */
        .hero {
            padding: 160px 24px 100px;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(74, 158, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 201, 183, 0.1) 0%, transparent 50%);
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 24px;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 48px;
            font-weight: 400;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 18px 40px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::after {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        /* Floating Stats */
        .stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 80px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            padding: 24px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-width: 180px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Packages Section - Modern Cards */
        .packages {
            padding: 120px 24px;
            background: var(--gray-light);
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-title h2 {
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 16px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--light-blue), var(--accent-teal));
            border-radius: 2px;
        }

        .section-title p {
            color: var(--gray-medium);
            font-size: 1.125rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            max-width: 1400px;
            margin: 0 auto;
            align-items: stretch;
        }

        .package-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .package-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .package-card.highlighted {
            border: 2px solid var(--light-blue);
            transform: scale(1.02);
            z-index: 2;
        }

        .package-card.highlighted:hover {
            transform: scale(1.02) translateY(-12px);
        }

        .package-header {
            padding: 40px 24px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .package-card:nth-child(1) .package-header {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: var(--white);
        }

        .package-card:nth-child(2) .package-header {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
        }

        .package-card:nth-child(3) .package-header {
            background: linear-gradient(135deg, #2D3B5D 0%, var(--navy-dark) 100%);
            color: var(--white);
        }

        .package-card:nth-child(4) .package-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: var(--white);
        }

        .package-header h3, .package-header p {
            color: inherit;
        }

        .price {
            font-size: 3.25rem;
            font-weight: 800;
            color: var(--white);
            margin: 16px 0 0;
            position: relative;
            line-height: 1;
        }

        .price span {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.8;
            margin-left: 4px;
        }

        .package-body {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .features {
            list-style: none;
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            flex-grow: 1;
        }

        .features li {
            padding: 0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.925rem;
            color: var(--gray-medium);
            line-height: 1.4;
        }

        .features i.fa-check {
            color: var(--accent-teal);
            margin-top: 3px;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .package-btn {
            width: 100%;
            padding: 16px;
            border-radius: var(--radius-md);
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .package-card:nth-child(2) .package-btn {
            background: var(--light-blue);
            color: var(--white);
        }

        .package-card:not(:nth-child(2)) .package-btn {
            background: var(--gray-light);
            color: var(--navy-dark);
            border: 1px solid var(--gray-200);
        }

        .package-btn:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
        }

        .badge {
            position: absolute;
            top: 20px;
            right: -35px;
            background: #FFD700;
            color: #000;
            padding: 8px 40px;
            font-size: 0.75rem;
            font-weight: 800;
            transform: rotate(45deg);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .features i.fa-times {
            color: var(--gray-medium);
            opacity: 0.5;
        }

        .package-btn {
            display: block;
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .package-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .highlighted {
            border: 2px solid var(--light-blue);
            position: relative;
            margin-top: -20px;
        }

        .badge {
            position: absolute;
            top: 24px;
            right: 24px;
            background: var(--accent-coral);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--shadow-sm);
        }

        /* About Section - Integrated Full Content */
        .about {
            padding: 100px 24px;
            background: var(--white);
        }

        .about-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .about-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 80px;
        }

        .about-main-text h2 {
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 24px;
            line-height: 1.2;
        }

        .about-main-text p {
            font-size: 1.125rem;
            color: var(--gray-medium);
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .about-main-image {
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .about-main-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .about-main-image:hover img {
            transform: scale(1.05);
        }

        /* Vision & Mission */
        .vm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 80px;
        }

        .vm-card {
            background: var(--gray-light);
            padding: 40px;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .vm-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            background: var(--white);
            border-color: var(--light-blue);
        }

        .vm-icon {
            width: 50px;
            height: 50px;
            background: var(--light-blue-bg);
            color: var(--light-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .vm-card h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            color: var(--navy-dark);
            font-weight: 800;
        }

        .vm-card p {
            color: var(--gray-medium);
            font-size: 1.05rem;
            line-height: 1.7;
        }

        /* Offers Grid */
        .offers-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .offers-title h2 {
            font-size: 2.5rem;
            margin-bottom: 16px;
            font-weight: 800;
        }

        .offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 80px;
        }

        .offer-item {
            padding: 24px;
            background: var(--white);
            border-radius: var(--radius-md);
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: flex-start;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .offer-item:hover {
            background: var(--gray-light);
            border-color: var(--light-blue);
            transform: translateY(-3px);
        }

        .offer-icon {
            color: var(--light-blue);
            font-size: 1.5rem;
            width: 40px;
            flex-shrink: 0;
            text-align: center;
        }

        .offer-content h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--navy-dark);
        }

        .offer-content p {
            font-size: 0.95rem;
            color: var(--gray-medium);
            margin: 0;
        }

        /* Growth & Commitment */
        .growth-box {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: var(--white);
            padding: 60px;
            border-radius: var(--radius-xl);
            text-align: center;
            box-shadow: var(--shadow-lg);
            margin-bottom: 60px;
        }

        .growth-box h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--white);
        }

        .growth-box p {
            font-size: 1.15rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
            color: var(--white);
        }

        .commitment-box {
            text-align: center;
            padding: 40px;
        }

        .commitment-box h3 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--navy-dark);
        }

        .commitment-box .slogan {
            font-weight: 800;
            color: var(--light-blue);
            font-size: 1.5rem;
            margin-top: 20px;
        }

        @media (max-width: 1024px) {
            .about-main {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .vm-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Footer */
        footer {
            background: var(--navy-dark);
            color: var(--white);
            padding: 80px 24px 40px;
            position: relative;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 60px;
        }

        .footer-logo {
            height: 40px;
            margin-bottom: 24px;
            filter: brightness(0) invert(1);
        }

        .footer-col h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 32px;
            position: relative;
            padding-bottom: 16px;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--light-blue), var(--accent-teal));
            border-radius: 2px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 16px;
        }

        .footer-col ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-col ul li a:hover {
            color: var(--light-blue);
            transform: translateX(4px);
        }

        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 24px;
        }

        .social-links a {
            background: rgba(255, 255, 255, 0.1);
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--light-blue);
            transform: translateY(-4px);
        }

        .copyright {
            text-align: center;
            padding-top: 60px;
            margin-top: 60px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .copyright a {
            color: var(--light-blue);
            text-decoration: none;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--navy-dark);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .about-container {
                grid-template-columns: 1fr;
                gap: 60px;
            }

            .hero h2 {
                font-size: 2.75rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 80px;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 24px;
                box-shadow: var(--shadow-md);
                gap: 20px;
            }

            .nav-links.active {
                display: flex;
            }

            .packages-grid {
                grid-template-columns: 1fr;
            }

            .highlighted {
                margin-top: 0;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero h2 {
                font-size: 2.25rem;
            }

            .hero p {
                font-size: 1.125rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 320px;
                justify-content: center;
            }

            .stats {
                gap: 20px;
            }

            .stat-item {
                min-width: 140px;
                padding: 20px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <!-- Modern Header -->
    <header>
        <div class="nav-container">
            <div class="logo-section">
                <!-- Replace with your logo path -->
                <img src="/assets/logo.png" alt="PosHere Logo" class="logo">
                <div class="logo-text">
                    <h1>PosHere</h1>
                </div>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="nav-links" id="navLinks">
                <a href="#home">Home</a>
                <a href="#packages">Packages</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <button class="login-btn" onclick="loginToPOS()">Login to POS</button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h2 class="fade-in-up">Revolutionize Your Grocery Store Operations</h2>
            <p class="fade-in-up delay-1">AI-powered POS system designed for modern retailers. Streamline sales,
                inventory, and customer relationships with our intelligent platform.</p>
            <div class="cta-buttons fade-in-up delay-2">
                <button class="btn btn-primary" onclick="startDemo()">
                    <i class="fas fa-play-circle"></i> Start Free Demo
                </button>
                <button class="btn btn-secondary" onclick="scrollToPackages()">
                    <i class="fas fa-chart-line"></i> View Pricing Plans
                </button>
            </div>
            <div class="stats fade-in-up delay-3">
                <div class="stat-item">
                    <div class="stat-number">2,500+</div>
                    <div class="stat-label">Stores Powered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">LKR 10M+</div>
                    <div class="stat-label">Processed Daily</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages" id="packages">
        <div class="section-title">
            <h2 class="fade-in-up">Flexible Pricing Plans</h2>
            <p class="fade-in-up delay-1">Choose the perfect plan that grows with your business</p>
        </div>
        <div class="packages-grid">
            @php $previousFeatures = []; @endphp
            @foreach($plans as $plan)
                @php
                    $isGrowth = $plan->slug === 'growth';
                    $delay = $loop->index;
                    
                    // Logic to show unique features (Pro features)
                    $uniqueFeatures = array_diff($plan->features, $previousFeatures);
                    $previousFeatures = array_unique(array_merge($previousFeatures, $plan->features));
                    
                    $maxFeatures = 6;
                    $displayFeatures = array_slice($uniqueFeatures, 0, $maxFeatures);
                    $hasMore = count($uniqueFeatures) > $maxFeatures;
                @endphp
                <div class="package-card {{ $isGrowth ? 'highlighted' : '' }} fade-in-up {{ $delay > 0 ? 'delay-'.$delay : '' }}">
                    @if($isGrowth)
                        <div class="badge">MOST POPULAR</div>
                    @endif
                    <div class="package-header">
                        <h3>{{ $plan->name }}</h3>
                        <p>{{ $plan->description }}</p>
                        @if($plan->slug === 'custom')
                            <div class="price">{{ __('Custom') }}<span>/{{ __('Tailored') }}</span></div>
                        @else
                            <div class="price">LKR {{ number_format($plan->price_lkr) }}<span>/month</span></div>
                        @endif
                    </div>
                    <div class="package-body">
                        <ul class="features">
                            <li style="color: var(--navy-dark); font-weight: 600;">
                                <i class="fas fa-building" style="color: var(--light-blue);"></i> 
                                <span>{{ $plan->max_branches === -1 ? 'Unlimited' : $plan->max_branches }} {{ $plan->max_branches == 1 ? 'Branch' : 'Branches' }}</span>
                            </li>
                            <li style="color: var(--navy-dark); font-weight: 600; margin-bottom: 8px;">
                                <i class="fas fa-users" style="color: var(--light-blue);"></i> 
                                <span>{{ $plan->max_users === -1 ? 'Unlimited' : $plan->max_users }} {{ $plan->max_users == 1 ? 'User' : 'Users' }}</span>
                            </li>
                            
                            @foreach($displayFeatures as $feature)
                                <li><i class="fas fa-check"></i> <span>{{ ucwords(str_replace('_', ' ', $feature)) }}</span></li>
                            @endforeach
                            @if($hasMore)
                                <li style="color: var(--gray-medium); font-style: italic; opacity: 0.7;"><i class="fas fa-plus-circle"></i> <span>{{ __('And much more...') }}</span></li>
                            @endif
                        </ul>
                        @if($plan->slug === 'custom')
                            <a href="#contact" class="package-btn" style="background: linear-gradient(135deg, var(--gray-dark) 0%, var(--navy-dark) 100%); color: var(--white);">{{ __('Contact Us') }}</a>
                        @else
                            <a href="{{ route('onboarding.index', ['plan' => $plan->slug]) }}" class="package-btn">{{ __('Start :name', ['name' => $plan->name]) }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- About Section - Integrated New Content -->
    <section class="about" id="about">
        <div class="about-container">
            <!-- Main Intro -->
            <div class="about-main">
                <div class="about-main-text scroll-reveal">
                    <h2>Modern Solutions for Modern Retail</h2>
                    <p>PosHere is a modern cloud-based Point of Sale (POS) platform designed to help retail businesses manage their daily operations efficiently and grow with confidence.</p>
                    <p>Our mission is to simplify business management for shop owners by providing powerful tools for sales, inventory, and business insights in one easy-to-use system.</p>
                    <p>Created to support small and growing businesses, PosHere combines industry knowledge with cutting-edge technology to help you make better decisions.</p>
                </div>
                <div class="about-main-image scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Modern Retail POS Interface">
                </div>
            </div>

            <!-- Vision & Mission -->
            <div class="vm-grid">
                <div class="vm-card scroll-reveal">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To empower businesses with simple, intelligent, and reliable POS technology that supports growth and efficiency across all retail sectors.</p>
                </div>
                <div class="vm-card scroll-reveal">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>To provide an affordable and powerful POS solution that helps businesses streamline sales, manage inventory accurately, and gain meaningful insights into their performance.</p>
                </div>
            </div>

            <!-- What We Offer -->
            <div class="offers-title scroll-reveal">
                <h2>What PosHere Offers</h2>
                <p>A complete set of tools designed for the modern retail landscape.</p>
            </div>
            <div class="offers-grid">
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="offer-content">
                        <h4>Fast and Reliable Billing</h4>
                        <p>Lightning-fast POS billing system that keeps your customers happy and reduces wait times.</p>
                    </div>
                </div>
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-boxes"></i></div>
                    <div class="offer-content">
                        <h4>Smart Inventory Management</h4>
                        <p>Complete control over your stock with GRN tracking, batch management, and real-time alerts.</p>
                    </div>
                </div>
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="offer-content">
                        <h4>Detailed Reports</h4>
                        <p>Access sales, inventory, and financial reports that give you a clear picture of your business health.</p>
                    </div>
                </div>
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-users"></i></div>
                    <div class="offer-content">
                        <h4>Loyalty Management</h4>
                        <p>Build lasting relationships with your customers through integrated loyalty and rewards programs.</p>
                    </div>
                </div>
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-store"></i></div>
                    <div class="offer-content">
                        <h4>Multi-Store Control</h4>
                        <p>Manage multiple outlets from a single dashboard, synchronizing stock and viewing consolidated reports.</p>
                    </div>
                </div>
                <div class="offer-item scroll-reveal">
                    <div class="offer-icon"><i class="fas fa-cogs"></i></div>
                    <div class="offer-content">
                        <h4>Business Insights</h4>
                        <p>Tailor your receipts, business reports, and dashboard to match your specific business needs and forecasting.</p>
                    </div>
                </div>
            </div>

            <!-- Growth Box -->
            <div class="growth-box scroll-reveal">
                <h2>Built for Businesses That Want to Grow</h2>
                <p>Whether you run a small retail shop or manage multiple outlets, PosHere is designed to scale with your business. Our platform focuses on simplicity, accuracy, and efficiency so that business owners can spend less time managing systems and more time serving their customers.</p>
            </div>

            <!-- Commitment -->
            <div class="commitment-box scroll-reveal">
                <h3>Our Commitment</h3>
                <p>At PosHere, we are committed to continuous improvement and innovation. We listen to our users, improve our platform, and work to deliver tools that genuinely support business success.</p>
                <div class="slogan">PosHere – Smart POS for Smarter Businesses.</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-container">
            <div class="footer-col">
                <img src="/assets/logo.png" alt="PosHere Logo" class="footer-logo">
                <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px;">Transforming retail operations with
                    intelligent POS solutions: PosHere.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Product</h3>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#packages">Pricing</a></li>
                    <li><a href="#demo">Live Demo</a></li>
                    <li><a href="#api">API Docs</a></li>
                    <li><a href="#updates">Changelog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Resources</h3>
                <ul>
                    <li><a href="#blog">Blog & Guides</a></li>
                    <li><a href="#help">Help Center</a></li>
                    <li><a href="#community">Community</a></li>
                    <li><a href="#webinars">Webinars</a></li>
                    <li><a href="#status">System Status</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <ul style="color: rgba(255,255,255,0.8);">
                    <li><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> Bangalore, India</li>
                    <li><i class="fas fa-phone" style="margin-right: 10px;"></i> +91 80 1234 5678</li>
                    <li><i class="fas fa-envelope" style="margin-right: 10px;"></i> hello@supergrocerspos.com</li>
                    <li><i class="fas fa-clock" style="margin-right: 10px;"></i> 24/7 Support Available</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 PosHere. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms
                    of Service</a></p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenuBtn.innerHTML = navLinks.classList.contains('active')
                ? '<i class="fas fa-times"></i>'
                : '<i class="fas fa-bars"></i>';
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    // Close mobile menu if open
                    navLinks.classList.remove('active');
                    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';

                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Functions
        function startDemo() {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(10, 26, 61, 0.95);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2000;
                padding: 20px;
            `;

            modal.innerHTML = `
                <div style="
                    background: white;
                    padding: 40px;
                    border-radius: 20px;
                    max-width: 500px;
                    width: 100%;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                ">
                    <h3 style="color: #0A1A3D; margin-bottom: 20px;">🚀 Start Your Free Demo</h3>
                    <p style="color: #64748B; margin-bottom: 30px;">Experience our POS system with pre-loaded sample data. No credit card required.</p>
                    <div style="background: #F0F8FF; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                        <p style="margin: 10px 0;"><strong>Demo Store:</strong> FreshMart Grocery</p>
                        <p style="margin: 10px 0;"><strong>Username:</strong> demo@supergrocers.com</p>
                        <p style="margin: 10px 0;"><strong>Password:</strong> demo2023</p>
                    </div>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button onclick="window.location.href='pos-demo.html'" style="
                            background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 10px;
                            font-weight: 600;
                            cursor: pointer;
                            flex: 1;
                        ">Launch Demo</button>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" style="
                            background: transparent;
                            color: #64748B;
                            border: 2px solid #E2E8F0;
                            padding: 15px 30px;
                            border-radius: 10px;
                            font-weight: 600;
                            cursor: pointer;
                            flex: 1;
                        ">Close</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
        }

        function loginToPOS() {
            window.location.href = "{{ route('login') }}";
        }

        function scrollToPackages() {
            document.getElementById('packages').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function selectPackage(packageName) {
            window.location.href = '{{ url("/onboarding") }}?plan=' + packageName;
        }

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.package-card, .section-title h2, .section-title p, .scroll-reveal').forEach(el => {
            observer.observe(el);
        });

        // Add active class to nav links on scroll
        window.addEventListener('scroll', function () {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');

            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
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