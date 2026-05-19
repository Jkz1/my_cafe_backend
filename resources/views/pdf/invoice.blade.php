<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .company-details {
            text-align: right;
            font-size: 13px;
            color: #555;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .billing-info {
            margin-bottom: 40px;
        }
        .billing-info table {
            width: 100%;
        }
        .billing-info td {
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th {
            background-color: #f8f8f8;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .totals-table {
            width: 50%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .totals-table tr:last-child td {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="invoice-title">INVOICE</div>
                    <div>Invoice #: {{ $order->id }}</div>
                    <div>Date: {{ $order->created_at->format('M d, Y') }}</div>
                </td>
                <td class="company-details">
                    <strong>My-Caffe</strong><br>
                    123 Coffee Street<br>
                    Brew City, BC 12345<br>
                    hello@my-caffe.com
                </td>
            </tr>
        </table>
    </div>

    <div class="billing-info">
        <table>
            <tr>
                <td>
                    <strong>Billed To:</strong><br>
                    {{ $order->user->name }}<br>
                    {{ $order->user->email }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td class="text-right">${{ number_format($detail->unit_price, 2) }}</td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-right">${{ number_format($detail->unit_price * $detail->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">${{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td>Discount:</td>
            <td class="text-right">-${{ number_format($order->discount_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>Grand Total:</td>
            <td class="text-right">${{ number_format($order->total_price, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Thank you for your business!
    </div>

</body>
</html>
