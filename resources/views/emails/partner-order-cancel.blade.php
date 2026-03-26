<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campaign Order Canceled - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background-color:#f5f7fa; color:#333;">

  <table align="center" width="600" cellpadding="0" cellspacing="0" style="margin:20px auto; background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">
    
    <tr>
      <td style="background:#111111; padding:20px; text-align:center; color:#fff;">
        <img src="https://marketplace.dipanshutech.co.in/frontend/assets/excs_logo.png" alt="{{ config('app.name') }}" style="max-width:180px; height:auto; display:block; margin:0 auto;">
        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;margin-top: 5px;">Campaign Order Status Update</p>
      </td>
    </tr>

    <tr>
      <td style="padding:25px 30px; font-size:15px; line-height:1.6; color:#444;">
        <p style="margin:0 0 10px;">Hi <strong>{{ $order->billing_first_name }}</strong>,</p>
        <p style="margin:0;">We’re reaching out to let you know that your order has been <strong style="color:#f99e1c;">canceled</strong>.</p>
      </td>
    </tr>

    <tr>
      <td style="padding:0 30px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:14px;">
          <tr>
            <td style="padding:10px 0; color:#666;"><strong>Order Number:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">#{{ $order->order_number }}</td>
          </tr>
          <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Order Date:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#333;">{{ date('F d, Y', strtotime($order->created_at)) }}</td>
          </tr>
          <tr style="border-top:1px solid #eee;">
            <td style="padding:10px 0; color:#666;"><strong>Status:</strong></td>
            <td style="padding:10px 0; text-align:right; color:#f99e1c; font-weight:bold;">Cancelled</td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style="padding:0 30px 25px;">
        <p style="margin:0; font-size:14px; line-height:1.6; color:#555;">
          We understand that plans can change. If you canceled this order by mistake or need further assistance, our support team is ready to help.
        </p>
      </td>
    </tr>

    <tr>
      <td style="padding:0 30px 30px; text-align:center;">
        <a href="{{url('/')}}" style="display:inline-block; background:#f99e1c; color:#fff; text-decoration:none; padding:12px 24px; font-size:14px; border-radius:6px; font-weight:500;">Track Your Order</a>
      </td>
    </tr>

    <tr>
      <td style="background:#111111; text-align:center; padding:20px; color:#fff; font-size:12px;">
        <p style="margin:0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p style="margin:5px 0 0;">Need help? <a href="mailto:support@ondjangobay.com" style="color:#f99e1c; text-decoration:none;">Contact Support</a></p>
      </td>
    </tr>

  </table>

</body>
</html>
