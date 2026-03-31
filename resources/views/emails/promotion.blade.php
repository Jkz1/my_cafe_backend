<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Special Promotion</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .hero {
            background-color: #10b981; /* Emerald Green */
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .button-wrapper {
            text-align: center;
            padding: 20px 0;
        }
        .button {
            background-color: #10b981;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1 style="margin: 0; font-size: 28px;">Exclusive Monthly Deals</h1>
            <p style="margin: 10px 0 0; opacity: 0.9;">Handpicked offers just for you</p>
        </div>

        <div class="content">
            <p>Hi {{ $user->name ?? 'there' }},</p>
            <p>Check out what we have in store for you this month. We've updated our catalog with new items and special discounts available for a limited time.</p>

            <div class="card">
                <h3 style="margin-top: 0; color: #10b981;">Featured Offer</h3>
                <p>Get <strong>20% OFF</strong> on all services when you use the code below at checkout.</p>
                <div style="background: #f3f4f6; padding: 10px; text-align: center; border-radius: 6px; font-family: monospace; font-size: 18px; font-weight: bold; border: 1px dashed #10b981;">
                    PROMO2026
                </div>
            </div>

            <p>Don't miss out on these updates! Click the button below to explore the full list of promotions.</p>
            
            <div class="button-wrapper">
                <a href="{{ url('/promotions') }}" class="button">View All Deals</a>
            </div>
        </div>

        <div class="footer">
            <p>You are receiving this email because you subscribed to our monthly newsletter.</p>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
            <p><a href="{{ url('/unsubscribe') }}" style="color: #999999;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>