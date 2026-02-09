<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | POS System</title>
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
            --gray-100: #F1F5F9;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-900: #0F172A;
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

        html,
        body {
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
            color: var(--gray-500);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .nav-item:hover,
        .nav-item.active {
            color: var(--light-blue);
            background: rgba(74, 158, 255, 0.08);
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
            box-shadow: 0 4px 12px rgba(74, 158, 255, 0.3);
        }

        .bell-icon {
            font-size: 1.2rem;
            color: var(--gray-medium);
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 70px;
            padding: 28px 24px;
            min-height: calc(100vh - 70px);
            background: var(--gray-light);
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
        }

        .page-subtitle {
            color: var(--gray-medium);
            font-size: 0.85rem;
        }

        .section {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid rgba(74, 158, 255, 0.1);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--light-blue);
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
            text-align: left;
            padding: 12px;
            background: var(--light-blue-bg);
            color: var(--gray-500);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 800;
        }

        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-900);
            font-weight: 500;
        }

        /* Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-badge.inactive {
            background: rgba(100, 116, 139, 0.1);
            color: var(--gray-500);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            border: none;
        }

        .btn-primary {
            background: var(--light-blue);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(74, 158, 255, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 158, 255, 0.3);
        }

        .btn-secondary {
            background: var(--light-blue-bg);
            color: var(--light-blue);
            border: 1px solid var(--light-blue);
        }

        /* Mobile Responsive */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--navy-dark);
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
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade {
            animation: fadeIn 0.3s ease-in;
        }

        @stack('styles')
    </style>
</head>

<body>
    <aside class="sidebar animate-fade" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-chart-line"></i>
                <span>POS Pro</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                @hasrole('system_owner')
                <a href="{{ route('tenants.index') }}" class="nav-item {{ request()->is('tenants*') ? 'active' : '' }}">
                    <i class="fas fa-store-alt"></i>
                    <span>Registered Shops</span>
                </a>
                @endhasrole
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="{{ route('users.index') }}" class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> <span>Users</span>
                </a>
                <a href="{{ route('branches.index') }}"
                    class="nav-item {{ request()->is('branches*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i> <span>Locations</span>
                </a>
                <a href="{{ route('roles.index') }}" class="nav-item {{ request()->is('roles*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> <span>Roles & Permissions</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Operations</div>
                @unlessrole('business_owner|system_owner')
                <a href="{{ route('cash-drawer.index') }}"
                    class="nav-item {{ request()->is('cash-drawer*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i> <span>POS Terminal</span>
                </a>
                @endunlessrole
                <a href="{{ route('products.index') }}"
                    class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> <span>Products & Stock</span>
                </a>
                <a href="{{ route('categories.index') }}"
                    class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> <span>Categories</span>
                </a>
                <a href="{{ route('suppliers.index') }}"
                    class="nav-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> <span>Suppliers</span>
                </a>
                <a href="{{ route('grns.index') }}" class="nav-item {{ request()->is('grns*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> <span>GRN (Goods Received)</span>
                </a>
                <a href="#" class="nav-item"><i class="fas fa-credit-card"></i> <span>Payments</span></a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <header class="animate-fade">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="logo-text"><i class="fas fa-chart-line"></i> POS Dashboard</div>
            </div>

            <div class="header-right">
                <div class="role-badge">
                    <i class="fas fa-crown"></i>
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->roles->first()->name ?? 'Admin')) }}
                </div>
                <i class="fas fa-bell bell-icon"></i>
                <div class="user-section">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--gray-900); font-size: 0.95rem;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size: 0.85rem; color: var(--gray-500);">Account Owner</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
    @stack('scripts')
</body>

</html>