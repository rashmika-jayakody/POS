<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Grocers POS | Modern Retail Solutions</title>
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
            height: 208px;
            width: auto;
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
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .package-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .package-card:hover {
            transform: translateY(-16px);
            box-shadow: var(--shadow-lg);
        }

        .package-header {
            padding: 40px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .package-card:nth-child(1) .package-header {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
        }

        .package-card:nth-child(2) .package-header {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
        }

        .package-card:nth-child(3) .package-header {
            background: linear-gradient(135deg, var(--navy-medium) 0%, #2D3B5D 100%);
        }

        .package-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
        }

        .package-header h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 12px;
            position: relative;
        }

        .package-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            margin-bottom: 24px;
        }

        .price {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--white);
            margin: 24px 0;
            position: relative;
        }

        .price span {
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.9;
        }

        .package-body {
            padding: 40px 32px;
        }

        .features {
            list-style: none;
            margin-bottom: 40px;
        }

        .features li {
            padding: 14px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .features li:last-child {
            border-bottom: none;
        }

        .features i.fa-check {
            color: var(--accent-teal);
            background: rgba(0, 201, 183, 0.1);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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

        /* About Section */
        .about {
            padding: 120px 24px;
            background: var(--white);
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-image {
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .about-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(74, 158, 255, 0.2), rgba(0, 201, 183, 0.2));
            z-index: 1;
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.5s ease;
        }

        .about-image:hover img {
            transform: scale(1.05);
        }

        .about-content h2 {
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 24px;
            line-height: 1.2;
        }

        .about-content p {
            color: var(--gray-medium);
            line-height: 1.7;
            margin-bottom: 32px;
            font-size: 1.125rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-top: 40px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            background: var(--gray-light);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .feature-icon {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .feature-item h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 8px;
        }

        .feature-item p {
            font-size: 0.95rem;
            color: var(--gray-medium);
            margin: 0;
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
                <img src="/assets/logo.png" alt="Super Grocers POS Logo" class="logo">

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
                    <div class="stat-number">₹10M+</div>
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
            <!-- Essential Plan -->
            <div class="package-card fade-in-up">
                <div class="package-header">
                    <h3>Essential</h3>
                    <p>Perfect for startups & small stores</p>
                    <div class="price">₹999<span>/month</span></div>
                </div>
                <div class="package-body">
                    <ul class="features">
                        <li><i class="fas fa-check"></i> Up to 2,000 products</li>
                        <li><i class="fas fa-check"></i> Basic billing & sales</li>
                        <li><i class="fas fa-check"></i> Customer management</li>
                        <li><i class="fas fa-check"></i> Daily sales reports</li>
                        <li><i class="fas fa-check"></i> Email support</li>
                        <li><i class="fas fa-times"></i> <del>Inventory alerts</del></li>
                        <li><i class="fas fa-times"></i> <del>Multi-store sync</del></li>
                    </ul>
                    <button class="package-btn" onclick="selectPackage('essential')">Start Essential Plan</button>
                </div>
            </div>

            <!-- Professional Plan (Highlighted) -->
            <div class="package-card highlighted fade-in-up delay-1">
                <div class="badge">MOST POPULAR</div>
                <div class="package-header">
                    <h3>Professional</h3>
                    <p>For growing businesses</p>
                    <div class="price">₹2,499<span>/month</span></div>
                </div>
                <div class="package-body">
                    <ul class="features">
                        <li><i class="fas fa-check"></i> Up to 10,000 products</li>
                        <li><i class="fas fa-check"></i> Advanced billing features</li>
                        <li><i class="fas fa-check"></i> Smart inventory management</li>
                        <li><i class="fas fa-check"></i> Supplier & vendor tracking</li>
                        <li><i class="fas fa-check"></i> GST & invoice generation</li>
                        <li><i class="fas fa-check"></i> Low stock alerts</li>
                        <li><i class="fas fa-check"></i> Priority phone support</li>
                    </ul>
                    <button class="package-btn" onclick="selectPackage('professional')">Start Professional Plan</button>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="package-card fade-in-up delay-2">
                <div class="package-header">
                    <h3>Enterprise</h3>
                    <p>For retail chains & large stores</p>
                    <div class="price">₹4,999<span>/month</span></div>
                </div>
                <div class="package-body">
                    <ul class="features">
                        <li><i class="fas fa-check"></i> Unlimited products</li>
                        <li><i class="fas fa-check"></i> Multi-store management</li>
                        <li><i class="fas fa-check"></i> Advanced AI analytics</li>
                        <li><i class="fas fa-check"></i> Custom loyalty programs</li>
                        <li><i class="fas fa-check"></i> Full API access</li>
                        <li><i class="fas fa-check"></i> Custom feature development</li>
                        <li><i class="fas fa-check"></i> 24/7 dedicated support</li>
                    </ul>
                    <button class="package-btn" onclick="selectPackage('enterprise')">Contact Sales</button>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-container">
            <div class="about-image fade-in-up">
                <!-- Replace with your POS screenshot -->
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                    alt="Modern POS Interface">
            </div>
            <div class="about-content">
                <h2 class="fade-in-up">Designed for Modern Retailers</h2>
                <p class="fade-in-up delay-1">We're revolutionizing grocery store management with our intuitive,
                    cloud-based POS system. Built by retail experts and technologists, we combine industry knowledge
                    with cutting-edge technology.</p>
                <p class="fade-in-up delay-1">Our mission is to empower store owners with tools that save time, reduce
                    errors, and boost profitability through intelligent automation and real-time insights.</p>

                <div class="features-grid fade-in-up delay-2">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h4>Lightning Fast</h4>
                            <p>Process transactions in under 2 seconds</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h4>Bank-Grade Security</h4>
                            <p>256-bit encryption & daily backups</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div>
                            <h4>Mobile Ready</h4>
                            <p>Manage from anywhere on any device</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h4>AI Insights</h4>
                            <p>Predictive analytics for smarter decisions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="footer-container">
            <div class="footer-col">
                <img src="logo.png" alt="Logo" class="footer-logo">
                <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px;">Transforming retail operations with
                    intelligent POS solutions since 2023.</p>
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
            <p>&copy; 2023 Super Grocers POS. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms
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
            const packages = {
                'essential': { name: 'Essential Plan', price: '₹999/month' },
                'professional': { name: 'Professional Plan', price: '₹2,499/month' },
                'enterprise': { name: 'Enterprise Plan', price: '₹4,999/month' }
            };

            const selected = packages[packageName];

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
                    <div style="
                        background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%);
                        width: 80px;
                        height: 80px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                        color: white;
                        font-size: 2rem;
                    ">
                        <i class="fas fa-check"></i>
                    </div>
                    <h3 style="color: #0A1A3D; margin-bottom: 10px;">${selected.name} Selected</h3>
                    <p style="color: #00C9B7; font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">${selected.price}</p>
                    <p style="color: #64748B; margin-bottom: 30px;">You'll be redirected to our secure checkout page. 14-day free trial included.</p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button onclick="window.location.href='checkout.html?plan=${packageName}'" style="
                            background: linear-gradient(135deg, #4A9EFF 0%, #00C9B7 100%);
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            border-radius: 10px;
                            font-weight: 600;
                            cursor: pointer;
                            flex: 1;
                        ">Continue to Checkout</button>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" style="
                            background: transparent;
                            color: #64748B;
                            border: 2px solid #E2E8F0;
                            padding: 15px 30px;
                            border-radius: 10px;
                            font-weight: 600;
                            cursor: pointer;
                            flex: 1;
                        ">Cancel</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.remove();
            });
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
        document.querySelectorAll('.package-card, .section-title h2, .section-title p, .about-content h2, .about-content p, .features-grid, .about-image').forEach(el => {
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