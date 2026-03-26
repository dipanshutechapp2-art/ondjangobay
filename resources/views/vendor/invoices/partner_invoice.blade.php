<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $orderInfo->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .container { width: 100%; margin: 0 auto; padding: 20px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #000; margin: 20px 0 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; }
        .table th { background-color: #f5f5f5; }
        .row { display: flex; justify-content: space-between; }
        .col { width: 48%; }
        .mb-2 { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Invoice #{{ $orderInfo->order_number }}</div>

        <div class="row mb-2">
            <div class="col">
                <strong>Order Date:</strong> {{ date('M d, Y', strtotime($orderInfo->created_at)) }}<br>
                <strong>Status:</strong> {{ ucfirst($orderInfo->status) }}<br>
                <strong>Payment Method:</strong> {{ $orderInfo->payment_method }}<br>
            </div>
            <div class="col" style="text-align: left;">
                <strong>Total Amount:</strong> {{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}<br>
            </div>
        </div>

       
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
				<tr>
					<td>{{ $orderInfo->product->name }}
					</td>
					<td>{{ $orderInfo->quantity }}</td>
					<td>{{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}</td>
				</tr>
            </tbody>
        </table>

        <table class="table">
            <tbody>
				<tr>
					<td><strong>Total:</strong></td>
					<td>{{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}</td>
				</tr>
            </tbody>
        </table>

       <br/>
        <div class="row">
            <div class="col">
                <strong>Billing Address</strong><br>
                {{ $orderInfo->billing_first_name }} {{ $orderInfo->billing_last_name }}<br>
                {{ $orderInfo->billing_company }}<br>
                {{ $orderInfo->billing_address_1 }}, {{ $orderInfo->billing_address_2 }}<br>
                {{ $orderInfo->billing_city }}, {{ $orderInfo->billing_state }}<br>
                {{ $orderInfo->billing_zipcode }}, {{ $orderInfo->billing_country }}<br>
                Phone: {{ $orderInfo->billing_phone }}<br>
                Email: {{ $orderInfo->billing_email }}
            </div>
			<br/>
            <div class="col">
                <strong>Shipping Address</strong><br>
                {{ $orderInfo->shipping_first_name }} {{ $orderInfo->shipping_last_name }}<br>
                {{ $orderInfo->shipping_company }}<br>
                {{ $orderInfo->shipping_address_1 }}, {{ $orderInfo->shipping_address_2 }}<br>
                {{ $orderInfo->shipping_city }}, {{ $orderInfo->shipping_state }}<br>
                {{ $orderInfo->shipping_zipcode }}, {{ $orderInfo->shipping_country }}<br>
                Phone: {{ $orderInfo->billing_phone }}<br>
                Email: {{ $orderInfo->billing_email }}
            </div>
        </div>
    </div>
</body>
</html>
