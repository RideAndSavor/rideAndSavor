<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .invoice-box {
            width: 80%;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-top: 10px solid #d4af37;
        }
        /* .left-item {
            text-align: left;
        }

        .right-item {
            text-align: right;
        } */


        /* .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 15px;
            margin-bottom: 20px;
        } */



        .company-details {
            text-align: right;
            font-size: 14px;
            color: #555;
        }

        .shop-logo {
            width: 100px;
            height: auto;
            text-align: left;
        }

        .shop-logo img {
            width: 100%;
            max-height: 100px;
            object-fit: contain;
        }

        h2 {
            color: #d4af37;
            margin-bottom: 5px;
        }

        .invoice-details {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        .invoice-details th {
            background-color: #d4af37;
            color: white;
            text-align: left;
            padding: 10px;
        }

        .invoice-details td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .summary {
            margin-top: 30px;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 8px;
            font-size: 16px;
            text-align: right;
        }

        .summary tr:last-child td {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #d4af37;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

    </style>
</head>
<body>
    <div class="invoice-box">
        <table style="width: 100%; border-bottom: 2px solid #d4af37; padding-bottom: 15px; margin-bottom: 20px;">
            <tr>
                <!-- Left side: Invoice info -->
                <td style="vertical-align: top; width: 50%;">
                    <h2>INVOICE</h2>
                    <p><strong>Invoice #:</strong> {{ $order->id }}</p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
                </td>

                <!-- Right side: Shop Logo + Details -->
                <td style="text-align: right; vertical-align: top; width: 50%;">
                    @if($order->shop->images->isNotEmpty())
                    <img
                    src="{{ public_path('storage/' . $order->shop->images->first()->upload_url) }}"
                    alt="Shop Logo"
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;"><br>
                    @else
                        <p style="color: #777;">No Logo Available</p>
                    @endif

                    <strong>{{ $order->shop->name }}</strong><br>
                    {{ $order->shop->email }}<br>
                    {{ $order->shop->address->block_no . ', ' . $order->shop->address->floor . ', ' . $order->shop->address->street->name . ', Ward: ' . $order->shop->address->street->ward->name ?? 'No address available' }}<br>
                    {{ $order->shop->address->street->ward->township->name . ', ' . $order->shop->address->street->ward->township->city->name . ', ' ?? 'No address available' }}
                </td>
            </tr>
        </table>


        <div>
            <strong>Bill To:</strong><br>
            {{ $order->user->name }}<br>
            {{ $order->user->email }}
        </div>

        <table class="invoice-details">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $detail)
                    <tr>
                        <td>{{ $detail->product->name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>${{ number_format($detail->unique_price, 2) }}</td>
                        <td>${{ number_format($detail->discount_percent, 2) }}</td>
                        <td>${{ number_format($detail->final_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td width="80%"><strong>Subtotal:</strong></td>
                    <td>${{ number_format($order->total_price, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Discount:</strong></td>
                    <td>${{ number_format($order->discount_percent, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong>${{ number_format($order->final_price, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Thank you for your shopping! <br>
            This is a system-generated invoice and does not require a signature.
        </div>
    </div>
</body>
</html>
