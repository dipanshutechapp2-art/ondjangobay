<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Receipt - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background-color:#f5f7fa; color:#333;">

  <table align="center" width="600" cellpadding="0" cellspacing="0" style="margin:20px auto; background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">
    
    <!-- Header -->
    <tr>
      <td style="background:#111111; padding:20px; text-align:center; color:#fff;">
        <img src="https://marketplace.dipanshutech.co.in/frontend/assets/excs_logo.png" alt="{{ config('app.name') }}" style="max-width:180px; height:auto; display:block; margin:0 auto;">
        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;margin-top: 5px;">Your Order Receipt</p>
      </td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td style="padding:25px 30px; font-size:15px; line-height:1.6; color:#444;">
        <p style="margin:0 0 10px;">Hi <strong>{{ $order->billing_first_name }}</strong>,</p>
        <p style="margin:0;">We’ve received your order and it’s now being processed. Here’s a summary of your purchase:</p>
      </td>
    </tr>

    <!-- Order Details -->
    <tr>
      <td style="padding:0 30px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
          <tr>
            <td style="padding:10px 0; color:#666;"><strong>Order Number:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">#{{ $order->order_number }}</td>
          </tr>
		   <tr>
            <td style="padding:10px 0; color:#666;"><strong>Order Status:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ ucfirst($order->order_status) }}</td>
          </tr>
          <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Order Date:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ $order->created_at->format('M d, Y') }}</td>
          </tr>
		  <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Total:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ $order->currency }}{{ number_format($order->total_amount, 2) }}</td>
          </tr>
          <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Payment Method:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ $order->payment_method }}</td>
          </tr>
		  <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Payment Status:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ $order->payment_status }}</td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Items -->
    <tr>
      <td style="padding:0 30px 25px;">
        <h3 style="margin:0 0 15px; font-size:16px; border-bottom:2px solid #f99e1c; display:inline-block; padding-bottom:3px;">Order Summary</h3>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
          <tr style="background:#f9f9f9;">
            <th align="left" style="padding:12px; border:1px solid #eee;">Product</th>
            <th align="center" style="padding:12px; border:1px solid #eee;">Qty</th>
            <th align="right" style="padding:12px; border:1px solid #eee;">Price</th>
          </tr>
		  @foreach($order->orderProduct as $item)
			  <tr>
				<td style="padding:12px; border:1px solid #eee;">{{ $item->name }}
				  @if (!empty($item->variant_text))
					<div class="text-muted small" style="font-size: 11px;font-weight: bold;">{{$item->variant_text}}</div>
					@endif
					<p class="text-muted small" style="font-size: 11px;"><b style="font-size: 11px;">Vendor:</b> {{$item->product->userInfo->name ?? "N/A"}} <br/><b style="font-size: 11px;">Orgin:</b> {{$item->product->type ?? "unknown"}}</p>
				</td>
				<td align="center" style="padding:12px; border:1px solid #eee;">{{ $item->quantity }}</td>
				<td align="right" style="padding:12px; border:1px solid #eee;">{{ $item->currency }}{{ number_format($item->total, 2) }}</td>
			  </tr>
          @endforeach
		  @foreach($order->orderTotal as $total)
			  <tr>
				<td colspan="2" align="right" style="padding:12px; border:1px solid #eee;"><strong>{{ $total->meta_key }}:</strong></td>
				<td align="right" style="padding:12px; border:1px solid #eee;">{{ $total->currency }}{{ number_format($total->meta_value, 2) }}</td>
			  </tr>
           @endforeach
        </table>
      </td>
    </tr>

    <!-- Shipping Address -->
    <tr>
      <td style="padding:0 30px 25px;">
        <h3 style="margin:0 0 10px; font-size:16px; border-bottom:2px solid #f99e1c; display:inline-block; padding-bottom:3px;">Billing Address</h3>
        <p style="margin:0; font-size:14px; line-height:1.6; color:#555;">
            {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
            {{ $order->billing_company }}<br>
            {{ $order->billing_address_1 }} {{ $order->billing_address_2 }}<br>
            {{ $order->billing_city }}, {{ $order->billing_state }}<br>
            {{ $order->billing_country }} - {{ $order->billing_zipcode }}<br>
            Phone: {{ $order->phone }}<br>
            Email: {{ $order->email }}
        </p>
      </td>
    </tr>
	
	<tr>
      <td style="padding:0 30px 25px;">
        <h3 style="margin:0 0 10px; font-size:16px; border-bottom:2px solid #f99e1c; display:inline-block; padding-bottom:3px;">Shipping Address</h3>
        <p style="margin:0; font-size:14px; line-height:1.6; color:#555;">
            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
            {{ $order->shipping_company }}<br>
            {{ $order->shipping_address_1 }} {{ $order->shipping_address_2 }}<br>
            {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
            {{ $order->shipping_country }} - {{ $order->shipping_zipcode }}<br>
			Phone: {{ $order->phone }}<br>
            Email: {{ $order->email }}
        </p>
      </td>
    </tr>
	
    <!-- CTA -->
    <tr>
      <td style="padding:0 30px 30px; text-align:center;">
        <a href="{{url('/my-account')}}" style="display:inline-block; background:#f99e1c; color:#fff; text-decoration:none; padding:12px 24px; font-size:14px; border-radius:6px; font-weight:500;">Track Your Order</a>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background:#111111; text-align:center; padding:20px; color:#fff; font-size:12px;">
        <p style="margin:0;">&copy; {{date('Y')}} {{ config('app.name') }}. All rights reserved.</p>
        <p style="margin:5px 0 0;">Need help? <a href="mailto:support@ondjangobay.com" style="color:#f99e1c; text-decoration:none;">Contact Support</a></p>
      </td>
    </tr>

  </table>

</body>
</html>
