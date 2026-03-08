<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | POS System</title>
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
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--white) 0%, rgba(240, 248, 255, 0.8) 100%);
            border-right: 1px solid rgba(74, 158, 255, 0.15);
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(10, 26, 61, 0.08);
        }

        .sidebar-header {
            flex-shrink: 0;
            padding: 24px;
            border-bottom: 1px solid rgba(74, 158, 255, 0.1);
        }

        .sidebar-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
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
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding: 24px 0;
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
            flex-shrink: 0;
            width: 100%;
            padding: 16px 24px 24px;
            border-top: 1px solid var(--gray-100);
            background: var(--white);
            box-sizing: border-box;
        }

        .sidebar-footer form {
            margin: 0;
            width: 100%;
        }

        .logout-btn {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 24px;
            background: rgba(255, 107, 130, 0.08);
            color: var(--accent-coral);
            border: 1px solid rgba(255, 107, 130, 0.2);
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
            font-family: inherit;
        }

        .logout-btn:hover {
            background: rgba(255, 107, 130, 0.15);
            border-color: rgba(255, 107, 130, 0.35);
        }

        .logout-btn i {
            font-size: 1rem;
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
            color: var(--gray-500);
            cursor: pointer;
        }

        /* Language switcher */
        .lang-dropdown {
            position: relative;
        }
        .lang-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-200);
            background: var(--white);
            color: var(--gray-700);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .lang-dropdown-trigger:hover {
            background: var(--gray-100);
            border-color: var(--gray-300);
        }
        .lang-dropdown-trigger i {
            color: var(--gray-500);
        }
        .lang-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 6px;
            min-width: 160px;
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            padding: 6px 0;
            z-index: 1101;
            display: none;
        }
        .lang-dropdown-menu.open {
            display: block;
        }
        .lang-dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: var(--gray-900);
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .lang-dropdown-menu a:hover {
            background: var(--gray-100);
        }
        .lang-dropdown-menu a.active {
            background: var(--light-blue-bg);
            color: var(--light-blue);
            font-weight: 600;
        }

        /* User account dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 6px 10px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: background 0.2s ease;
            border: none;
            background: none;
            color: inherit;
            font: inherit;
        }

        .user-dropdown-trigger:hover {
            background: var(--gray-100);
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            min-width: 200px;
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            padding: 8px 0;
            z-index: 1100;
            display: none;
        }

        .user-dropdown-menu.open {
            display: block;
        }

        .user-dropdown-menu a,
        .user-dropdown-menu button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            text-align: left;
            border: none;
            background: none;
            color: var(--gray-900);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .user-dropdown-menu a:hover,
        .user-dropdown-menu button:hover {
            background: var(--gray-100);
        }

        .user-dropdown-menu a i,
        .user-dropdown-menu button i {
            width: 20px;
            color: var(--gray-500);
        }

        .user-dropdown-menu .dropdown-divider {
            height: 1px;
            background: var(--gray-100);
            margin: 6px 0;
        }

        .user-dropdown-menu button.logout-item {
            color: var(--accent-coral);
        }

        .user-dropdown-menu button.logout-item i {
            color: var(--accent-coral);
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
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        /* Dashboard: stats grid & cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 1px solid rgba(74, 158, 255, 0.1);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 14px;
        }

        .stat-card.blue .stat-icon {
            background: rgba(74, 158, 255, 0.15);
            color: var(--light-blue);
        }

        .stat-card.green .stat-icon {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .stat-card.warning .stat-icon {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .stat-card.danger .stat-icon {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: var(--gray-500);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--navy-dark);
            letter-spacing: -0.5px;
        }

        .stat-card .stat-change {
            font-size: 0.8rem;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-card .stat-change.positive {
            color: var(--success);
        }

        .stat-card .stat-change.negative {
            color: var(--danger);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        @media (max-width: 900px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @keyframes animateIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: animateIn 0.4s ease-out forwards;
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
            background: currentColor;
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

        /* Sidebar hide button - top of side nav */
        .sidebar-hide-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: rgba(74, 158, 255, 0.12);
            border: 1px solid rgba(74, 158, 255, 0.25);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            color: var(--light-blue);
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .sidebar-hide-btn:hover {
            background: rgba(74, 158, 255, 0.2);
            color: var(--navy-dark);
        }

        /* Show menu button - fixed on left edge when sidebar is hidden (desktop only) */
        .sidebar-show-btn {
            display: none;
            position: fixed;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1001;
            width: 40px;
            height: 80px;
            align-items: center;
            justify-content: center;
            background: var(--light-blue);
            color: white;
            border: none;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            box-shadow: 2px 0 12px rgba(74, 158, 255, 0.4);
            cursor: pointer;
            font-size: 1.25rem;
            transition: background 0.2s, width 0.2s;
        }

        .sidebar-show-btn:hover {
            background: var(--navy-medium, #1A2B4D);
            width: 44px;
        }

        body.sidebar-hidden .sidebar-show-btn {
            display: flex;
        }

        /* Header hamburger - mobile only */
        .sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: none;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--navy-dark);
        }

        .sidebar-toggle:hover {
            background: var(--gray-100);
            color: var(--light-blue);
        }

        /* When user hides sidebar - full width content */
        body.sidebar-hidden .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-hidden header {
            left: 0;
        }

        body.sidebar-hidden .main-content {
            margin-left: 0;
        }

        .sidebar {
            transition: transform 0.3s ease;
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
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            body.sidebar-hidden .sidebar,
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
                display: flex;
            }

            .sidebar-show-btn {
                display: none !important;
            }

            .sidebar-hide-btn {
                display: none;
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

        /* Print: hide nav and header so only bill/receipt prints */
        @media print {

            .sidebar,
            header,
            .sidebar-show-btn,
            .sidebar-hide-btn,
            .sidebar-toggle {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                margin-top: 0 !important;
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }
        }

        @stack('styles')
    </style>
    @php
        $businessSettings = auth()->user()->tenant?->businessSetting;
        $businessName = $businessSettings?->display_name ?? optional(auth()->user()->tenant)->name ?? config('app.name');
        $businessLogo = $businessSettings?->logo_path ? asset('storage/' . $businessSettings->logo_path) : null;
        $primaryColor = $businessSettings?->primary_color ?? '#4A9EFF';
        $secondaryColor = $businessSettings?->secondary_color ?? '#0A1A3D';
        $accentColor = $businessSettings?->accent_color ?? '#00C9B7';
    @endphp
    @if ($businessSettings && ($primaryColor || $secondaryColor || $accentColor))
        <style>
            :root {
                @if ($primaryColor)
                    --light-blue:
                        {{ $primaryColor }}
                    ;
                    --light-blue-light:
                        {{ $primaryColor }}
                    ;
                @endif
                @if ($secondaryColor)
                    --navy-dark:
                    {{ $secondaryColor }}
                    ;
                    --navy-medium:
                        {{ $secondaryColor }}
                    ;
                @endif
                @if ($accentColor)
                    --accent-teal:
                    {{ $accentColor }}
                    ;
                @endif
            }
        </style>
    @endif
</head>

<body>
    <aside class="sidebar animate-fade" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-row">
                <div class="sidebar-logo">
                    @if ($businessLogo ?? null)
                        <img src="{{ $businessLogo }}" alt=""
                            style="height: 48px; width: auto; max-width: 180px; object-fit: contain;">
                    @else
                        <i class="fas fa-chart-line"></i>
                    @endif
                    <span>{{ $businessName }}</span>
                </div>
                <button type="button" class="sidebar-hide-btn" id="sidebarHideBtn" title="Hide menu"
                    aria-label="Hide menu">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php
                $posType = auth()->user()->tenant?->pos_type ?? 'retail';
            @endphp
            <div class="nav-section">
                <div class="nav-section-title">{{ __('Main') }}</div>
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                @hasrole('system_owner')
                <a href="{{ route('tenants.index') }}" class="nav-item {{ request()->is('tenants*') ? 'active' : '' }}">
                    <i class="fas fa-store-alt"></i>
                    <span>{{ __('Registered Shops') }}</span>
                </a>
                <a href="{{ route('translations.index') }}" class="nav-item {{ request()->is('translations*') ? 'active' : '' }}">
                    <i class="fas fa-language"></i>
                    <span>{{ __('Customize Translations') }}</span>
                </a>
                @endhasrole
            </div>

            <div class="nav-section">
                <div class="nav-section-title">{{ __('Management') }}</div>
                <a href="{{ route('users.index') }}" class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i> <span>{{ __('Users') }}</span>
                </a>
                <a href="{{ route('branches.index') }}"
                    class="nav-item {{ request()->is('branches*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i> <span>{{ __('Locations') }}</span>
                </a>
                <a href="{{ route('roles.index') }}" class="nav-item {{ request()->is('roles*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> <span>{{ __('Roles & Permissions') }}</span>
                </a>
                @hasrole('business_owner|system_owner')
                <a href="{{ route('business-settings.edit') }}"
                    class="nav-item {{ request()->is('business-settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> <span>{{ __('Business Settings') }}</span>
                </a>
                @endhasrole
                @can('view activity log')
                    <a href="{{ route('activity-logs.index') }}"
                        class="nav-item {{ request()->is('activity-logs*') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> <span>{{ __('Activity Log') }}</span>
                    </a>
                @endcan
            </div>

            <div class="nav-section">
                <div class="nav-section-title">{{ __('Operations') }}</div>
                @php
                    $posType = auth()->user()->tenant?->pos_type ?? 'retail';
                    // Debug: Uncomment to see pos_type value
                    // dd('POS Type: ' . $posType, 'Tenant: ' . auth()->user()->tenant?->name, 'Tenant ID: ' . auth()->user()->tenant_id);
                @endphp
                @if($posType === 'restaurant')
                    {{-- Restaurant-specific menu --}}
                    @unlessrole('business_owner|system_owner')
                    <a href="{{ route('restaurant-cash-drawer.index') }}"
                        class="nav-item {{ request()->is('restaurant-cash-drawer*') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i> <span>{{ __('Restaurant POS') }}</span>
                    </a>
                    @endunlessrole
                    <a href="{{ route('restaurant.tables.index') }}" class="nav-item {{ request()->is('restaurant/tables*') ? 'active' : '' }}">
                        <i class="fas fa-chair"></i> <span>{{ __('Table Management') }}</span>
                    </a>
                    <a href="{{ route('restaurant.orders.index') }}" class="nav-item {{ request()->is('restaurant/orders*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> <span>{{ __('Orders') }}</span>
                    </a>
                    <a href="{{ route('restaurant.kitchen.index') }}" class="nav-item {{ request()->is('restaurant/kitchen*') ? 'active' : '' }}">
                        <i class="fas fa-tv"></i> <span>{{ __('Kitchen Display (KDS)') }}</span>
                    </a>
                    <a href="{{ route('restaurant.reservations.index') }}" class="nav-item {{ request()->is('restaurant/reservations*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> <span>{{ __('Reservations') }}</span>
                    </a>
                    <a href="{{ route('restaurant.customers.index') }}" class="nav-item {{ request()->is('restaurant/customers*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> <span>{{ __('Customers') }}</span>
                    </a>
                    <a href="{{ route('products.index') }}"
                        class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> <span>{{ __('Menu Management') }}</span>
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> <span>{{ __('Menu Categories') }}</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}"
                        class="nav-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> <span>{{ __('Suppliers') }}</span>
                    </a>
                    <a href="{{ route('grns.index') }}" class="nav-item {{ request()->is('grns*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> <span>{{ __('Ingredient Inventory') }}</span>
                    </a>
                    <a href="{{ route('reports.stock-valuation') }}" class="nav-item {{ request()->is('reports/stock-valuation*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle"></i> <span>{{ __('Low Stock Alerts') }}</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> <span>{{ __('Reports & Analytics') }}</span>
                    </a>
                    <a href="#" class="nav-item"><i class="fas fa-credit-card"></i> <span>{{ __('Payments') }}</span></a>
                @else
                    {{-- Retail menu --}}
                    @unlessrole('business_owner|system_owner')
                    <a href="{{ route('cash-drawer.index') }}"
                        class="nav-item {{ request()->is('cash-drawer*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> <span>{{ __('POS Terminal') }}</span>
                    </a>
                    @endunlessrole
                    <a href="{{ route('products.index') }}"
                        class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> <span>{{ __('Products & Stock') }}</span>
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="nav-item {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> <span>{{ __('Categories') }}</span>
                    </a>
                    <a href="{{ route('units.index') }}"
                        class="nav-item {{ request()->is('units*') ? 'active' : '' }}">
                        <i class="fas fa-balance-scale"></i> <span>{{ __('Units') }}</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}"
                        class="nav-item {{ request()->is('suppliers*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> <span>{{ __('Suppliers') }}</span>
                    </a>
                    <a href="{{ route('grns.index') }}" class="nav-item {{ request()->is('grns*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> <span>{{ __('GRN (Goods Received)') }}</span>
                    </a>
                    <a href="{{ route('company-other-expenses.index') }}" class="nav-item {{ request()->is('company-other-expenses*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> <span>{{ __('Other Expenses') }}</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> <span>{{ __('Reports') }}</span>
                    </a>
                    <a href="#" class="nav-item"><i class="fas fa-credit-card"></i> <span>{{ __('Payments') }}</span></a>
                @endif
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </button>
            </form>
        </div>
    </aside>

    <button type="button" class="sidebar-show-btn" id="sidebarShowBtn" title="Show menu" aria-label="Show menu">
        <i class="fas fa-chevron-right"></i>
    </button>

    <header class="animate-fade">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <div class="logo-text" style="display: flex; align-items: center; gap: 10px;">
                    @if ($businessLogo ?? null)
                        <img src="{{ $businessLogo }}" alt=""
                            style="height: 40px; width: auto; max-width: 200px; object-fit: contain;">
                    @else
                        <i class="fas fa-chart-line"></i>
                    @endif
                    <span>{{ $businessName }}</span>
                </div>
            </div>

            <div class="header-right">
                <div class="role-badge">
                    <i class="fas fa-crown"></i>
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->roles->first()->name ?? 'Admin')) }}
                </div>
                <div class="lang-dropdown" id="langDropdown">
                    <button type="button" class="lang-dropdown-trigger" id="langTrigger" aria-expanded="false" aria-haspopup="true" title="{{ app()->getLocale() === 'si' ? 'සිංහල' : 'Language' }}">
                        <i class="fas fa-globe"></i>
                        <span>{{ app()->getLocale() === 'si' ? 'සිංහල' : 'EN' }}</span>
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="lang-dropdown-menu" id="langMenu" role="menu">
                        <a href="{{ route('locale.switch', ['locale' => 'en']) }}" role="menuitem" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">English</a>
                        <a href="{{ route('locale.switch', ['locale' => 'si']) }}" role="menuitem" class="{{ app()->getLocale() === 'si' ? 'active' : '' }}">සිංහල</a>
                    </div>
                </div>
                <i class="fas fa-bell bell-icon"></i>
                <div class="user-dropdown" id="userAccountDropdown">
                    <button type="button" class="user-dropdown-trigger user-section" id="userAccountTrigger"
                        aria-expanded="false" aria-haspopup="true">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: var(--gray-900); font-size: 0.95rem;">
                                {{ auth()->user()->name }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--gray-500);">{{ __('Account') }}</div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--gray-400);"></i>
                    </button>
                    <div class="user-dropdown-menu" id="userAccountMenu" role="menu">
                        <a href="{{ route('profile.edit') }}" role="menuitem"><i class="fas fa-user-cog"></i>
                            {{ __('Profile') }}</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-item" role="menuitem"><i
                                    class="fas fa-sign-out-alt"></i> {{ __('Logout') }}</button>
                        </form>
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
        const sidebarHideBtn = document.getElementById('sidebarHideBtn');
        const sidebarShowBtn = document.getElementById('sidebarShowBtn');

        sidebarHideBtn?.addEventListener('click', () => {
            document.body.classList.add('sidebar-hidden');
        });

        sidebarShowBtn?.addEventListener('click', () => {
            document.body.classList.remove('sidebar-hidden');
        });

        sidebarToggle?.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('active');
            }
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

        // User account dropdown
        const userDropdown = document.getElementById('userAccountDropdown');
        const userTrigger = document.getElementById('userAccountTrigger');
        const userMenu = document.getElementById('userAccountMenu');

        userTrigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('open');
            userTrigger.setAttribute('aria-expanded', userMenu.classList.contains('open'));
        });

        document.addEventListener('click', (e) => {
            if (userDropdown && !userDropdown.contains(e.target)) {
                userMenu.classList.remove('open');
                userTrigger.setAttribute('aria-expanded', 'false');
            }
        });

        // Language dropdown
        const langDropdown = document.getElementById('langDropdown');
        const langTrigger = document.getElementById('langTrigger');
        const langMenu = document.getElementById('langMenu');
        if (langTrigger && langMenu) {
            langTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                langMenu.classList.toggle('open');
                langTrigger.setAttribute('aria-expanded', langMenu.classList.contains('open'));
            });
            document.addEventListener('click', (e) => {
                if (langDropdown && !langDropdown.contains(e.target)) {
                    langMenu.classList.remove('open');
                    langTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>