<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting to Payment...</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0A1A3D 0%, #1A2B4D 100%);
            color: white;
            text-align: center;
        }
        .container {
            padding: 40px;
            max-width: 480px;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255,255,255,0.2);
            border-top-color: #4A9EFF;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 24px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        p {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .manual-submit {
            margin-top: 32px;
        }
        .manual-submit a {
            display: inline-block;
            padding: 12px 28px;
            background: #4A9EFF;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .manual-submit a:hover {
            background: #3B82F6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Redirecting to Payment</h1>
        <p>You're being redirected to our secure payment partner PayHere to complete your <strong>{{ $planName }}</strong> subscription payment.</p>
        <p>Please do not close this page.</p>
        <div class="manual-submit">
            <p style="font-size: 0.85rem; margin-bottom: 12px;">If you are not redirected automatically,</p>
            <button onclick="document.getElementById('payhere-form').submit()" style="padding: 12px 28px; background: #4A9EFF; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.95rem;">Click here to pay</button>
        </div>
    </div>

    <form id="payhere-form" method="POST" action="{{ $checkoutData['action'] }}">
        @foreach($checkoutData as $key => $value)
            @if($key !== 'action')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>

    <script>
        document.getElementById('payhere-form').submit();
    </script>
</body>
</html>