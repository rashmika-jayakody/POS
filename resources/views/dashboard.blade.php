<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | POS System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --shadow-sm: 0 2px 8px rgba(10, 26, 61, 0.08);
            --shadow-md: 0 8px 24px rgba(10, 26, 61, 0.12);
            --shadow-lg: 0 16px 48px rgba(10, 26, 61, 0.16);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            background: var(--white);
            color: var(--navy-dark);
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, var(--white) 0%, rgba(240, 248, 255, 0.8) 100%);
            border-right: 1px solid rgba(74, 158, 255, 0.15);
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(10, 26, 61, 0.08);
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(74, 158, 255, 0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--light-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sidebar-nav {
            padding: 24px 0 140px 0;
        }

        .nav-section {
            margin-bottom: 28px;
        }

        .nav-section-title {
            padding: 0 24px;
            margin-bottom: 12px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray-400);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: var(--gray-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .nav-item:hover {
            color: var(--light-blue);
            background: rgba(74, 158, 255, 0.08);
            border-left-color: var(--light-blue);
        }

        .nav-item.active {
            color: var(--light-blue);
            background: rgba(74, 158, 255, 0.12);
            border-left-color: var(--light-blue);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px 24px;
            border-top: 1px solid var(--gray-100);
            background: var(--white);
        }

        .logout-btn {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 107, 130, 0.1);
            color: var(--accent-coral);
            border: 1px solid rgba(255, 107, 130, 0.2);
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: rgba(255, 107, 130, 0.2);
            border-color: rgba(255, 107, 130, 0.3);
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 70px;
            background: var(--white);
            border-bottom: 1px solid rgba(74, 158, 255, 0.12);
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            height: 100%;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--light-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .role-badge {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--light-blue), var(--accent-teal));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(74, 158, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .bell-icon {
            font-size: 1.2rem;
            color: var(--gray-medium);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .bell-icon:hover {
            color: var(--light-blue);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 70px;
            padding: 28px 24px;
            min-height: calc(100vh - 70px);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.5px;
        }

        .page-title i {
            color: var(--light-blue);
            font-size: 1.6rem;
        }

        .page-subtitle {
            color: var(--gray-medium);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--white) 0%, rgba(240, 248, 255, 0.5) 100%);
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid rgba(74, 158, 255, 0.15);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--light-blue), var(--accent-teal));
        }

        .stat-card.green::before {
            background: linear-gradient(90deg, var(--success), #059669);
        }

        .stat-card.warning::before {
            background: linear-gradient(90deg, var(--warning), #FBBF24);
        }

        .stat-card.danger::before {
            background: linear-gradient(90deg, var(--accent-coral), #FF8FA3);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .stat-card.blue .stat-icon {
            background: linear-gradient(135deg, rgba(74, 158, 255, 0.2), rgba(0, 201, 183, 0.15));
            color: var(--light-blue);
        }

        .stat-card.green .stat-icon {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.15));
            color: var(--success);
        }

        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.15));
            color: var(--warning);
        }

        .stat-card.danger .stat-icon {
            background: linear-gradient(135deg, rgba(255, 107, 130, 0.2), rgba(255, 107, 130, 0.15));
            color: var(--accent-coral);
        }

        .stat-label {
            color: var(--gray-medium);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-change {
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--accent-coral);
        }

        /* Section */
        .section {
            background: linear-gradient(135deg, var(--white) 0%, rgba(240, 248, 255, 0.3) 100%);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid rgba(74, 158, 255, 0.12);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .section:hover {
            box-shadow: var(--shadow-md);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--light-blue-bg);
        }

        .section-title i {
            color: var(--light-blue);
            font-size: 1.1rem;
        }

        /* Tables */
        .table-wrapper {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--light-blue-bg);
            padding: 10px;
            text-align: left;
            font-weight: 700;
            color: var(--navy-dark);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(74, 158, 255, 0.2);
        }

        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(74, 158, 255, 0.08);
            color: var(--gray-dark);
            font-weight: 500;
        }

        .table tbody tr:hover {
            background: var(--light-blue-bg);
        }

        /* List Items */
        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid rgba(74, 158, 255, 0.08);
            transition: all 0.2s ease;
        }

        .list-item:hover {
            padding-left: 8px;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item-info h4 {
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 4px;
            font-size: 0.85rem;
        }

        .list-item-info p {
            color: var(--gray-medium);
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .status-badge.inactive {
            background: rgba(100, 116, 139, 0.15);
            color: var(--gray-medium);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        .status-badge.active .status-dot {
            background: var(--success);
        }

        .status-badge.inactive .status-dot {
            background: var(--gray-500);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .card {
            background: linear-gradient(135deg, rgba(240, 248, 255, 0.6) 0%, rgba(74, 158, 255, 0.03) 100%);
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid rgba(74, 158, 255, 0.15);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, var(--white) 0%, var(--light-blue-bg) 100%);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .card-title {
            font-weight: 700;
            color: var(--navy-dark);
            font-size: 0.85rem;
        }

        .card-badge {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .card-text {
            color: var(--gray-dark);
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-teal) 100%);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(74, 158, 255, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 158, 255, 0.45);
        }

        .btn-secondary {
            background: var(--light-blue-bg);
            color: var(--light-blue);
            border: 2px solid var(--light-blue);
        }

        .btn-secondary:hover {
            background: rgba(74, 158, 255, 0.12);
            border-color: var(--accent-teal);
        }

        /* Progress Bar */
        .progress-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .progress-label {
            color: var(--navy-dark);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .progress-bar {
            flex: 1;
            height: 8px;
            background: rgba(74, 158, 255, 0.15);
            border-radius: 4px;
            margin: 0 12px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.4);
        }

        .progress-fill.green {
            background: linear-gradient(90deg, var(--success), #059669);
        }

        .progress-fill.warning {
            background: linear-gradient(90deg, var(--accent), #FBBF24);
        }

        .progress-percent {
            color: var(--navy-dark);
            font-size: 0.8rem;
            font-weight: 700;
            min-width: 50px;
            text-align: right;
        }

        /* Grid Layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* Mobile Responsive */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--navy-dark);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 260px;
            }

            header {
                left: 260px;
            }

            .main-content {
                margin-left: 260px;
                padding: 32px 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
                transition: transform 0.3s ease;
                width: 280px;
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 24px 16px;
            }

            .sidebar-toggle {
                display: block;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .grid-2, .grid-3 {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .section {
                padding: 20px;
                margin-bottom: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .table {
                font-size: 0.8rem;
            }
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-in {
            animation: slideIn 0.4s ease-in;
        }

        .animate-fade {
            animation: fadeIn 0.3s ease-in;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar animate-fade" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-chart-line"></i>
                <span>POS Pro</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Management -->
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="#" class="nav-item">
                    <i class="fas fa-user-tie"></i>
                    <span>Users</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-store"></i>
                    <span>Locations</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-lock"></i>
                    <span>Permissions</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Roles</span>
                </a>
            </div>

            <!-- Operations -->
            <div class="nav-section">
                <div class="nav-section-title">Operations</div>
                <a href="#" class="nav-item">
                    <i class="fas fa-cash-register"></i>
                    <span>Transactions</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-box"></i>
                    <span>Inventory</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
            </div>

            <!-- Settings -->
            <div class="nav-section">
                <div class="nav-section-title">Settings</div>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Header -->
    <header class="animate-fade">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo-text">
                    <i class="fas fa-chart-line"></i> POS Dashboard
                </div>
            </div>

            <div class="header-right">
                <div class="role-badge">
                    <i class="fas fa-crown"></i>
                    {{ auth()->user()->roles->first()->name ?? 'Admin' | ucfirst }}
                </div>

                <i class="fas fa-bell bell-icon"></i>

                <div class="user-section">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--gray-900); font-size: 0.95rem;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size: 0.85rem; color: var(--gray-500);">
                            Account Owner
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header animate-in">
            <div class="page-title">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </div>
            <div class="page-subtitle">Welcome back, {{ auth()->user()->name }}! Here's your business overview.</div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid animate-in">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-label">Total Sales</div>
                <div class="stat-value">$24,500</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    12.5% from last week
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value">1,284</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    8.2% increase
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-label">In Stock</div>
                <div class="stat-value">3,592</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    2.1% decrease
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="stat-label">Locations</div>
                <div class="stat-value">18</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    2 new added
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="section animate-in">
            <h2 class="section-title">
                <i class="fas fa-receipt"></i>
                Recent Transactions
            </h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#TXN-001024</td>
                            <td>John Anderson</td>
                            <td>$125.50</td>
                            <td>Feb 4, 2:15 PM</td>
                            <td><span class="status-badge active"><span class="status-dot"></span>Completed</span></td>
                            <td>Main Store</td>
                        </tr>
                        <tr>
                            <td>#TXN-001023</td>
                            <td>Sarah Mitchell</td>
                            <td>$89.99</td>
                            <td>Feb 4, 1:45 PM</td>
                            <td><span class="status-badge active"><span class="status-dot"></span>Completed</span></td>
                            <td>Downtown</td>
                        </tr>
                        <tr>
                            <td>#TXN-001022</td>
                            <td>Michael Chen</td>
                            <td>$256.30</td>
                            <td>Feb 4, 1:20 PM</td>
                            <td><span class="status-badge pending"><span class="status-dot"></span>Pending</span></td>
                            <td>Mall Branch</td>
                        </tr>
                        <tr>
                            <td>#TXN-001021</td>
                            <td>Emily Rodriguez</td>
                            <td>$45.00</td>
                            <td>Feb 4, 12:50 PM</td>
                            <td><span class="status-badge active"><span class="status-dot"></span>Completed</span></td>
                            <td>West End</td>
                        </tr>
                        <tr>
                            <td>#TXN-001020</td>
                            <td>David Thompson</td>
                            <td>$167.80</td>
                            <td>Feb 3, 6:30 PM</td>
                            <td><span class="status-badge inactive"><span class="status-dot"></span>Cancelled</span></td>
                            <td>Main Store</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid Layout - Quick Actions & Active Users -->
        <div class="grid-2 animate-in">
            <!-- Quick Actions -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        New Sale
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Add User
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-box-open"></i>
                        Stock Check
                    </button>
                    <button class="btn btn-secondary">
                        <i class="fas fa-file-pdf"></i>
                        Export Report
                    </button>
                </div>
            </div>

            <!-- Active Users -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Active Users Today
                </h2>
                <div>
                    <div class="list-item">
                        <div class="list-item-info">
                            <h4>Jennifer Lee</h4>
                            <p>Cashier - Main Store</p>
                        </div>
                        <span class="status-badge active">
                            <span class="status-dot"></span>
                            Online
                        </span>
                    </div>
                    <div class="list-item">
                        <div class="list-item-info">
                            <h4>Robert Garcia</h4>
                            <p>Manager - Downtown</p>
                        </div>
                        <span class="status-badge active">
                            <span class="status-dot"></span>
                            Online
                        </span>
                    </div>
                    <div class="list-item">
                        <div class="list-item-info">
                            <h4>Patricia Wilson</h4>
                            <p>Supervisor - Mall</p>
                        </div>
                        <span class="status-badge inactive">
                            <span class="status-dot"></span>
                            Offline
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Layout - Store Locations & System Health -->
        <div class="grid-2 animate-in">
            <!-- Store Locations -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Store Locations
                </h2>
                <div class="cards-grid" style="grid-template-columns: 1fr;">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Main Store</div>
                            <span class="card-badge">Active</span>
                        </div>
                        <div class="card-text">
                            <i class="fas fa-map-pin" style="color: var(--primary); margin-right: 6px;"></i>
                            123 Business Ave, Downtown
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Downtown Branch</div>
                            <span class="card-badge">Active</span>
                        </div>
                        <div class="card-text">
                            <i class="fas fa-map-pin" style="color: var(--primary); margin-right: 6px;"></i>
                            456 Retail St, City Center
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Mall Branch</div>
                            <span class="card-badge">Active</span>
                        </div>
                        <div class="card-text">
                            <i class="fas fa-map-pin" style="color: var(--primary); margin-right: 6px;"></i>
                            Shopping Mall, Floor 2
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-heartbeat"></i>
                    System Health
                </h2>
                <div>
                    <div class="progress-item">
                        <span class="progress-label">Database</span>
                        <div class="progress-bar">
                            <div class="progress-fill green" style="width: 94%;"></div>
                        </div>
                        <span class="progress-percent">94%</span>
                    </div>
                    <div class="progress-item">
                        <span class="progress-label">Server CPU</span>
                        <div class="progress-bar">
                            <div class="progress-fill warning" style="width: 67%;"></div>
                        </div>
                        <span class="progress-percent">67%</span>
                    </div>
                    <div class="progress-item">
                        <span class="progress-label">Memory Usage</span>
                        <div class="progress-bar">
                            <div class="progress-fill green" style="width: 82%;"></div>
                        </div>
                        <span class="progress-percent">82%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions -->
        <div class="section animate-in">
            <h2 class="section-title">
                <i class="fas fa-key"></i>
                Roles & Permissions
            </h2>
            <div class="cards-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Super Admin</div>
                    </div>
                    <div class="card-text">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        Full Access<br>
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        All Locations<br>
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        System Admin
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Manager</div>
                    </div>
                    <div class="card-text">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        Location Admin<br>
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        Staff Management<br>
                        <i class="fas fa-times-circle" style="color: var(--gray-400); margin-right: 6px;"></i>
                        System Config
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Cashier</div>
                    </div>
                    <div class="card-text">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        Sales Entry<br>
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        Payment Accept<br>
                        <i class="fas fa-times-circle" style="color: var(--gray-400); margin-right: 6px;"></i>
                        User Admin
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Viewer</div>
                    </div>
                    <div class="card-text">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-right: 6px;"></i>
                        View Reports<br>
                        <i class="fas fa-times-circle" style="color: var(--gray-400); margin-right: 6px;"></i>
                        Edit Content<br>
                        <i class="fas fa-times-circle" style="color: var(--gray-400); margin-right: 6px;"></i>
                        Manage Staff
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar on link click (mobile)
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });

        // Close sidebar when clicking outside (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>
