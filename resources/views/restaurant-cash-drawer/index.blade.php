@extends('layouts.admin')

@section('title', 'Restaurant POS Terminal')

@section('content')
    <style>
        /* Lock html/body and main when on cash drawer - no scrollbar anywhere */
        html:has(.pos-wrapper),
        body:has(.pos-wrapper) {
            overflow: hidden !important;
            height: 100vh !important;
            height: 100dvh !important;
            width: 100%;
            position: relative;
        }
        html:has(.pos-wrapper)::-webkit-scrollbar,
        body:has(.pos-wrapper)::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        html:has(.pos-wrapper),
        body:has(.pos-wrapper) {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .main-content:has(.pos-wrapper) {
            overflow: hidden !important;
            height: calc(100vh - 70px) !important;
            height: calc(100dvh - 70px) !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin-left: 280px !important;
        }
        body.sidebar-hidden .main-content:has(.pos-wrapper) {
            margin-left: 0 !important;
        }
        .main-content:has(.pos-wrapper)::-webkit-scrollbar {
            display: none !important;
        }
        .main-content:has(.pos-wrapper) {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        /* Hide scrollbar on any scrollable element inside pos */
        .pos-wrapper *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .pos-wrapper * {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .pos-dropdown-no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }
        .pos-dropdown-no-scrollbar {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        /* Modern POS Styles - fit to screen, no scroll anywhere. No negative top margin so header does not overlay. */
        .pos-wrapper {
            height: calc(100vh - 126px);
            height: calc(100dvh - 126px);
            min-height: 0;
            display: flex;
            flex-direction: column;
            background: var(--gray-light, #f3f4f6);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0 -24px -28px -24px;
            padding: 12px;
            overflow: hidden;
            box-sizing: border-box;
        }

        .pos-container {
            flex: 1;
            display: grid;
            grid-template-columns: 1.5fr 440px 380px;
            grid-template-rows: auto 1fr;
            gap: 12px;
            overflow: hidden;
            min-height: 0;
        }
        .left-catalog {
            grid-column: 1;
            grid-row: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-width: 0;
            min-height: 0;
        }

        .middle-cart {
            grid-column: 2;
            grid-row: 1 / -1;
            width: 100%;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: var(--radius-md, 12px);
            box-shadow: var(--shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1));
            overflow: hidden;
            flex-shrink: 0;
            min-height: 0;
        }

        .right-controls {
            grid-column: 3;
            grid-row: 1 / -1;
            width: 100%;
            display: flex;
            flex-direction: column;
            background: var(--gray-light, #f8fafc);
            border-radius: var(--radius-md, 12px);
            box-shadow: var(--shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1));
            overflow: hidden;
            min-height: 0;
        }

        .pos-customer-header {
            padding: 16px 20px;
        }
        .right-controls .sidebar-header,
        .right-controls .payment-methods,
        .right-controls .discount-container,
        .right-controls .checkout-actions,
        .right-controls .numpad-section {
            flex-shrink: 0;
        }
        .right-controls .checkout-summary {
            flex: 1 1 auto;
            min-height: 0;
        }
        .checkout-summary {
            flex-shrink: 0;
        }

        .search-wrap input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border-radius: var(--radius-md, 12px);
            border: 1px solid var(--gray-300, #e5e7eb);
            background: white;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .search-wrap input:focus {
            outline: none;
            border-color: var(--light-blue, #3b82f6);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .category-bar {
            background: white;
            padding: 10px 16px;
            border-radius: var(--radius-md, 12px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .category-item {
            padding: 8px 18px;
            border-radius: 10px;
            background: #f9fafb;
            color: var(--gray-500, #4b5563);
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--gray-300, #e5e7eb);
            white-space: nowrap;
        }

        .category-item:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .category-item.active {
            background: var(--light-blue, #3b82f6);
            color: white;
            border-color: var(--light-blue, #3b82f6);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .product-grid-container {
            flex: 1;
            overflow: hidden;
            padding-bottom: 8px;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
            overflow: hidden;
            align-content: start;
            min-height: 0;
            flex: 1;
        }

        .product-card {
            background: white;
            border-radius: var(--radius-md, 12px);
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #f1f5f9;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 180px;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: var(--light-blue, #3b82f6);
        }

        .product-image-wrap {
            width: 100%;
            height: 100px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-light, #f8fafc);
            border-radius: 8px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image-placeholder {
            font-size: 24px;
            color: var(--gray-300, #cbd5e1);
        }

        .product-card .p-unit {
            font-size: 11px;
            color: var(--gray-500, #6b7280);
            background: #f3f4f6;
            padding: 3px 8px;
            border-radius: 6px;
            align-self: flex-start;
        }

        .product-card .p-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-900, #111827);
            line-height: 1.4;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .p-price {
            font-weight: 700;
            font-size: 16px;
            color: var(--success, #10b981);
        }

        .product-card .p-stock {
            font-size: 11px;
            color: var(--danger, #ef4444);
            font-weight: 600;
            background: #fef2f2;
            padding: 2px 6px;
            border-radius: 4px;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .product-card.out-of-stock {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #f3f4f6;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900, #111827);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-container {
            flex: 1;
            overflow: hidden;
            padding: 10px 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .cart-row {
            display: grid;
            grid-template-columns: 1fr 110px 80px 40px;
            padding: 12px 20px;
            border-bottom: 1px solid #f9fafb;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .cart-row:hover { background: #f8fafc; }

        .item-info .item-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-900, #1e293b);
            display: block;
            margin-bottom: 2px;
        }

        .item-info .item-price { font-size: 12px; color: var(--gray-500, #64748b); }

        .qty-controls {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 2px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--gray-300, #e2e8f0);
            cursor: pointer;
            font-weight: 700;
            font-size: 18px;
            color: var(--gray-500, #475569);
            transition: all 0.1s;
        }

        .qty-btn:hover {
            background: #f8fafc;
            color: var(--light-blue, #3b82f6);
            border-color: var(--light-blue, #3b82f6);
        }

        .qty-input {
            width: 40px;
            text-align: center;
            background: transparent;
            border: none;
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-900, #1e293b);
        }

        .qty-input:focus { outline: none; }
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        .item-total {
            font-weight: 700;
            font-size: 14px;
            text-align: right;
            color: var(--gray-900, #0f172a);
        }

        .item-remove {
            color: var(--gray-400, #94a3b8);
            cursor: pointer;
            text-align: center;
            display: flex;
            justify-content: center;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .item-remove:hover {
            color: var(--danger, #ef4444);
            background: #fee2e2;
        }

        .checkout-summary {
            padding: 20px;
            background: var(--gray-light, #f8fafc);
            border-top: 1px solid var(--gray-300, #e2e8f0);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            color: var(--gray-500, #64748b);
        }

        .summary-line.total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--gray-300, #e2e8f0);
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-900, #0f172a);
        }

        .summary-line.total .val { color: var(--success, #10b981); }

        .checkout-actions {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }
        .btn-hold:hover { background: #f1f5f9; border-color: var(--gray-500); color: var(--gray-900); }

        .btn-clear {
            padding: 16px;
            border: 2px solid var(--danger, #ef4444);
            color: var(--danger, #ef4444);
            background: transparent;
            border-radius: var(--radius-md, 12px);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-clear:hover { background: #fef2f2; }

        .btn-pay {
            padding: 16px;
            background: linear-gradient(135deg, var(--light-blue, #3b82f6) 0%, #2563eb 100%);
            color: white;
            border-radius: var(--radius-md, 12px);
            border: none;
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
        }

        .btn-pay:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.show { display: flex; }

        .modal-card {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            padding: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalPop 0.18s ease-out forwards;
        }

        #priceModal .modal-card {
            max-width: 820px;
            min-width: 420px;
            padding: 36px;
        }

        @keyframes modalPop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .price-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            margin-bottom: 12px;
            border: 2px solid #f1f5f9;
            border-radius: var(--radius-md, 12px);
            cursor: pointer;
            transition: all 0.2s;
        }

        .price-option:hover {
            border-color: var(--light-blue, #3b82f6);
            background: #f8fafc;
        }

        .price-option.selected {
            border-color: var(--light-blue, #3b82f6);
            background: #eff6ff;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
        }

        .price-option .label { font-weight: 600; color: #334155; }
        .price-option .amount { font-weight: 700; color: var(--success, #10b981); font-size: 16px; }

        #priceModalTable { width: 100%; border-collapse: collapse; margin: 0; }
        #priceModalTable thead th { text-align: left; padding: 10px 14px; background: var(--light-blue-bg, #eff6ff); color: var(--gray-600); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--gray-200); }
        #priceModalTable tbody tr { cursor: pointer; transition: background 0.15s, border-color 0.15s; border-left: 3px solid transparent; }
        #priceModalTable tbody tr:hover { background: #f8fafc; }
        #priceModalTable tbody tr.price-row-selected { background: #eff6ff; border-left-color: var(--light-blue, #3b82f6); }
        #priceModalTable tbody td { padding: 12px 14px; border-bottom: 1px solid var(--gray-100); font-size: 13px; }
        #priceModalTable tbody td:first-child { font-weight: 600; color: var(--navy-dark); font-size: 13px; }
        #priceModalTable tbody td:last-child { font-weight: 700; color: var(--success, #10b981); text-align: right; font-family: monospace; font-size: 15px; }
        #priceModal .modal-footer-btns { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; }
        #priceModal .modal-footer-btns button { height: 34px; padding: 0 18px; font-size: 12px; font-weight: 600; border-radius: 8px; }

        .numpad-section {
            padding: 12px 20px;
            background: var(--gray-light, #f8fafc);
            border-top: 1px solid var(--gray-300, #e2e8f0);
            flex-shrink: 0;
        }

        .numpad-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .numpad-btn {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid var(--gray-300, #e2e8f0);
            border-radius: 10px;
            font-weight: 700;
            font-size: 18px;
            color: var(--gray-900, #1e293b);
            cursor: pointer;
            transition: all 0.1s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .numpad-btn:hover {
            border-color: var(--light-blue, #3b82f6);
            color: var(--light-blue, #3b82f6);
            background: #eff6ff;
        }

        .numpad-btn:active { transform: scale(0.95); background: #dbeafe; }
        .numpad-btn.clear { color: var(--danger, #ef4444); font-size: 14px; }
        .numpad-btn.action { background: #f1f5f9; font-size: 14px; color: var(--gray-500, #64748b); }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 10px 20px;
            background: var(--gray-light, #f8fafc);
            border-top: 1px solid var(--gray-300, #e2e8f0);
        }

        .payment-btn {
            padding: 10px 5px;
            border: 2px solid var(--gray-300, #e2e8f0);
            border-radius: 10px;
            background: white;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-500, #64748b);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .payment-btn:hover {
            border-color: var(--light-blue, #3b82f6);
            color: var(--light-blue, #3b82f6);
            background: #eff6ff;
        }

        .payment-btn.active {
            border-color: var(--light-blue, #3b82f6);
            background: var(--light-blue, #3b82f6);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .discount-container {
            padding: 10px 20px;
            background: #fff;
            border-top: 1px solid #f1f5f9;
        }

        .discount-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--gray-light, #f8fafc);
            padding: 4px;
            border-radius: 10px;
            border: 1px solid var(--gray-300, #e2e8f0);
        }

        .discount-toggle {
            display: flex;
            background: var(--gray-300, #e2e8f0);
            border-radius: 8px;
            padding: 2px;
        }

        .toggle-btn {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toggle-btn.active {
            background: white;
            color: var(--light-blue, #3b82f6);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .discount-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-900, #1e293b);
            padding: 4px 8px;
            text-align: right;
            width: 100%;
        }

        .discount-input:focus { outline: none; }

        body.sidebar-hidden .pos-wrapper {
            margin-left: 0;
        }

        /* Numpad pinned to bottom of right panel so it's always visible */
        .right-controls .numpad-section {
            margin-top: auto;
        }

        /* Tablet: same 3-column UI, scale down components so nothing overlays or hides */
        @media (max-width: 1200px) {
            .pos-wrapper {
                height: calc(100vh - 126px);
                height: calc(100dvh - 126px);
                padding: 8px;
            }
            .pos-container {
                grid-template-columns: 1fr minmax(280px, 380px) minmax(280px, 360px);
                grid-template-rows: auto 1fr;
                gap: 8px;
            }
            .middle-cart, .right-controls { min-width: 0; }
            .search-wrap input { padding: 10px 12px 10px 40px; font-size: 13px; }
            .category-bar { padding: 8px 12px; gap: 8px; }
            .category-item { padding: 6px 14px; font-size: 12px; }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 10px;
            }
            .product-card {
                min-height: 160px;
                padding: 12px;
            }
            .product-image-wrap { height: 80px; }
            .product-card .p-name { font-size: 13px; min-height: 36px; }
            .product-card .p-price { font-size: 15px; }
            .product-card .p-unit, .product-card .p-stock { font-size: 10px; }
            .sidebar-header { padding: 14px 16px; }
            .sidebar-header h3 { font-size: 16px; }
            .cart-row {
                grid-template-columns: 1fr 90px 70px 36px;
                padding: 10px 12px;
                gap: 6px;
            }
            .item-info .item-name { font-size: 13px; }
            .item-info .item-price { font-size: 11px; }
            .item-total { font-size: 13px; }
            .qty-btn { width: 30px; height: 30px; font-size: 16px; }
            .qty-input { width: 36px; font-size: 13px; }
            .pos-customer-header { padding: 12px 16px; }
            .payment-methods { padding: 8px 16px; gap: 8px; }
            .payment-btn { padding: 8px 4px; font-size: 12px; }
            .discount-container { padding: 8px 16px; }
            .discount-input { font-size: 13px; }
            .toggle-btn { padding: 3px 8px; font-size: 10px; }
            .checkout-summary { padding: 14px 16px; gap: 8px; }
            .checkout-actions { padding: 14px 16px; gap: 12px; }
            .summary-line { font-size: 14px; min-height: 22px; align-items: center; }
            .summary-line.total { font-size: 18px; margin-top: 8px; padding-top: 8px; }
            .summary-line span:last-child, .summary-line input { flex-shrink: 0; min-width: 56px; text-align: right; }
            .btn-clear, .btn-pay { padding: 12px; font-size: 15px; }
            .numpad-section { padding: 10px 16px; }
            .numpad-grid { gap: 6px; }
            .numpad-btn { height: 44px; font-size: 16px; }
            .numpad-btn.clear, .numpad-btn.action { font-size: 12px; }
        }

        /* 1200x800 and similar: reduce right-panel sizes so discount and keypad don't overlay */
        @media (max-width: 1200px) and (max-height: 850px) {
            .right-controls {
                display: flex;
                flex-direction: column;
                min-height: 0;
                overflow-y: auto;
            }
            .right-controls .payment-methods {
                padding: 5px 14px;
                gap: 5px;
                flex-shrink: 0;
            }
            .right-controls .payment-btn {
                padding: 5px 2px;
                font-size: 11px;
            }
            .right-controls .discount-container {
                padding: 5px 14px;
                flex-shrink: 0;
            }
            .right-controls .discount-wrap {
                padding: 3px;
                gap: 6px;
            }
            .right-controls .discount-input {
                font-size: 12px;
            }
            .right-controls .toggle-btn {
                padding: 2px 6px;
                font-size: 9px;
            }
            .right-controls .checkout-summary {
                padding: 8px 14px;
                gap: 4px;
                flex-shrink: 0;
            }
            .right-controls .summary-line {
                font-size: 12px;
                min-height: 18px;
            }
            .right-controls .summary-line.total {
                font-size: 15px;
                margin-top: 4px;
                padding-top: 4px;
            }
            .right-controls .checkout-actions {
                padding: 8px 14px;
                gap: 8px;
                flex-shrink: 0;
            }
            .right-controls .btn-clear,
            .right-controls .btn-pay {
                padding: 8px 10px;
                font-size: 13px;
            }
            .right-controls .numpad-section {
                padding: 6px 14px;
                flex-shrink: 0;
                margin-top: auto;
            }
            .right-controls .numpad-grid {
                gap: 4px;
            }
            .right-controls .numpad-btn {
                height: 34px;
                font-size: 14px;
            }
            .right-controls .numpad-btn.clear,
            .right-controls .numpad-btn.action {
                font-size: 10px;
            }
        }

        @media (max-width: 1024px) {
            .pos-wrapper {
                height: calc(100vh - 126px);
                height: calc(100dvh - 126px);
                min-height: 0;
            }
            .pos-container { gap: 6px; }
            .search-wrap input { padding: 8px 10px 8px 36px; font-size: 12px; }
            .category-bar { padding: 6px 10px; gap: 6px; }
            .category-item { padding: 5px 12px; font-size: 11px; }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 8px;
            }
            .product-card {
                min-height: 120px;
                padding: 10px;
            }
            .product-image-wrap { height: 60px; }
            .product-card .p-name { font-size: 12px; min-height: 32px; }
            .product-card .p-price { font-size: 14px; }
            .sidebar-header { padding: 12px 14px; }
            .sidebar-header h3 { font-size: 15px; }
            .cart-row {
                grid-template-columns: 1fr 80px 62px 32px;
                padding: 8px 10px;
                gap: 5px;
            }
            .item-info .item-name { font-size: 12px; }
            .item-info .item-price { font-size: 10px; }
            .item-total { font-size: 12px; }
            .qty-btn { width: 28px; height: 28px; font-size: 14px; }
            .qty-input { width: 32px; font-size: 12px; }
            .pos-customer-header { padding: 10px 14px; }
            .payment-methods { padding: 6px 14px; gap: 6px; }
            .payment-btn { padding: 6px 2px; font-size: 11px; }
            .discount-container { padding: 6px 14px; }
            .discount-input { font-size: 12px; }
            .toggle-btn { padding: 2px 6px; font-size: 10px; }
            .checkout-summary { padding: 12px 14px; gap: 6px; }
            .checkout-actions { padding: 12px 14px; gap: 10px; }
            .summary-line { font-size: 13px; min-height: 20px; }
            .summary-line.total { font-size: 16px; margin-top: 6px; padding-top: 6px; }
            .btn-clear, .btn-pay { padding: 10px; font-size: 14px; }
            .numpad-section { padding: 8px 14px; }
            .numpad-grid { gap: 5px; }
            .numpad-btn { height: 40px; font-size: 15px; }
            .numpad-btn.clear, .numpad-btn.action { font-size: 11px; }
            /* Prevent overlay: summary and actions stay stacked, no overlap */
            .right-controls { overflow-y: auto; display: flex; flex-direction: column; }
            .right-controls .checkout-summary { flex: 0 0 auto; }
            .right-controls .checkout-actions { flex-shrink: 0; }
        }

        /* Small height: scale down so everything fits, nothing hidden */
        @media (max-height: 600px) {
            .right-controls .checkout-summary { padding: 8px 10px !important; gap: 4px !important; }
            .summary-line { font-size: 12px !important; min-height: 18px !important; }
            .summary-line.total { font-size: 14px !important; margin-top: 4px !important; padding-top: 4px !important; }
            .checkout-actions { padding: 8px 10px !important; }
            .btn-clear, .btn-pay { padding: 8px !important; font-size: 13px !important; }
            .numpad-btn { height: 36px !important; font-size: 14px !important; }
            .numpad-grid { gap: 4px !important; }
        }

        /* Small tablet: hide customer (name, phone, loyalty) section; right-controls fill column */
        @media (max-width: 768px) {
            .pos-customer-section {
                display: none !important;
            }
            .right-controls {
                grid-row: 1 / -1;
            }
        }

        /* Small tablet: same 3-column UI, smaller components, no overlay/hide */
        @media (max-width: 768px) {
            .pos-wrapper {
                margin: -28px -16px -28px -16px;
                padding: 6px;
                height: calc(100vh - 126px);
                height: calc(100dvh - 126px);
            }
            .pos-container {
                grid-template-columns: 1fr minmax(200px, 1fr) minmax(220px, 1fr);
                grid-template-rows: auto 1fr;
                gap: 6px;
            }
            .search-wrap input { padding: 6px 8px 6px 32px; font-size: 11px; }
            .category-bar { padding: 5px 8px; gap: 5px; }
            .category-item { padding: 4px 10px; font-size: 10px; }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 6px;
            }
            .product-card {
                min-height: 120px;
                padding: 8px;
            }
            .product-card .p-name { font-size: 11px; min-height: 28px; }
            .product-card .p-price { font-size: 13px; }
            .product-image-wrap { height: 50px; }
            .sidebar-header { padding: 10px 12px; }
            .sidebar-header h3 { font-size: 14px; }
            .cart-row {
                grid-template-columns: 1fr 64px 54px 28px;
                padding: 6px 10px;
                gap: 4px;
            }
            .item-info .item-name { font-size: 11px; }
            .item-info .item-price { font-size: 10px; }
            .item-total { font-size: 11px; }
            .qty-btn { width: 26px; height: 26px; font-size: 13px; }
            .qty-input { width: 28px; font-size: 11px; }
            .pos-customer-header { padding: 8px 12px; }
            .payment-methods { padding: 5px 12px; gap: 5px; }
            .payment-btn { padding: 5px 2px; font-size: 10px; }
            .discount-container { padding: 5px 12px; }
            .discount-input { font-size: 11px; }
            .toggle-btn { padding: 2px 5px; font-size: 9px; }
            .checkout-summary { padding: 10px 12px; gap: 5px; }
            .checkout-actions { padding: 10px 12px; gap: 8px; grid-template-columns: 1fr 1fr 1fr; }
            .summary-line { font-size: 12px; min-height: 18px; }
            .summary-line.total { font-size: 15px; margin-top: 5px; padding-top: 5px; }
            .btn-clear, .btn-pay { padding: 8px; font-size: 13px; }
            .numpad-section { padding: 6px 12px; }
            .numpad-grid { gap: 5px; }
            .numpad-btn { height: 38px; font-size: 14px; }
            .numpad-btn.clear, .numpad-btn.action { font-size: 10px; }
            .right-controls .numpad-section { margin-top: auto; }
            .right-controls { overflow-y: auto; flex-direction: column; }
            .right-controls .checkout-summary { flex: 0 0 auto; }
            .right-controls .checkout-actions { flex-shrink: 0; }
        }

        /* Phone: same 3-column UI, smallest components, no overlay/hide */
        @media (max-width: 480px) {
            .pos-wrapper {
                margin: -28px -12px -28px -12px;
                padding: 4px;
            }
            .pos-container {
                grid-template-columns: minmax(0, 1fr) minmax(100px, 1fr) minmax(120px, 1fr);
                gap: 4px;
            }
            .search-wrap input { padding: 5px 6px 5px 28px; font-size: 10px; }
            .category-bar { padding: 4px 6px; gap: 4px; }
            .category-item { padding: 3px 8px; font-size: 9px; }
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 4px;
            }
            .product-card {
                min-height: 100px;
                padding: 6px;
            }
            .product-image-wrap { height: 42px; }
            .product-card .p-name { font-size: 10px; min-height: 24px; -webkit-line-clamp: 2; }
            .product-card .p-price { font-size: 11px; }
            .product-card .p-unit, .product-card .p-stock { font-size: 8px; }
            .sidebar-header { padding: 8px 10px; }
            .sidebar-header h3 { font-size: 12px; }
            .pos-customer-section .pos-customer-header { padding: 8px 10px; }
            .pos-customer-section input[type="text"] { padding: 6px 8px !important; font-size: 11px !important; }
            .pos-customer-section button { padding: 5px 6px !important; font-size: 10px !important; }
            .cart-row {
                grid-template-columns: 1fr 52px 44px 24px;
                padding: 5px 8px;
                gap: 3px;
            }
            .item-info .item-name { font-size: 10px; }
            .item-info .item-price { font-size: 9px; }
            .item-total { font-size: 10px; }
            .qty-btn { width: 22px; height: 22px; font-size: 11px; }
            .qty-input { width: 24px; font-size: 10px; }
            .payment-methods { padding: 4px 10px; gap: 4px; }
            .payment-btn { padding: 4px 2px; font-size: 9px; }
            .discount-container { padding: 4px 10px; }
            .discount-input { font-size: 10px; }
            .toggle-btn { padding: 2px 4px; font-size: 8px; }
            .checkout-summary { padding: 8px 10px; gap: 4px; }
            .checkout-actions { padding: 8px 10px; gap: 6px; grid-template-columns: 1fr 1fr 1fr; }
            .summary-line { font-size: 11px; min-height: 16px; }
            .summary-line.total { font-size: 13px; margin-top: 4px; padding-top: 4px; }
            .summary-line input { min-width: 50px !important; padding: 2px 4px !important; font-size: 10px !important; }
            .btn-clear, .btn-pay { padding: 6px; font-size: 11px; }
            .numpad-section { padding: 4px 10px; }
            .numpad-grid { gap: 4px; }
            .numpad-btn { height: 32px; font-size: 12px; }
            .numpad-btn.clear, .numpad-btn.action { font-size: 9px; }
            .right-controls { overflow-y: auto; flex-direction: column; }
            .right-controls .checkout-summary { flex: 0 0 auto; }
            .right-controls .checkout-actions { flex-shrink: 0; }
        }

        @media print {
            @page { size: auto; margin: 12mm; }
            body, .pos-wrapper { margin: 0 !important; padding: 0 !important; background: #fff !important; }
            .pos-wrapper { display: block !important; }
            .pos-wrapper > .no-print { display: none !important; }
            .pos-container { display: none !important; }
            #receipt-print { display: block !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            #receipt-print * { visibility: visible; }
            .receipt-paper { box-shadow: none !important; margin: 0 auto !important; }
        }
        .receipt-paper {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            max-width: 320px;
            width: 100%;
            margin: 0 auto;
            padding: 24px 20px;
            background: #fff;
            color: #111;
            font-size: 14px;
            line-height: 1.4;
            box-sizing: border-box;
        }
        .receipt-paper .receipt-store {
            text-align: center;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.02em;
            margin-bottom: 4px;
            color: #111;
        }
        .receipt-paper .receipt-divider { border: none; border-top: 2px dashed #333; margin: 12px 0; }
        .receipt-paper .receipt-line { border: none; border-top: 1px solid #333; margin: 10px 0; }
        .receipt-paper .receipt-meta { font-size: 12px; color: #444; margin-bottom: 2px; }
        .receipt-paper .receipt-meta strong { color: #111; }
        .receipt-paper table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 12px 0; }
        .receipt-paper th { text-align: left; padding: 6px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #555; border-bottom: 1px solid #333; }
        .receipt-paper th:nth-child(2) { text-align: center; }
        .receipt-paper th:nth-child(3) { text-align: right; }
        .receipt-paper td { padding: 8px 0; border-bottom: 1px dotted #ccc; vertical-align: top; }
        .receipt-paper td:nth-child(2) { text-align: center; }
        .receipt-paper td:nth-child(3) { text-align: right; font-weight: 600; }
        .receipt-paper .receipt-total-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; font-weight: 700; font-size: 16px; border-top: 2px solid #333; margin-top: 8px; }
        .receipt-paper .receipt-sub-row { display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0; color: #444; }
        .receipt-paper .receipt-thanks { text-align: center; margin-top: 24px; padding-top: 12px; font-size: 13px; font-weight: 600; color: #333; border-top: 1px dashed #333; }
        .receipt-paper .receipt-badge { text-align: center; font-size: 14px; font-weight: 800; letter-spacing: 0.08em; padding: 8px 0; margin: 12px 0; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .receipt-paper .receipt-amount { font-size: 20px; font-weight: 800; padding: 12px 0; margin: 12px 0; border-top: 2px solid #333; border-bottom: 2px solid #333; text-align: center; }

        .pos-shortcuts-bar {
            grid-column: 1 / -1;
            display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 6px 14px;
            padding: 8px 16px; background: var(--navy-dark, #0f172a); color: rgba(255,255,255,0.9);
            font-size: 11px; font-weight: 500; border-top: 1px solid rgba(255,255,255,0.1);
        }
        .pos-shortcuts-bar kbd { background: rgba(255,255,255,0.15); padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-right: 2px; }
        .pos-shortcuts-bar span { color: rgba(255,255,255,0.7); }
        .shortcut-hint { font-size: 10px; color: var(--gray-500); margin-left: 4px; font-weight: 600; }
        .shortcut-hint kbd { background: var(--gray-100); padding: 1px 4px; border-radius: 3px; font-size: 9px; }
    </style>

    <div class="pos-wrapper">
        <div class="pos-container no-print">
            <div class="left-catalog">
                <div class="search-wrap" style="display: flex; gap: 8px; align-items: center;">
                    <div style="flex: 1; position: relative;">
                        <input type="text" id="productSearch" placeholder="Search by name, code, barcode or category..." autocomplete="off" style="width: 100%;">
                        <span class="shortcut-hint" data-shortcut-action="search" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;"><kbd>{{ $posShortcuts['search'] ?? 'F2' }}</kbd></span>
                    </div>
                    <button type="button" id="shortcutsHelpBtn" onclick="toggleShortcutsHelp()" title="Keyboard shortcuts" style="flex-shrink: 0; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 8px; background: white; color: var(--gray-600); cursor: pointer; font-size: 14px;"><i class="fas fa-keyboard"></i> <span class="shortcut-hint" data-shortcut-action="help"><kbd>{{ $posShortcuts['help'] ?? 'F1' }}</kbd></span></button>
                </div>
                <div class="category-bar" id="categoryBar">
                    <div class="category-item active" data-category-id="all">All Items</div>
                    @foreach($categories as $category)
                        <div class="category-item" data-category-id="{{ $category->id }}">{{ $category->name }}</div>
                    @endforeach
                </div>
                <div class="product-grid-container">
                    <div class="product-grid" id="productGrid"></div>
                </div>
            </div>

            <div class="middle-cart">
                <div class="sidebar-header" style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                        <h3 style="margin: 0; flex: 1; min-width: 0;">
                            <span>Current Order</span>
                            <span id="invoiceBadge" style="font-size: 11px; background: #eff6ff; color: var(--light-blue, #3b82f6); padding: 4px 8px; border-radius: 6px;">{{ $invoiceNo }}</span>
                        </h3>
                        <div style="position: relative;">
                            <button type="button" id="heldBillsBtn" onclick="toggleHeldBills()" style="display: none; padding: 6px 12px; border: 1px solid var(--gray-300); border-radius: 8px; background: white; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer; white-space: nowrap;"><i class="fas fa-pause-circle" style="margin-right: 4px;"></i>Held <span id="heldBillsCount">0</span></button>
                            <div id="heldBillsDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 4px; min-width: 220px; max-height: 280px; overflow-y: auto; background: white; border: 1px solid var(--gray-300); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 50;"></div>
                        </div>
                    </div>
                    <div id="orderInfoBadges" style="display: flex; gap: 6px; flex-wrap: wrap; font-size: 11px;">
                        <span id="tableBadge" style="display: none; background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                            <i class="fas fa-chair"></i> <span id="tableBadgeName"></span>
                        </span>
                        <span id="waiterBadge" style="display: none; background: rgba(74, 158, 255, 0.1); color: #4A9EFF; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                            <i class="fas fa-user-tie"></i> <span id="waiterBadgeName"></span>
                        </span>
                    </div>
                </div>
                <div class="pos-customer-section" style="flex-shrink: 0; padding: 12px 20px; border-top: 1px solid #f3f4f6;">
                    <!-- Table & Waiter Selection -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">
                                <i class="fas fa-chair" style="margin-right: 4px;"></i>Table (Optional)
                            </label>
                            <select id="selectedTable" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 13px; background: white; cursor: pointer;">
                                <option value="">No Table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" data-name="{{ $table->name }}" data-section="{{ $table->floor_section }}">
                                        {{ $table->name }}@if($table->floor_section) - {{ $table->floor_section }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">
                                <i class="fas fa-user-tie" style="margin-right: 4px;"></i>Waiter (Optional)
                            </label>
                            <select id="selectedWaiter" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 13px; background: white; cursor: pointer;">
                                <option value="">No Waiter</option>
                                @foreach($waiters as $waiter)
                                    <option value="{{ $waiter->id }}" data-name="{{ $waiter->name }}">
                                        {{ $waiter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div id="customerSelectionArea" style="margin-top: 0;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                            <input type="text" id="customerName" placeholder="Customer Name" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 13px;">
                            <input type="text" id="customerPhone" placeholder="Phone" style="padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 13px;">
                        </div>
                        <button onclick="openLoyaltyModal()" id="loyaltyPlaceholderBtn" style="width: 100%; padding: 8px; border: 1px dashed var(--gray-300); background: var(--gray-light); border-radius: 8px; color: var(--gray-500); font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-search" style="margin-right: 6px;"></i> Loyalty Search <span class="shortcut-hint" data-shortcut-action="loyalty"><kbd>{{ $posShortcuts['loyalty'] ?? 'F3' }}</kbd></span>
                        </button>
                        <div id="selectedCustomerDisplay" style="display: none; margin-top: 8px; padding: 10px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 700; color: var(--gray-900); font-size: 13px;">Loyalty Member</div>
                                    <div id="displayCustomerPoints" style="font-size: 11px; color: var(--gray-500);">Points: 0.00</div>
                                </div>
                                <button onclick="resetCustomer()" style="color: var(--danger); background: none; border: none; cursor: pointer; padding: 4px;"><i class="fas fa-times-circle"></i> Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="text-align: center; padding: 60px 40px; color: var(--gray-400, #94a3b8); display: block;" id="emptyCartMsg">
                    <i class="fas fa-shopping-cart" style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p style="font-size: 16px; font-weight: 500;">No items in cart</p>
                    <p style="font-size: 13px;">Select a product to start an order</p>
                </div>
                <div class="cart-container" id="cartContainer"></div>
            </div>

            <div class="right-controls">
                <div class="payment-methods">
                    <button class="payment-btn active" onclick="setPaymentMethod('Cash', this)"><i class="fas fa-money-bill-wave"></i><span>Cash</span></button>
                    <button class="payment-btn" onclick="setPaymentMethod('Card', this)"><i class="fas fa-credit-card"></i><span>Card</span></button>
                    <button class="payment-btn" onclick="setPaymentMethod('Bank', this)"><i class="fas fa-university"></i><span>Bank</span></button>
                </div>
                <div class="discount-container">
                    <div class="discount-wrap">
                        <div class="discount-toggle">
                            <div class="toggle-btn active" id="type-fixed" onclick="setDiscountType('fixed')">Rs</div>
                            <div class="toggle-btn" id="type-percent" onclick="setDiscountType('percent')">%</div>
                        </div>
                        <input type="text" inputmode="decimal" id="discountInput" class="discount-input" placeholder="0.00" value="0.00" onfocus="if(this.value==='0.00') this.value=''" onblur="if(this.value==='') this.value='0.00'" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                    </div>
                </div>
                <div class="checkout-summary">
                    <div class="summary-line"><span>Items</span><span id="summaryItemCount">0</span></div>
                    <div class="summary-line"><span>Subtotal</span><span id="summarySubtotal">0.00</span></div>
                    <div class="summary-line"><span>Discount</span><span id="summaryDiscount" style="color: var(--danger);">- 0.00</span></div>
                    <div class="summary-line total"><span>Total</span><span class="val" id="summaryTotal">0.00</span></div>
                    <div class="summary-line" id="row-received" style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee;">
                        <span>Received (Rs)</span>
                        <input type="text" inputmode="decimal" id="receivedAmount" value="" placeholder="0.00" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); calculateChange()" style="width: 100px; text-align: right; padding: 4px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div class="summary-line" id="row-change" style="font-weight: bold;"><span>Change</span><span id="summaryChange">0.00</span></div>
                </div>
                <div class="checkout-actions" style="grid-template-columns: 1fr 1fr;">
                    <button type="button" class="btn-clear" onclick="clearCart()">Clear <span class="shortcut-hint" data-shortcut-action="clear"><kbd>{{ $posShortcuts['clear'] ?? 'F9' }}</kbd></span></button>
                    <button type="button" class="btn-hold" onclick="holdBill()" style="padding: 16px; border: 2px solid var(--gray-400); color: var(--gray-700); background: white; border-radius: var(--radius-md, 12px); font-weight: 700; cursor: pointer; transition: all 0.2s;"><i class="fas fa-pause-circle" style="margin-right: 4px;"></i>Hold <span class="shortcut-hint" data-shortcut-action="hold"><kbd>{{ $posShortcuts['hold'] ?? 'F5' }}</kbd></span></button>
                </div>
                <div class="checkout-actions" style="grid-template-columns: 1fr 1fr; margin-top: 8px;">
                    <button class="btn-pay" onclick="sendToKitchen()" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); border: none; color: white; padding: 16px; border-radius: var(--radius-md, 12px); font-weight: 700; cursor: pointer; font-size: 15px;">
                        <i class="fas fa-utensils" style="margin-right: 6px;"></i>Send to Kitchen
                    </button>
                    <button class="btn-pay" onclick="processPayment()">Pay & Print <span class="shortcut-hint" data-shortcut-action="pay" style="color: rgba(255,255,255,0.9);"><kbd style="background: rgba(255,255,255,0.25);">{{ $posShortcuts['pay'] ?? 'F8' }}</kbd></span></button>
                </div>
                <div style="padding: 0 20px 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" onclick="newBill()" style="flex: 1; min-width: 100px; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; background: var(--gray-light, #f8fafc); font-size: 13px; font-weight: 600; color: var(--gray-600); cursor: pointer;"><i class="fas fa-utensils" style="margin-right: 4px;"></i>New Order <span class="shortcut-hint" data-shortcut-action="newBill"><kbd>{{ $posShortcuts['newBill'] ?? 'F4' }}</kbd></span></button>
                    <button type="button" onclick="openPendingPaymentsModal()" style="flex: 1; min-width: 100px; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); font-size: 13px; font-weight: 600; color: white; cursor: pointer; position: relative;">
                        <i class="fas fa-receipt" style="margin-right: 4px;"></i>Pending Payments
                        @if($unpaidOrders->count() > 0)
                            <span style="position: absolute; top: -4px; right: -4px; background: #EF4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700;">{{ $unpaidOrders->count() }}</span>
                        @endif
                    </button>
                    <button type="button" onclick="openRefundModal()" style="flex: 1; min-width: 100px; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; background: #fef2f2; font-size: 13px; font-weight: 600; color: var(--danger, #dc2626); cursor: pointer;"><i class="fas fa-undo" style="margin-right: 4px;"></i>Return / Refund <span class="shortcut-hint" data-shortcut-action="refund" style="color: var(--danger);"><kbd>{{ $posShortcuts['refund'] ?? 'F6' }}</kbd></span></button>
                </div>
                <div class="numpad-section">
                    <div class="numpad-grid">
                        <button class="numpad-btn" onclick="handleNumpad('1')">1</button>
                        <button class="numpad-btn" onclick="handleNumpad('2')">2</button>
                        <button class="numpad-btn" onclick="handleNumpad('3')">3</button>
                        <button class="numpad-btn" onclick="handleNumpad('4')">4</button>
                        <button class="numpad-btn" onclick="handleNumpad('5')">5</button>
                        <button class="numpad-btn" onclick="handleNumpad('6')">6</button>
                        <button class="numpad-btn" onclick="handleNumpad('7')">7</button>
                        <button class="numpad-btn" onclick="handleNumpad('8')">8</button>
                        <button class="numpad-btn" onclick="handleNumpad('9')">9</button>
                        <button class="numpad-btn action" onclick="handleNumpad('.')">.</button>
                        <button class="numpad-btn" onclick="handleNumpad('0')">0</button>
                        <button class="numpad-btn clear" onclick="handleNumpad('CLR')">CLR</button>
                    </div>
                </div>
            </div>
            <div id="posShortcutsBar" class="pos-shortcuts-bar no-print"></div>
        </div>

        <div id="shortcutsHelpModal" class="modal-overlay" role="dialog" aria-label="Keyboard shortcuts" onclick="if(event.target===this)toggleShortcutsHelp()">
            <div class="modal-card" style="max-width: 520px;" onclick="event.stopPropagation()">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="margin: 0; font-size: 18px;"><i class="fas fa-keyboard" style="margin-right: 8px;"></i>Keyboard shortcuts</h2>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="resetShortcutsToDefaults()" style="padding: 6px 12px; border: 1px solid var(--gray-300); background: white; border-radius: 6px; cursor: pointer; font-size: 12px;">Reset to defaults</button>
                        <button type="button" onclick="toggleShortcutsHelp()" style="padding: 6px 10px; border: none; background: var(--gray-100); border-radius: 6px; cursor: pointer; font-size: 14px;"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <p style="font-size: 12px; color: var(--gray-500); margin-bottom: 12px;">Click <strong>Change</strong> then press the key you want. Esc and Enter are fixed.</p>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--gray-200);">
                            <th style="text-align: left; padding: 8px 12px;">Key</th>
                            <th style="text-align: left; padding: 8px 12px;">Action</th>
                            <th style="text-align: right; padding: 8px 12px; width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="shortcutsModalTbody"></tbody>
                </table>
            </div>
        </div>

        <div id="priceModal" class="modal-overlay" role="dialog" aria-modal="true">
            <div class="modal-card">
                <div class="table-wrapper" style="max-height: 400px; overflow-y: auto; margin-bottom: 24px; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <table id="priceModalTable">
                        <thead>
                            <tr>
                                <th>Price level</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="priceModalOptions"></tbody>
                    </table>
                </div>
                <div class="modal-footer-btns">
                    <button type="button" class="qty-btn" style="background: #f1f5f9; border: none;" onclick="closePriceModal()">Cancel</button>
                    <button type="button" class="btn-pay" style="padding: 0;" id="confirmPriceBtn">Add with selected price</button>
                </div>
            </div>
        </div>

        <div id="loyaltyModal" class="modal-overlay">
            <div class="modal-card">
                <h2 style="margin-top: 0; font-size: 18px;">Loyalty Customer</h2>
                <p style="color: var(--gray-500); font-size: 14px; margin-bottom: 20px;">Search by name or phone number.</p>
                <div style="position: relative; margin-bottom: 20px;">
                    <input type="text" id="loyaltySearch" placeholder="Phone or name..." style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--gray-300);" oninput="searchLoyaltyCustomers(this.value)">
                    <div id="loyaltySearchResults" class="pos-dropdown-no-scrollbar" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid var(--gray-300); border-radius: 12px; margin-top: 4px; max-height: 200px; overflow-y: auto; z-index: 100; display: none;"></div>
                </div>
                <button class="qty-btn" style="width: 100%; height: 48px; background: #f1f5f9; border: none; font-size: 14px; font-weight: 600;" onclick="closeLoyaltyModal()">Cancel</button>
            </div>
        </div>

        <div id="refundModal" class="modal-overlay">
            <div class="modal-card" style="max-width: 440px;">
                <h2 style="margin-top: 0; font-size: 18px; display: flex; align-items: center; gap: 8px;"><i class="fas fa-undo" style="color: var(--danger, #dc2626);"></i> Return & Refund</h2>
                <p style="color: var(--gray-500); font-size: 13px; margin-bottom: 12px;">Search and select the order to refund.</p>
                <div style="margin-bottom: 12px; position: relative;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">Search order</label>
                    <input type="text" id="refundInvoiceSearch" placeholder="Type order no or customer name..." autocomplete="off" style="width: 100%; padding: 12px 12px 12px 36px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 38px; color: var(--gray-400); font-size: 14px;"></i>
                    <div id="refundInvoiceResults" class="pos-dropdown-no-scrollbar" style="display: none; position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; max-height: 200px; overflow-y: auto; background: white; border: 1px solid var(--gray-300); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 60;"></div>
                </div>
                <div id="refundSelectedInvoice" style="display: none; margin-bottom: 12px; padding: 12px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; font-size: 13px;">
                    <div style="font-weight: 700; color: var(--gray-900); margin-bottom: 4px;"><span id="refundSelInvoiceNo"></span> · <span id="refundSelCustomer"></span></div>
                    <div style="color: var(--gray-600);"><span id="refundSelItems"></span> · Total <span id="refundCurrencyLabel">{{ $currencySymbol ?? 'Rs' }}</span> <span id="refundSelTotal"></span></div>
                    <button type="button" onclick="clearRefundSelection()" style="margin-top: 6px; padding: 4px 8px; font-size: 11px; border: none; background: #fee2e2; color: var(--danger); border-radius: 4px; cursor: pointer;">Change order</button>
                </div>
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">Refund amount ({{ $currencySymbol ?? 'Rs' }}) <span style="color: var(--danger);">*</span></label>
                    <input type="text" inputmode="decimal" id="refundAmount" placeholder="0.00" value="" style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 16px; font-weight: 600; box-sizing: border-box;" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                </div>
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--gray-600); margin-bottom: 8px;">Refund method</label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                        <button type="button" class="refund-method-btn" data-method="Cash" onclick="setRefundMethod('Cash', this)" style="padding: 10px; border: 2px solid var(--gray-300); border-radius: 8px; background: white; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer;"><i class="fas fa-money-bill-wave"></i><br>Cash</button>
                        <button type="button" class="refund-method-btn" data-method="Card" onclick="setRefundMethod('Card', this)" style="padding: 10px; border: 2px solid var(--gray-300); border-radius: 8px; background: white; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer;"><i class="fas fa-credit-card"></i><br>Card</button>
                        <button type="button" class="refund-method-btn" data-method="Bank" onclick="setRefundMethod('Bank', this)" style="padding: 10px; border: 2px solid var(--gray-300); border-radius: 8px; background: white; font-size: 12px; font-weight: 600; color: var(--gray-600); cursor: pointer;"><i class="fas fa-university"></i><br>Bank</button>
                    </div>
                </div>
                <div style="margin-bottom: 12px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 600; color: var(--gray-700);">
                        <input type="checkbox" id="refundUpdateInventory" style="width: 18px; height: 18px; accent-color: var(--light-blue, #3b82f6);" onchange="document.getElementById('refundBranchWrap').style.display = this.checked ? 'block' : 'none';">
                        <span>Update inventory with this return</span>
                    </label>
                    <p style="font-size: 12px; color: var(--gray-500); margin: 4px 0 0 28px;">Returned items will be added back to your assigned branch.</p>
                </div>
                <div id="refundBranchWrap" style="display: none; margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">Branch (for inventory) <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="refundBranchDisplay" value="{{ $inventoryBranchName ?? '' }}" readonly style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 14px; box-sizing: border-box; background: #f8fafc; color: var(--gray-700);">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--gray-600); margin-bottom: 4px;">Reason (optional)</label>
                    <textarea id="refundReason" placeholder="Reason for refund..." rows="2" style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 13px; resize: none; box-sizing: border-box;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button type="button" class="qty-btn" style="height: 48px; background: #f1f5f9; border: none; font-size: 14px; font-weight: 600;" onclick="closeRefundModal()">Cancel</button>
                    <button type="button" class="btn-pay" style="height: 48px; padding: 0; font-size: 14px; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);" id="processRefundBtn" onclick="processRefund()">Process Refund</button>
                </div>
            </div>
        </div>

        <!-- Pending Payments Modal -->
        <div id="pendingPaymentsModal" class="modal-overlay" style="display: none;">
            <div class="modal-card" style="max-width: 700px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.5rem; color: var(--navy-dark);">
                        <i class="fas fa-receipt" style="margin-right: 8px; color: #F59E0B;"></i>Pending Payments
                    </h2>
                    <button type="button" onclick="closePendingPaymentsModal()" style="padding: 6px 10px; border: none; background: var(--gray-100); border-radius: 6px; cursor: pointer; font-size: 14px;"><i class="fas fa-times"></i></button>
                </div>
                <div id="pendingPaymentsList" style="max-height: 500px; overflow-y: auto;">
                    @if($unpaidOrders->isEmpty())
                        <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3; color: #10B981;"></i>
                            <p>No pending payments. All orders are paid!</p>
                        </div>
                    @else
                        @foreach($unpaidOrders as $order)
                            <div onclick="loadOrderForPayment({{ $order->id }})" style="padding: 16px; border: 2px solid var(--gray-200); border-radius: 12px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; background: white;" onmouseover="this.style.borderColor='#F59E0B'; this.style.background='#FFFBEB';" onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='white';">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                    <div>
                                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--navy-dark);">{{ $order->order_no }}</h3>
                                        <div style="font-size: 0.85rem; color: var(--gray-600); margin-top: 4px;">
                                            @if($order->table)
                                                <i class="fas fa-chair"></i> Table: {{ $order->table->name }}
                                            @else
                                                <i class="fas fa-walking"></i> Takeout/Delivery
                                            @endif
                                            @if($order->user)
                                                · Waiter: {{ $order->user->name }}
                                            @endif
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">
                                            <i class="fas fa-clock"></i> {{ $order->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.2rem; font-weight: 700; color: #F59E0B;">{{ $currencySymbol }}{{ number_format($order->grand_total, 2) }}</div>
                                        <span class="status-badge {{ $order->status }}" style="font-size: 0.7rem; padding: 4px 8px; margin-top: 4px; display: inline-block;">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--gray-100);">
                                    <div style="font-size: 0.85rem; color: var(--gray-600);">
                                        <strong>{{ $order->items->count() }}</strong> item(s)
                                        @if($order->customer_name)
                                            · Customer: {{ $order->customer_name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div id="receipt-print" style="display:none;"></div>
    </div>

    <script>
        // Table and Waiter selection handlers
        document.getElementById('selectedTable').addEventListener('change', function() {
            const tableBadge = document.getElementById('tableBadge');
            const tableBadgeName = document.getElementById('tableBadgeName');
            if (this.value) {
                const selectedOption = this.selectedOptions[0];
                tableBadgeName.textContent = selectedOption.dataset.name;
                tableBadge.style.display = 'inline-flex';
            } else {
                tableBadge.style.display = 'none';
            }
        });

        document.getElementById('selectedWaiter').addEventListener('change', function() {
            const waiterBadge = document.getElementById('waiterBadge');
            const waiterBadgeName = document.getElementById('waiterBadgeName');
            if (this.value) {
                const selectedOption = this.selectedOptions[0];
                waiterBadgeName.textContent = selectedOption.dataset.name;
                waiterBadge.style.display = 'inline-flex';
            } else {
                waiterBadge.style.display = 'none';
            }
        });
    </script>
    <script>
        const products = @json($productsJson);
        const categoryNames = @json($categories->pluck('name', 'id'));
        const storeName = @json($storeName);
        const currencySymbol = @json($currencySymbol ?? 'Rs');
        const HELD_STORAGE_KEY = 'pos_held_bills';
        const SHORTCUTS_UPDATE_URL = @json(route('restaurant-cash-drawer.shortcuts.update'));
        const CSRF_TOKEN = @json(csrf_token());

        const SHORTCUT_DEFAULTS = {
            help: 'F1', search: 'F2', loyalty: 'F3', newBill: 'F4', hold: 'F5', refund: 'F6', pay: 'F8', clear: 'F9',
            newBill2: 'Ctrl+N', pay2: 'Ctrl+P'
        };
        let posShortcutsConfig = { ...SHORTCUT_DEFAULTS, ...@json($posShortcuts ?? []) };
        const SHORTCUT_LABELS = {
            help: 'Shortcuts help', search: 'Search', loyalty: 'Loyalty', newBill: 'New order', hold: 'Hold', refund: 'Refund', pay: 'Pay & Print', clear: 'Clear cart',
            newBill2: 'New bill', pay2: 'Pay & Print'
        };
        const SHORTCUT_CONFIG_KEYS = ['help', 'search', 'loyalty', 'newBill', 'hold', 'refund', 'pay', 'clear', 'newBill2', 'pay2'];

        function getShortcutsConfig() {
            return { ...SHORTCUT_DEFAULTS, ...posShortcutsConfig };
        }
        function saveShortcutsConfig(config) {
            fetch(SHORTCUTS_UPDATE_URL, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ shortcuts: config })
            }).then(r => r.json()).then(data => {
                if (data.success && data.shortcuts) posShortcutsConfig = data.shortcuts;
                buildShortcutKeyToAction();
                renderShortcutsBar();
                updateShortcutHints();
                if (document.getElementById('shortcutsHelpModal').classList.contains('show')) renderShortcutsModalTable();
            }).catch(() => {
                buildShortcutKeyToAction();
                renderShortcutsBar();
                updateShortcutHints();
            });
        }
        function updateShortcutHints() {
            const config = getShortcutsConfig();
            document.querySelectorAll('.shortcut-hint[data-shortcut-action]').forEach(el => {
                const kbd = el.querySelector('kbd');
                const key = config[el.dataset.shortcutAction];
                if (kbd && key) kbd.textContent = key;
            });
        }
        function getShortcutKey(e) {
            if (e.key === 'Escape') return 'Esc';
            const pre = (e.ctrlKey ? 'Ctrl+' : '') + (e.metaKey ? 'Meta+' : '') + (e.altKey ? 'Alt+' : '');
            const k = e.key.length === 1 ? e.key.toUpperCase() : e.key;
            return pre + k;
        }
        let shortcutKeyToAction = {};
        function buildShortcutKeyToAction() {
            const config = getShortcutsConfig();
            const actionMap = { help: 'help', search: 'search', loyalty: 'loyalty', newBill: 'newBill', hold: 'hold', refund: 'refund', pay: 'pay', clear: 'clear', newBill2: 'newBill', pay2: 'pay' };
            shortcutKeyToAction = {};
            SHORTCUT_CONFIG_KEYS.forEach(id => {
                const key = config[id];
                if (key) shortcutKeyToAction[key] = actionMap[id] || id;
            });
        }
        function runShortcutAction(actionId) {
            const actions = {
                help: toggleShortcutsHelp,
                search: () => document.getElementById('productSearch').focus(),
                loyalty: openLoyaltyModal,
                newBill: newBill,
                hold: holdBill,
                refund: openRefundModal,
                pay: processPayment,
                clear: clearCart
            };
            if (actions[actionId]) actions[actionId]();
        }
        function renderShortcutsBar() {
            const config = getShortcutsConfig();
            const bar = document.getElementById('posShortcutsBar');
            if (!bar) return;
            const parts = [
                [config.help, 'Help'], [config.search, 'Search'], [config.loyalty, 'Loyalty'], [config.newBill, 'New'], [config.hold, 'Hold'],
                [config.refund, 'Refund'], [config.pay, 'Pay'], [config.clear, 'Clear']
            ];
            bar.innerHTML = parts.map(([key, label]) => `<kbd>${key}</kbd><span>${label}</span>`).join('<span style="opacity:0.4;">|</span>');
        }
        function renderShortcutsModalTable() {
            const config = getShortcutsConfig();
            const rows = [
                { id: 'help', label: 'Shortcuts help' },
                { id: 'search', label: 'Focus product search' },
                { id: 'loyalty', label: 'Loyalty customer search' },
                { id: 'newBill', label: 'New order' },
                { id: 'hold', label: 'Hold current bill' },
                { id: 'refund', label: 'Return / Refund' },
                { id: 'pay', label: 'Pay & Print' },
                { id: 'clear', label: 'Clear cart' },
                { id: 'newBill2', label: 'New order' },
                { id: 'pay2', label: 'Pay & Print' },
                { fixed: true, key: 'Esc', label: 'Close modal' },
                { fixed: true, key: 'Enter', label: 'Confirm (price modal)' }
            ];
            const tbody = document.getElementById('shortcutsModalTbody');
            if (!tbody) return;
            tbody.innerHTML = rows.map(r => {
                if (r.fixed) return `<tr><td style="padding:10px 12px;"><kbd style="background:var(--gray-100);padding:4px 8px;border-radius:4px;font-size:12px;">${r.key}</kbd></td><td style="padding:10px 12px;">${r.label}</td><td></td></tr>`;
                const key = config[r.id] || '';
                return `<tr><td style="padding:10px 12px;"><kbd class="shortcut-kbd" style="background:var(--gray-100);padding:4px 8px;border-radius:4px;font-size:12px;">${key}</kbd></td><td style="padding:10px 12px;">${r.label}</td><td style="padding:10px 12px;"><button type="button" class="btn-change-key" data-action="${r.id}" style="padding:4px 10px;font-size:11px;border:1px solid var(--gray-300);border-radius:6px;background:white;cursor:pointer;">Change</button></td></tr>`;
            }).join('');
            tbody.querySelectorAll('.btn-change-key').forEach(btn => {
                btn.addEventListener('click', function() {
                    const actionId = this.dataset.action;
                    const kbd = this.closest('tr').querySelector('.shortcut-kbd');
                    kbd.textContent = 'Press key...';
                    const once = (e) => {
                        e.preventDefault();
                        document.removeEventListener('keydown', once);
                        const keyStr = getShortcutKey(e);
                        if (keyStr === 'Esc') { kbd.textContent = getShortcutsConfig()[actionId] || ''; return; }
                        const cfg = getShortcutsConfig();
                        cfg[actionId] = keyStr;
                        saveShortcutsConfig(cfg);
                        kbd.textContent = keyStr;
                    };
                    document.addEventListener('keydown', once);
                });
            });
        }
        const COMPLETED_SALES_KEY = 'pos_completed_sales';
        const MAX_COMPLETED_SALES = 100;
        const INVOICE_DATE_KEY = 'pos_invoice_date';
        const INVOICE_SEQ_KEY = 'pos_invoice_seq';
        let cart = [];
        let currentFilter = 'all';
        let searchQuery = '';
        let activeInput = null;
        let paymentMethod = 'Cash';
        let discountType = 'fixed';
        let discountValue = 0;
        let selectedCustomer = null;

        function getCompletedSales() {
            try {
                const raw = localStorage.getItem(COMPLETED_SALES_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) { return []; }
        }
        function saveCompletedSale(sale) {
            const list = getCompletedSales();
            list.unshift(sale);
            if (list.length > MAX_COMPLETED_SALES) list.length = MAX_COMPLETED_SALES;
            localStorage.setItem(COMPLETED_SALES_KEY, JSON.stringify(list));
        }

        function getHeldBills() {
            try {
                const raw = localStorage.getItem(HELD_STORAGE_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) { return []; }
        }
        function setHeldBills(list) {
            localStorage.setItem(HELD_STORAGE_KEY, JSON.stringify(list));
            updateHeldBillsUI();
        }
        function getNextInvoiceNo() {
            const d = new Date();
            const ymd = d.getFullYear() + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
            let seq = parseInt(localStorage.getItem(INVOICE_SEQ_KEY) || '0', 10);
            const savedDate = localStorage.getItem(INVOICE_DATE_KEY) || '';
            if (savedDate !== ymd) seq = 0;
            seq += 1;
            localStorage.setItem(INVOICE_DATE_KEY, ymd);
            localStorage.setItem(INVOICE_SEQ_KEY, String(seq));
            return 'ORD-' + ymd + '-' + String(seq).padStart(4, '0');
        }
        function startNewBill() {
            cart = [];
            discountValue = 0;
            discountType = 'fixed';
            selectedCustomer = null;
            document.getElementById('invoiceBadge').textContent = getNextInvoiceNo();
            const discInput = document.getElementById('discountInput');
            if (discInput) discInput.value = '0.00';
            const nameInput = document.getElementById('customerName');
            const phoneInput = document.getElementById('customerPhone');
            if (nameInput) { nameInput.value = ''; nameInput.readOnly = false; }
            if (phoneInput) { phoneInput.value = ''; phoneInput.readOnly = false; }
            document.getElementById('selectedCustomerDisplay').style.display = 'none';
            document.getElementById('loyaltyPlaceholderBtn').style.display = 'flex';
            document.getElementById('type-fixed').classList.add('active');
            document.getElementById('type-percent').classList.remove('active');
            const receivedInput = document.getElementById('receivedAmount');
            if (receivedInput) receivedInput.value = '';
            document.getElementById('summaryChange').textContent = '0.00';
            renderCart();
            renderProducts();
            document.getElementById('productSearch').focus();
        }
        function holdBill() {
            if (cart.length === 0) return alert('Cart is empty. Nothing to hold.');
            const held = getHeldBills();
            const invoiceNo = document.getElementById('invoiceBadge').textContent;
            const nameInput = document.getElementById('customerName');
            const phoneInput = document.getElementById('customerPhone');
            held.push({
                id: Date.now(),
                invoiceNo,
                cart: JSON.parse(JSON.stringify(cart)),
                customerName: nameInput ? nameInput.value : '',
                customerPhone: phoneInput ? phoneInput.value : '',
                selectedCustomer: selectedCustomer ? JSON.parse(JSON.stringify(selectedCustomer)) : null,
                discountType,
                discountValue,
                heldAt: new Date().toISOString(),
                itemCount: cart.length,
                total: cart.reduce((s, i) => s + lineTotal(i), 0)
            });
            setHeldBills(held);
            startNewBill();
        }
        function newBill() {
            if (cart.length > 0 && !confirm('Hold current bill and start a new one?')) return;
            if (cart.length > 0) holdBill();
            else startNewBill();
        }
        function toggleHeldBills() {
            const el = document.getElementById('heldBillsDropdown');
            if (el.style.display === 'block') { el.style.display = 'none'; return; }
            const held = getHeldBills();
            if (held.length === 0) { el.innerHTML = '<div style="padding: 12px; color: var(--gray-500); font-size: 13px;">No held bills</div>'; }
            else {
                el.innerHTML = held.map((h, i) => `
                    <div style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 8px;" onclick="recallHeldBill(${i}); document.getElementById('heldBillsDropdown').style.display='none';">
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; font-size: 12px; color: var(--gray-900);">${h.invoiceNo}</div>
                            <div style="font-size: 11px; color: var(--gray-500);">${h.itemCount} items · ${currencySymbol} ${(h.total || 0).toFixed(2)}</div>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); removeHeldBill(${i});" style="padding: 4px 8px; border: none; background: #fee2e2; color: var(--danger); border-radius: 4px; font-size: 11px; cursor: pointer;">Remove</button>
                    </div>
                `).join('');
            }
            el.style.display = 'block';
        }
        function recallHeldBill(index) {
            const held = getHeldBills();
            const h = held[index];
            if (!h) return;
            cart = h.cart || [];
            document.getElementById('invoiceBadge').textContent = h.invoiceNo || getNextInvoiceNo();
            discountType = h.discountType || 'fixed';
            discountValue = parseFloat(h.discountValue) || 0;
            selectedCustomer = h.selectedCustomer || null;
            const nameInput = document.getElementById('customerName');
            const phoneInput = document.getElementById('customerPhone');
            if (nameInput) { nameInput.value = h.customerName || ''; nameInput.readOnly = !!selectedCustomer; }
            if (phoneInput) { phoneInput.value = h.customerPhone || ''; phoneInput.readOnly = !!selectedCustomer; }
            document.getElementById('type-fixed').classList.toggle('active', discountType === 'fixed');
            document.getElementById('type-percent').classList.toggle('active', discountType === 'percent');
            document.getElementById('discountInput').value = discountValue.toFixed(2);
            if (selectedCustomer) {
                document.getElementById('selectedCustomerDisplay').style.display = 'block';
                document.getElementById('loyaltyPlaceholderBtn').style.display = 'none';
                document.getElementById('displayCustomerPoints').textContent = 'Points: ' + parseFloat(selectedCustomer.loyalty_points || 0).toFixed(2);
            } else {
                document.getElementById('selectedCustomerDisplay').style.display = 'none';
                document.getElementById('loyaltyPlaceholderBtn').style.display = 'flex';
            }
            held.splice(index, 1);
            setHeldBills(held);
            renderCart();
            document.getElementById('productSearch').focus();
        }
        function removeHeldBill(index) {
            const held = getHeldBills();
            held.splice(index, 1);
            setHeldBills(held);
            toggleHeldBills();
        }
        function updateHeldBillsUI() {
            const held = getHeldBills();
            const btn = document.getElementById('heldBillsBtn');
            const countEl = document.getElementById('heldBillsCount');
            if (btn && countEl) {
                if (held.length > 0) { btn.style.display = 'inline-flex'; countEl.textContent = held.length; }
                else btn.style.display = 'none';
            }
        }

        window.addEventListener('load', () => {
            document.getElementById('invoiceBadge').textContent = getNextInvoiceNo();
            renderProducts();
            initEventListeners();
            updateHeldBillsUI();
            document.getElementById('productSearch').focus();
        });
        document.addEventListener('click', (e) => {
            const dd = document.getElementById('heldBillsDropdown');
            const btn = document.getElementById('heldBillsBtn');
            if (dd && btn && dd.style.display === 'block' && !dd.contains(e.target) && !btn.contains(e.target)) dd.style.display = 'none';
        });

        function initEventListeners() {
            document.querySelectorAll('.category-item').forEach(item => {
                item.addEventListener('click', () => {
                    document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    currentFilter = item.dataset.categoryId;
                    renderProducts();
                });
            });
            document.getElementById('productSearch').addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                renderProducts();
            });
            document.getElementById('productSearch').addEventListener('keydown', (e) => {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const raw = (document.getElementById('productSearch').value || '').trim();
                if (!raw) return;
                const byBarcode = products.filter(p => p.barcode && String(p.barcode).trim() === raw);
                const byCode = products.filter(p => p.code && String(p.code).trim() === raw);
                const match = byBarcode[0] || byCode[0];
                if (match) {
                    // Restaurant menu items don't have stock limits (made to order)
                    const price = (match.prices && match.prices.length === 1) ? match.prices[0].price : match.price;
                    addToCart(match, price);
                    document.getElementById('productSearch').value = '';
                    searchQuery = '';
                    renderProducts();
                    document.getElementById('productSearch').focus();
                }
            });
            buildShortcutKeyToAction();
            renderShortcutsBar();
            updateShortcutHints();
            document.addEventListener('keydown', function posShortcuts(e) {
                const priceOpen = document.getElementById('priceModal').classList.contains('show');
                const loyaltyOpen = document.getElementById('loyaltyModal').classList.contains('show');
                const refundOpen = document.getElementById('refundModal').classList.contains('show');
                const shortcutsOpen = document.getElementById('shortcutsHelpModal').classList.contains('show');
                const inInput = /^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement?.tagName) && !document.activeElement.getAttribute('data-shortcut-ok');
                const keyStr = getShortcutKey(e);

                if (keyStr === 'Esc') {
                    if (shortcutsOpen) { toggleShortcutsHelp(); e.preventDefault(); return; }
                    closePriceModal(); closeLoyaltyModal(); closeRefundModal();
                    e.preventDefault();
                    return;
                }
                if (priceOpen && e.key === 'Enter') {
                    const sel = document.getElementById('priceModalOptions')?.querySelector('tr.price-row-selected');
                    if (sel) { addToCart(activeProduct, parseFloat(sel.dataset.price)); closePriceModal(); }
                    e.preventDefault();
                    return;
                }
                if (priceOpen || loyaltyOpen || refundOpen) return;

                if (shortcutsOpen) {
                    if (shortcutKeyToAction[keyStr] === 'help') { toggleShortcutsHelp(); e.preventDefault(); }
                    return;
                }
                if (inInput) {
                    const config = getShortcutsConfig();
                    if (keyStr !== config.help && keyStr !== config.search) return;
                }
                const action = shortcutKeyToAction[keyStr] || (keyStr === '?' ? 'help' : null);
                if (action) {
                    runShortcutAction(action);
                    e.preventDefault();
                }
            });
            document.addEventListener('focusin', (e) => {
                if (e.target.tagName === 'INPUT') activeInput = e.target;
            });
            document.getElementById('discountInput').addEventListener('input', (e) => {
                discountValue = parseFloat(e.target.value) || 0;
                updateTotals();
            });
            const refundSearchEl = document.getElementById('refundInvoiceSearch');
            if (refundSearchEl) refundSearchEl.addEventListener('input', searchRefundInvoices);
            if (refundSearchEl) refundSearchEl.addEventListener('focus', function() { if (this.value.trim()) searchRefundInvoices(); });
        }
        document.addEventListener('click', (e) => {
            const dd = document.getElementById('refundInvoiceResults');
            const searchEl = document.getElementById('refundInvoiceSearch');
            if (dd && searchEl && dd.style.display === 'block' && !dd.contains(e.target) && !searchEl.contains(e.target)) dd.style.display = 'none';
        });

        function setPaymentMethod(method, el) {
            paymentMethod = method;
            document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
            const receivedRow = document.getElementById('row-received');
            const changeRow = document.getElementById('row-change');
            if (receivedRow && changeRow) {
                if (method === 'Cash') {
                    receivedRow.style.display = 'flex';
                    changeRow.style.display = 'flex';
                } else {
                    receivedRow.style.display = 'none';
                    changeRow.style.display = 'none';
                }
            }
        }

        function calculateChange() {
            const total = parseFloat(document.getElementById('summaryTotal').textContent) || 0;
            const received = parseFloat(document.getElementById('receivedAmount').value) || 0;
            const change = received - total;
            const changeEl = document.getElementById('summaryChange');
            if (changeEl) {
                changeEl.textContent = change.toFixed(2);
                changeEl.style.color = change < 0 ? 'var(--danger)' : 'var(--success)';
            }
        }

        function setDiscountType(type) {
            discountType = type;
            document.getElementById('type-fixed').classList.toggle('active', type === 'fixed');
            document.getElementById('type-percent').classList.toggle('active', type === 'percent');
            updateTotals();
        }

        function handleNumpad(val) {
            const el = activeInput || document.getElementById('productSearch');
            if (!el) return;
            if (val === 'CLR') el.value = '';
            else {
                const start = el.selectionStart, end = el.selectionEnd, text = el.value;
                el.value = text.slice(0, start) + val + text.slice(end);
                el.selectionStart = el.selectionEnd = start + val.length;
            }
            el.focus();
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function renderProducts() {
            const grid = document.getElementById('productGrid');
            const filtered = products.filter(p => {
                const matchesCategory = currentFilter === 'all' || String(p.category_id) === String(currentFilter);
                const catName = (p.category_id && categoryNames[p.category_id]) ? String(categoryNames[p.category_id]).toLowerCase() : '';
                const matchesSearch = p.name.toLowerCase().includes(searchQuery) ||
                    (p.code && String(p.code).toLowerCase().includes(searchQuery)) ||
                    (p.barcode && String(p.barcode).toLowerCase().includes(searchQuery)) ||
                    (catName && catName.includes(searchQuery));
                return matchesCategory && matchesSearch;
            });
            if (filtered.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 80px 40px; color: var(--gray-400); background: white; border-radius: 12px;">No products found</div>';
                return;
            }
            grid.innerHTML = filtered.map(p => `
                <div class="product-card" onclick="handleProductClick(${p.id})">
                    <span class="p-unit">${p.unit || 'unit'}</span>
                    <div class="product-image-wrap">
                        ${p.image ? `<img src="${p.image}" class="product-image" alt="${p.name}">` : `<div class="product-image-placeholder"><i class="fas fa-box"></i></div>`}
                    </div>
                    <div class="p-name">${p.name}</div>
                    <div class="p-price">${currencySymbol} ${parseFloat(p.price).toFixed(2)}</div>
                </div>
            `).join('');
        }

        function handleProductClick(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            if (product.prices && product.prices.length >= 1) {
                openPriceModal(product);
            } else {
                addToCart(product, product.price);
            }
        }

        function lineTotal(item) {
            const raw = item.qty * item.price;
            const dtype = item.discount_type;
            const dval = parseFloat(item.discount_value) || 0;
            if (!dtype || dval <= 0) return raw;
            if (dtype === 'flat') return Math.max(0, raw - (dval * item.qty));
            return Math.max(0, raw - (raw * dval / 100));
        }

        function addToCart(product, price) {
            const existing = cart.find(item => item.id === product.id && parseFloat(item.price) === parseFloat(price));
            if (existing) {
                // Restaurant menu items don't have stock limits (made to order)
                existing.qty = parseFloat(existing.qty) + 1;
            } else {
                // Restaurant menu items don't have stock limits (made to order)
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(price),
                    qty: 1,
                    unit: product.unit || 'unit',
                    maxStock: 999999, // Unlimited for restaurant menu items
                    discount_type: product.discount_type || null,
                    discount_value: parseFloat(product.discount_value) || 0
                });
            }
            renderCart();
        }

        function updateQty(index, delta) {
            // Restaurant menu items don't have stock limits (made to order)
            const newQty = parseFloat(cart[index].qty) + delta;
            cart[index].qty = Math.max(0, newQty);
            if (cart[index].qty <= 0) {
                if (confirm('Remove item from cart?')) cart.splice(index, 1);
                else cart[index].qty = 1;
            }
            renderCart();
        }

        function removeFromCart(index) { cart.splice(index, 1); renderCart(); }

        function clearCart(force) {
            if (force || confirm('Clear current order?')) {
                cart = [];
                discountValue = 0;
                discountType = 'fixed';
                selectedCustomer = null;
                const discInput = document.getElementById('discountInput');
                if (discInput) discInput.value = '0.00';
                const nameInput = document.getElementById('customerName');
                const phoneInput = document.getElementById('customerPhone');
                if (nameInput) { nameInput.value = ''; nameInput.readOnly = false; }
                if (phoneInput) { phoneInput.value = ''; phoneInput.readOnly = false; }
                document.getElementById('selectedCustomerDisplay').style.display = 'none';
                document.getElementById('loyaltyPlaceholderBtn').style.display = 'flex';
                document.getElementById('type-fixed').classList.add('active');
                document.getElementById('type-percent').classList.remove('active');
                const receivedInput = document.getElementById('receivedAmount');
                if (receivedInput) receivedInput.value = '';
                document.getElementById('summaryChange').textContent = '0.00';
                renderCart();
                renderProducts();
                document.getElementById('productSearch').focus();
            }
        }

        function renderCart() {
            const container = document.getElementById('cartContainer');
            const emptyMsg = document.getElementById('emptyCartMsg');
            if (!container) return;
            if (cart.length === 0) {
                container.innerHTML = '';
                if (emptyMsg) emptyMsg.style.display = 'block';
            } else {
                if (emptyMsg) emptyMsg.style.display = 'none';
                container.innerHTML = cart.map((item, i) => {
                    const total = lineTotal(item);
                    const discountLabel = (item.discount_type && (parseFloat(item.discount_value) || 0) > 0)
                        ? (item.discount_type === 'flat' ? ` (${item.qty} × ${currencySymbol} ${parseFloat(item.discount_value).toFixed(2)} off)` : ` (${parseFloat(item.discount_value).toFixed(0)}% off)`)
                        : '';
                    return `
                    <div class="cart-row">
                        <div class="item-info">
                            <span class="item-name">${item.name}</span>
                            <span class="item-price">${currencySymbol} ${parseFloat(item.price).toFixed(2)}${discountLabel}</span>
                        </div>
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="updateQty(${i}, -1)">-</button>
                            <input type="number" class="qty-input" value="${item.qty}" onchange="var val = Math.max(1, parseFloat(this.value)); cart[${i}].qty = val; renderCart();">
                            <button class="qty-btn" onclick="updateQty(${i}, 1)">+</button>
                        </div>
                        <div class="item-total">${currencySymbol} ${total.toFixed(2)}</div>
                        <div class="item-remove" onclick="removeFromCart(${i})"><i class="fas fa-trash-alt"></i></div>
                    </div>
                `;
                }).join('');
            }
            updateTotals();
        }

        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + lineTotal(item), 0);
            const itemCount = cart.reduce((sum, item) => sum + 1, 0);
            let discountAmount = discountType === 'fixed' ? discountValue : (subtotal * discountValue) / 100;
            const total = Math.max(0, subtotal - discountAmount);
            document.getElementById('summaryItemCount').textContent = itemCount;
            document.getElementById('summarySubtotal').textContent = subtotal.toFixed(2);
            document.getElementById('summaryDiscount').textContent = '- ' + discountAmount.toFixed(2);
            document.getElementById('summaryTotal').textContent = total.toFixed(2);
            calculateChange();
        }

        let activeProduct = null;
        function openPriceModal(product) {
            activeProduct = product;
            const priceModalEl = document.getElementById('priceModal');
            const tbody = document.getElementById('priceModalOptions');
            const rowsHtml = product.prices.map((p, i) =>
                `<tr class="price-row ${i === 0 ? 'price-row-selected' : ''}" data-price="${p.price}" onclick="selectPriceRow(this)"><td>${p.label}</td><td>${currencySymbol} ${parseFloat(p.price).toFixed(2)}</td></tr>`
            ).join('');
            priceModalEl.classList.add('show');
            tbody.innerHTML = rowsHtml;
            document.getElementById('confirmPriceBtn').onclick = () => {
                const selected = tbody.querySelector('tr.price-row-selected');
                if (selected) {
                    addToCart(activeProduct, parseFloat(selected.dataset.price));
                    closePriceModal();
                }
            };
        }

        function selectPriceRow(rowEl) {
            const tbody = document.getElementById('priceModalOptions');
            if (!tbody) return;
            tbody.querySelectorAll('tr.price-row').forEach(tr => tr.classList.remove('price-row-selected'));
            rowEl.classList.add('price-row-selected');
        }

        function closePriceModal() {
            document.getElementById('priceModal').classList.remove('show');
            activeProduct = null;
        }

        function toggleShortcutsHelp() {
            const el = document.getElementById('shortcutsHelpModal');
            el.classList.toggle('show');
            if (el.classList.contains('show')) renderShortcutsModalTable();
        }
        function resetShortcutsToDefaults() {
            saveShortcutsConfig({ ...SHORTCUT_DEFAULTS });
        }

        function openLoyaltyModal() {
            document.getElementById('loyaltyModal').classList.add('show');
            document.getElementById('loyaltySearch').focus();
        }

        function closeLoyaltyModal() {
            document.getElementById('loyaltyModal').classList.remove('show');
            document.getElementById('loyaltySearch').value = '';
            document.getElementById('loyaltySearchResults').style.display = 'none';
        }

        let refundMethod = 'Cash';
        let selectedRefundSale = null;
        let refundSearchResults = [];
        function openRefundModal() {
            selectedRefundSale = null;
            document.getElementById('refundInvoiceSearch').value = '';
            document.getElementById('refundInvoiceResults').style.display = 'none';
            document.getElementById('refundInvoiceResults').innerHTML = '';
            document.getElementById('refundSelectedInvoice').style.display = 'none';
            document.getElementById('refundAmount').value = '';
            document.getElementById('refundReason').value = '';
            document.getElementById('refundUpdateInventory').checked = false;
            document.getElementById('refundBranchWrap').style.display = 'none';
            refundMethod = 'Cash';
            document.querySelectorAll('.refund-method-btn').forEach(btn => {
                btn.style.borderColor = btn.dataset.method === 'Cash' ? 'var(--light-blue, #3b82f6)' : 'var(--gray-300)';
                btn.style.background = btn.dataset.method === 'Cash' ? '#eff6ff' : 'white';
                btn.style.color = btn.dataset.method === 'Cash' ? 'var(--light-blue, #3b82f6)' : 'var(--gray-600)';
            });
            document.getElementById('refundModal').classList.add('show');
            document.getElementById('refundInvoiceSearch').focus();
        }
        function closeRefundModal() {
            document.getElementById('refundModal').classList.remove('show');
            selectedRefundSale = null;
        }
        function searchRefundInvoices() {
            const q = (document.getElementById('refundInvoiceSearch').value || '').trim().toLowerCase();
            const list = getCompletedSales();
            if (!q) {
                document.getElementById('refundInvoiceResults').innerHTML = '<div style="padding: 12px; color: var(--gray-500); font-size: 13px;">Type to search by invoice no or customer name.</div>';
                document.getElementById('refundInvoiceResults').style.display = 'block';
                return;
            }
            const matches = list.filter(s => (s.invoiceNo && s.invoiceNo.toLowerCase().includes(q)) || (s.customerName && s.customerName.toLowerCase().includes(q)));
            refundSearchResults = matches.slice(0, 15);
            const el = document.getElementById('refundInvoiceResults');
            if (refundSearchResults.length === 0) {
                el.innerHTML = '<div style="padding: 12px; color: var(--gray-500); font-size: 13px;">No invoices found.</div>';
            } else {
                el.innerHTML = refundSearchResults.map((s, i) => `
                    <div onclick="selectRefundInvoice(refundSearchResults[${i}])" style="padding: 10px 12px; border-bottom: 1px solid #f1f5f9; cursor: pointer; font-size: 13px;">
                        <div style="font-weight: 600; color: var(--gray-900);">${s.invoiceNo}</div>
                        <div style="color: var(--gray-500); font-size: 12px;">${s.customerName || '—'} · ${(s.items || []).length} items · ${currencySymbol} ${(s.total || 0).toFixed(2)}</div>
                    </div>
                `).join('');
            }
            el.style.display = 'block';
        }
        function selectRefundInvoice(sale) {
            if (!sale) return;
            selectedRefundSale = sale;
            document.getElementById('refundInvoiceSearch').value = '';
            document.getElementById('refundInvoiceResults').style.display = 'none';
            document.getElementById('refundSelectedInvoice').style.display = 'block';
            document.getElementById('refundSelInvoiceNo').textContent = sale.invoiceNo || '—';
            document.getElementById('refundSelCustomer').textContent = sale.customerName || '—';
            const itemCount = (sale.items || []).length;
            document.getElementById('refundSelItems').textContent = itemCount + ' item' + (itemCount !== 1 ? 's' : '');
            document.getElementById('refundSelTotal').textContent = (sale.total || 0).toFixed(2);
            document.getElementById('refundAmount').value = (sale.total || 0).toFixed(2);
            document.getElementById('refundAmount').focus();
        }
        function clearRefundSelection() {
            selectedRefundSale = null;
            document.getElementById('refundSelectedInvoice').style.display = 'none';
            document.getElementById('refundInvoiceSearch').value = '';
            document.getElementById('refundAmount').value = '';
            document.getElementById('refundInvoiceSearch').focus();
        }
        function setRefundMethod(method, el) {
            refundMethod = method;
            document.querySelectorAll('.refund-method-btn').forEach(btn => {
                const active = btn.dataset.method === method;
                btn.style.borderColor = active ? 'var(--light-blue, #3b82f6)' : 'var(--gray-300)';
                btn.style.background = active ? '#eff6ff' : 'white';
                btn.style.color = active ? 'var(--light-blue, #3b82f6)' : 'var(--gray-600)';
            });
        }
        function processRefund() {
            const amount = parseFloat(document.getElementById('refundAmount').value) || 0;
            if (amount <= 0) {
                alert('Please enter a valid refund amount.');
                document.getElementById('refundAmount').focus();
                return;
            }
            const reason = (document.getElementById('refundReason').value || '').trim();
            const updateInventory = document.getElementById('refundUpdateInventory').checked;
            const reference = selectedRefundSale ? selectedRefundSale.invoiceNo : '';
            const date = new Date().toLocaleString();

            if (updateInventory && selectedRefundSale && (selectedRefundSale.items || []).length > 0) {
                const items = selectedRefundSale.items.map(it => ({ product_id: it.id, qty: it.qty }));
                fetch('{{ route("restaurant-cash-drawer.process-return") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ invoice_no: reference, items, update_inventory: true })
                }).then(r => r.json()).then(data => {
                    const invMsg = data.inventory_updated ? ' Inventory updated.' : (updateInventory ? ' Inventory was not updated (assign a branch in Users to update stock on returns).' : '');
                    finishRefundSlip(amount, reference, date, reason, invMsg);
                }).catch(() => {
                    finishRefundSlip(amount, reference, date, reason, updateInventory ? ' Inventory was not updated.' : '');
                });
            } else {
                finishRefundSlip(amount, reference, date, reason);
            }
        }
        function finishRefundSlip(amount, reference, date, reason, extraMessage) {
            if (typeof extraMessage === 'undefined') extraMessage = '';
            const slip = document.getElementById('receipt-print');
            slip.innerHTML = `
                <div class="receipt-paper">
                    <div class="receipt-store">${storeName}</div>
                    <hr class="receipt-divider">
                    <div class="receipt-badge" style="color: #b91c1c;">REFUND</div>
                    <div class="receipt-meta"><strong>Date:</strong> ${date}</div>
                    ${reference ? '<div class="receipt-meta"><strong>Invoice:</strong> ' + reference + '</div>' : ''}
                    <div class="receipt-meta"><strong>Method:</strong> ${refundMethod}</div>
                    <div class="receipt-amount" style="color: #b91c1c;">${currencySymbol} ${amount.toFixed(2)}</div>
                    ${reason ? '<div class="receipt-meta" style="margin-top: 8px;">Reason: ' + reason + '</div>' : ''}
                    <hr class="receipt-divider">
                    <div class="receipt-thanks">Thank you</div>
                </div>
            `;
            window.print();
            closeRefundModal();
            alert('Refund of ' + currencySymbol + ' ' + amount.toFixed(2) + ' (' + refundMethod + ') processed.' + extraMessage);
        }

        function searchLoyaltyCustomers(query) {
            if (query.length < 2) {
                document.getElementById('loyaltySearchResults').style.display = 'none';
                return;
            }
            fetch('{{ route("customers.search") }}?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    const results = document.getElementById('loyaltySearchResults');
                    if (data.length > 0) {
                        results.innerHTML = data.map(c => `
                            <div onclick='selectCustomer(${JSON.stringify(c).replace(/'/g, "&#39;")})' style="padding: 10px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                                <div style="font-weight: 600; color: var(--gray-900);">${c.name}</div>
                                <div style="font-size: 12px; color: var(--gray-500);">${c.phone} | Pts: ${parseFloat(c.loyalty_points || 0).toFixed(2)}</div>
                            </div>
                        `).join('');
                        results.style.display = 'block';
                    } else {
                        results.innerHTML = '<div style="padding: 10px; color: var(--gray-400); text-align: center;">No customers found</div>';
                        results.style.display = 'block';
                    }
                }).catch(() => {
                    document.getElementById('loyaltySearchResults').innerHTML = '<div style="padding: 10px; color: var(--gray-400); text-align: center;">No customers found</div>';
                    document.getElementById('loyaltySearchResults').style.display = 'block';
                });
        }

        function selectCustomer(customer) {
            selectedCustomer = customer;
            document.getElementById('customerName').value = customer.name;
            document.getElementById('customerName').readOnly = true;
            document.getElementById('customerPhone').value = customer.phone || '';
            document.getElementById('customerPhone').readOnly = true;
            document.getElementById('displayCustomerPoints').textContent = 'Points: ' + parseFloat(customer.loyalty_points || 0).toFixed(2);
            document.getElementById('selectedCustomerDisplay').style.display = 'block';
            document.getElementById('loyaltyPlaceholderBtn').style.display = 'none';
            closeLoyaltyModal();
        }

        let currentOrderId = null; // Track if we're paying for an existing order

        function openPendingPaymentsModal() {
            document.getElementById('pendingPaymentsModal').style.display = 'flex';
        }

        function closePendingPaymentsModal() {
            document.getElementById('pendingPaymentsModal').style.display = 'none';
        }

        function loadOrderForPayment(orderId) {
            fetch(`/restaurant/orders/${orderId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Order data received:', data);
                    if (data.success && data.order) {
                        const order = data.order;
                        currentOrderId = orderId;
                        
                        // Clear current cart
                        cart = [];
                        
                        // Load order items into cart
                        if (order.items && order.items.length > 0) {
                            order.items.forEach(item => {
                                cart.push({
                                    id: item.product_id,
                                    name: item.product?.name || 'Unknown Product',
                                    price: parseFloat(item.unit_price || 0),
                                    qty: parseFloat(item.qty || 0),
                                    unit: item.product?.unit?.short_code || 'unit',
                                    maxStock: 999999,
                                    discount_type: null,
                                    discount_value: 0
                                });
                            });
                        } else {
                            alert('Order has no items.');
                            return;
                        }
                        
                        // Set table and waiter if available
                        if (order.restaurant_table_id) {
                            document.getElementById('selectedTable').value = order.restaurant_table_id;
                            const tableSelect = document.getElementById('selectedTable');
                            tableSelect.dispatchEvent(new Event('change'));
                        }
                        if (order.user_id) {
                            document.getElementById('selectedWaiter').value = order.user_id;
                            const waiterSelect = document.getElementById('selectedWaiter');
                            waiterSelect.dispatchEvent(new Event('change'));
                        }
                        
                        // Set customer info
                        if (order.customer_name) {
                            document.getElementById('customerName').value = order.customer_name;
                        }
                        if (order.customer_phone) {
                            document.getElementById('customerPhone').value = order.customer_phone;
                        }
                        
                        // Update order number badge
                        document.getElementById('invoiceBadge').textContent = order.order_no;
                        
                        // Render cart
                        renderCart();
                        
                        // Close modal
                        closePendingPaymentsModal();
                        
                        // Scroll to payment section
                        setTimeout(() => {
                            document.querySelector('.right-controls').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    } else {
                        alert('Failed to load order: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Error loading order:', err);
                    alert('Failed to load order: ' + err.message);
                });
        }

        function resetCustomer() {
            selectedCustomer = null;
            document.getElementById('customerName').value = '';
            document.getElementById('customerName').readOnly = false;
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerPhone').readOnly = false;
            document.getElementById('selectedCustomerDisplay').style.display = 'none';
            document.getElementById('loyaltyPlaceholderBtn').style.display = 'flex';
        }

        function sendToKitchen() {
            if (cart.length === 0) return alert('Cart is empty!');
            const invoiceNo = document.getElementById('invoiceBadge').textContent;
            const customerName = selectedCustomer ? selectedCustomer.name : (document.getElementById('customerName').value || 'Walking Customer');
            const customerPhone = selectedCustomer ? selectedCustomer.phone : (document.getElementById('customerPhone').value || '');
            const selectedTableId = document.getElementById('selectedTable').value;
            const selectedWaiterId = document.getElementById('selectedWaiter').value;
            const finalTotal = parseFloat(document.getElementById('summaryTotal').textContent) || 0;

            // Save to database via API (unpaid order - pay later)
            fetch('{{ route("restaurant.orders.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_no: invoiceNo,
                    restaurant_table_id: selectedTableId || null,
                    user_id: selectedWaiterId || null,
                    customer_name: customerName,
                    customer_phone: customerPhone || null,
                    order_type: 'dine_in',
                    guest_count: null,
                    items: cart.map(item => ({
                        product_id: item.id,
                        qty: item.qty,
                        unit_price: item.price,
                        special_instructions: null
                    })),
                    subtotal: finalTotal,
                    tax_total: 0,
                    service_charge: 0,
                    grand_total: finalTotal,
                    payment_method: null,
                    is_paid: false
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order sent to kitchen successfully!');
                    if (confirm('Order sent to kitchen. Clear cart for next order?')) {
                        cart = [];
                        discountValue = 0;
                        document.getElementById('discountInput').value = '0.00';
                        resetCustomer();
                        renderCart();
                        document.getElementById('productSearch').focus();
                        document.getElementById('invoiceBadge').textContent = getNextInvoiceNo();
                    }
                } else {
                    alert('Failed to send order to kitchen: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Failed to save order to database:', err);
                alert('Failed to send order to kitchen. Please try again.');
            });
        }

        function processPayment() {
            if (cart.length === 0) return alert('Cart is empty!');
            const total = cart.reduce((sum, item) => sum + lineTotal(item), 0);
            let cashReceived = 0, changeAmount = 0;
            if (paymentMethod === 'Cash') {
                cashReceived = parseFloat(document.getElementById('receivedAmount').value) || 0;
                const finalTotal = parseFloat(document.getElementById('summaryTotal').textContent) || 0;
                if (finalTotal > 0 && cashReceived < finalTotal) {
                    alert('Insufficient cash! Need ' + currencySymbol + ' ' + (finalTotal - cashReceived).toFixed(2) + ' more.');
                    document.getElementById('receivedAmount').focus();
                    return;
                }
                changeAmount = cashReceived - finalTotal;
            }
            const invoiceNo = document.getElementById('invoiceBadge').textContent;
            const customerName = selectedCustomer ? selectedCustomer.name : (document.getElementById('customerName').value || 'Walking Customer');
            const customerPhone = selectedCustomer ? selectedCustomer.phone : (document.getElementById('customerPhone').value || '');
            const selectedTableId = document.getElementById('selectedTable').value;
            const selectedTableName = selectedTableId ? document.getElementById('selectedTable').selectedOptions[0].dataset.name : null;
            const selectedWaiterId = document.getElementById('selectedWaiter').value;
            const selectedWaiterName = selectedWaiterId ? document.getElementById('selectedWaiter').selectedOptions[0].dataset.name : null;
            const date = new Date().toLocaleString();
            const itemsHtml = cart.map((item, i) => `
                <tr>
                    <td>${i + 1}. ${item.name}</td>
                    <td>${item.qty}</td>
                    <td>${currencySymbol} ${lineTotal(item).toFixed(2)}</td>
                </tr>
            `).join('');
            const finalTotal = parseFloat(document.getElementById('summaryTotal').textContent) || 0;
            const receipt = document.getElementById('receipt-print');
            receipt.innerHTML = `
                <div class="receipt-paper">
                    <div class="receipt-store">${storeName}</div>
                    <div class="receipt-badge">ORDER</div>
                    <div class="receipt-meta"><strong>Order No:</strong> ${invoiceNo}</div>
                    <div class="receipt-meta"><strong>Date:</strong> ${date}</div>
                    ${selectedTableName ? `<div class="receipt-meta"><strong>Table:</strong> ${selectedTableName}</div>` : ''}
                    ${selectedWaiterName ? `<div class="receipt-meta"><strong>Waiter:</strong> ${selectedWaiterName}</div>` : ''}
                    <div class="receipt-meta"><strong>Customer:</strong> ${customerName}</div>
                    <hr class="receipt-line">
                    <table>
                        <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                    <div class="receipt-total-row">
                        <span>Total</span>
                        <span>${currencySymbol} ${finalTotal.toFixed(2)}</span>
                    </div>
                    ${paymentMethod === 'Cash' ? `
                    <div class="receipt-sub-row"><span>Cash received</span><span>${currencySymbol} ${cashReceived.toFixed(2)}</span></div>
                    <div class="receipt-sub-row"><span>Change</span><span>${currencySymbol} ${changeAmount.toFixed(2)}</span></div>
                    ` : ''}
                    <div class="receipt-sub-row"><span>Payment</span><span>${paymentMethod}</span></div>
                    <hr class="receipt-divider">
                    <div class="receipt-thanks">Thank you!</div>
                </div>
            `;
            window.print();
            // Save order data
            const orderData = {
                invoiceNo,
                date,
                customerName,
                customerPhone,
                tableId: selectedTableId || null,
                tableName: selectedTableName || null,
                waiterId: selectedWaiterId || null,
                waiterName: selectedWaiterName || null,
                total: finalTotal,
                paymentMethod,
                items: cart.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    qty: item.qty,
                    discount_type: item.discount_type || null,
                    discount_value: parseFloat(item.discount_value) || 0
                }))
            };
            
            // Save to localStorage for history
            saveCompletedSale(orderData);
            
            // If paying for an existing order, update it instead of creating new
            if (currentOrderId) {
                // Update existing order to mark as paid
                fetch(`/restaurant/orders/${currentOrderId}/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethod
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentOrderId = null; // Reset
                        if (confirm('Payment processed successfully! Clear cart for next customer?')) {
                            cart = [];
                            discountValue = 0;
                            document.getElementById('discountInput').value = '0.00';
                            resetCustomer();
                            renderCart();
                            document.getElementById('productSearch').focus();
                            document.getElementById('invoiceBadge').textContent = getNextInvoiceNo();
                        }
                    } else {
                        alert('Failed to process payment: ' + (data.message || 'Unknown error'));
                    }
                }).catch(err => {
                    console.error('Failed to update order:', err);
                    alert('Failed to process payment. Please try again.');
                    currentOrderId = null;
                });
            } else {
                // Create new paid order
                fetch('{{ route("restaurant.orders.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_no: invoiceNo,
                        restaurant_table_id: selectedTableId || null,
                        user_id: selectedWaiterId || null,
                        customer_name: customerName,
                        customer_phone: customerPhone || null,
                        order_type: 'dine_in',
                        guest_count: null,
                        items: cart.map(item => ({
                            product_id: item.id,
                            qty: item.qty,
                            unit_price: item.price,
                            special_instructions: null
                        })),
                        subtotal: finalTotal,
                        tax_total: 0,
                        service_charge: 0,
                        grand_total: finalTotal,
                        payment_method: paymentMethod,
                        is_paid: true
                    })
                }).catch(err => {
                    console.error('Failed to save order to database:', err);
                });
                if (confirm('Order completed. Clear cart for next customer?')) {
                    cart = [];
                    discountValue = 0;
                    document.getElementById('discountInput').value = '0.00';
                    resetCustomer();
                    currentOrderId = null;
                    renderCart();
                    document.getElementById('productSearch').focus();
                    document.getElementById('invoiceBadge').textContent = getNextInvoiceNo();
                }
            }
        }
    </script>
@endsection
