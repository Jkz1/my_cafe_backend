<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Order Confirmation</h2>
        </div>
        <div class="content">
            <p>Dear {{ $order->user->name }},</p>
            <p>Thank you for your order! We have successfully received it and are currently processing it.</p>
            <p><strong>Order Number:</strong> #{{ $order->id }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($order->total_price, 2) }}</p>
            <p>We have attached your official invoice to this email for your records.</p>
            <br>
            <p>If you have any questions, feel free to reply to this email.</p>
            <p>Best regards,<br>The My-Caffe Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} My-Caffe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
