<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | PosHere - Smart POS for Smarter Businesses</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
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
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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
            gap: 12px;
            text-decoration: none;
        }

        .logo {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .logo-text h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin: 0;
            letter-spacing: -0.5px;
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

        .nav-links a:hover {
            color: var(--light-blue);
        }

        .login-btn {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            padding: 10px 24px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        /* Hero Page Header */
        .page-header {
            padding: 160px 24px 80px;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-medium) 100%);
            color: var(--white);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(74, 158, 255, 0.1) 0%, transparent 50%);
        }

        .page-header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .page-header p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Content Sections */
        section {
            padding: 100px 24px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* About Intro */
        .about-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-main-text h2 {
            font-size: 2.5rem;
            color: var(--navy-dark);
            margin-bottom: 24px;
            font-weight: 800;
        }

        .about-main-text p {
            font-size: 1.1rem;
            color: var(--gray-medium);
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .about-main-image {
            position: relative;
        }

        .about-main-image img {
            width: 100%;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
        }

        /* Vision & Mission Card Section */
        .vision-mission {
            background: var(--gray-light);
            border-top: 1px solid rgba(0,0,0,0.05);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .vm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .vm-card {
            background: var(--white);
            padding: 50px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .vm-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .vm-icon {
            width: 60px;
            height: 60px;
            background: var(--light-blue-bg);
            color: var(--light-blue);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 24px;
        }

        .vm-card h3 {
            font-size: 1.75rem;
            margin-bottom: 16px;
            color: var(--navy-dark);
            font-weight: 700;
        }

        .vm-card p {
            color: var(--gray-medium);
            font-size: 1.05rem;
            line-height: 1.7;
        }

        /* Features Section */
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
        }

        .offer-item {
            padding: 30px;
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
        }

        .offer-icon {
            color: var(--light-blue);
            font-size: 1.5rem;
            width: 40px;
            flex-shrink: 0;
            text-align: center;
        }

        .offer-content h4 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--navy-dark);
        }

        .offer-content p {
            font-size: 0.95rem;
            color: var(--gray-medium);
            margin: 0;
        }

        /* Growth Section */
        .growth-section {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            text-align: center;
            padding: 100px 24px;
        }

        .growth-section h2 {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 24px;
        }

        .growth-section p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 40px;
            opacity: 0.95;
        }

        .growth-section .cta-btn {
            background: var(--white);
            color: var(--light-blue);
            padding: 18px 40px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .growth-section .cta-btn:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        /* Commitment Footer Section */
        .commitment {
            padding: 80px 24px;
            text-align: center;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .commitment h3 {
            font-size: 2rem;
            margin-bottom: 16px;
            color: var(--navy-dark);
        }

        .commitment p {
            font-size: 1.1rem;
            color: var(--gray-medium);
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .slogan {
            font-weight: 800;
            color: var(--light-blue);
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        /* Footer */
        footer {
            background: var(--navy-dark);
            color: white;
            padding: 80px 24px 40px;
        }

        .footer-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .footer-col h3 {
            font-size: 1.1rem;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-col ul li a:hover {
            color: var(--light-blue);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scroll-reveal {
            opacity: 0;
            transition: all 0.6s ease-out;
        }

        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .about-main, .vm-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .vm-icon {
                margin: 0 auto 24px;
            }
            .page-header h1 {
                font-size: 2.75rem;
            }
        }
    </style>
</head>

<body>
    <!-- Modern Header -->
    <header>
        <div class="nav-container">
            <a href="/" class="logo-section">
                <img src="{{ asset('assets/logo.png') }}" alt="PosHere Logo" class="logo">
                <div class="logo-text">
                    <h1>PosHere</h1>
                </div>
            </a>
            <nav class="nav-links">
                <a href="/">Home</a>
                <a href="/#packages">Packages</a>
                <a href="/about">About</a>
                <a href="/#contact">Contact</a>
                <a href="{{ route('login') }}" class="login-btn">Login to POS</a>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container scroll-reveal visible">
            <h1>About PosHere</h1>
            <p>Empowering retail businesses with reliable technology since 2023.</p>
        </div>
    </div>

    <!-- About Section -->
    <section class="about-container">
        <div class="container">
            <div class="about-main">
                <div class="about-main-text scroll-reveal">
                    <h2>Modern Solutions for Modern Retail</h2>
                    <p>PosHere is a modern cloud-based Point of Sale (POS) platform designed to help retail businesses manage their daily operations efficiently and grow with confidence.</p>
                    <p>Our mission is to simplify business management for shop owners by providing powerful tools for sales, inventory, and business insights in one easy-to-use system.</p>
                </div>
                <div class="about-main-image scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Modern Retail POS">
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="vision-mission">
        <div class="container">
            <div class="vm-grid">
                <div class="vm-card scroll-reveal">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To empower businesses with simple, intelligent, and reliable POS technology that supports growth and efficiency across all sectors of retail.</p>
                </div>
                <div class="vm-card scroll-reveal">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>Our mission is to provide an affordable and powerful POS solution that helps businesses streamline sales, manage inventory accurately, and gain meaningful insights into their performance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed About -->
    <section>
        <div class="container">
            <div class="scroll-reveal" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <p style="font-size: 1.25rem; color: var(--navy-medium); line-height: 1.8;">PosHere was created to support small and growing businesses that need reliable technology without complicated systems. From billing and inventory tracking to advanced business reports, PosHere helps business owners understand their operations and make better decisions.</p>
            </div>
        </div>
    </section>

    <!-- What We Offer -->
    <section style="background: var(--gray-light);">
        <div class="container">
            <div class="offers-title scroll-reveal">
                <h2>What PosHere Offers</h2>
                <p>A complete set of tools designed for the growth of modern retail businesses.</p>
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
                        <h4>Customizable Tools</h4>
                        <p>Tailor your receipts, business reports, and dashboard to match your specific business needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Built for Growth -->
    <section class="growth-section">
        <div class="container">
            <h2 class="scroll-reveal">Built for Businesses That Want to Grow</h2>
            <p class="scroll-reveal">Whether you run a small retail shop or manage multiple outlets, PosHere is designed to scale with your business. Our platform focuses on simplicity, accuracy, and efficiency so that business owners can spend less time managing systems and more time serving their customers.</p>
            <div class="scroll-reveal">
                <a href="/#packages" class="cta-btn">View Pricing Plans</a>
            </div>
        </div>
    </section>

    <!-- Commitment -->
    <section class="commitment">
        <div class="container">
            <div class="scroll-reveal">
                <h3>Our Commitment</h3>
                <p>At PosHere, we are committed to continuous improvement and innovation. We listen to our users, improve our platform, and work to deliver tools that genuinely support business success.</p>
                <div class="slogan">PosHere – Smart POS for Smarter Businesses.</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo" style="height: 40px; filter: brightness(0) invert(1); margin-bottom: 20px;">
                <p style="opacity: 0.7;">PosHere - Empowering your business with smart POS technology.</p>
            </div>
            <div class="footer-col">
                <h3>Product</h3>
                <ul>
                    <li><a href="/#features">Features</a></li>
                    <li><a href="/#packages">Pricing</a></li>
                    <li><a href="/#demo">Live Demo</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Resources</h3>
                <ul>
                    <li><a href="/#help">Help Center</a></li>
                    <li><a href="/#blog">Blog</a></li>
                    <li><a href="/#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <ul style="opacity: 0.7;">
                    <li><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> Bangalore, India</li>
                    <li><i class="fas fa-envelope" style="margin-right: 10px;"></i> hello@supergrocerspos.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 PosHere. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Simple scroll reveal animation
        function reveal() {
            var reveals = document.querySelectorAll(".scroll-reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("visible");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        // Initial check
        reveal();
    </script>
</body>

</html>
