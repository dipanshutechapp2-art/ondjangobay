<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Newsletter Subscription - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background-color:#f5f7fa; color:#333;">

  <table align="center" width="600" cellpadding="0" cellspacing="0" style="margin:20px auto; background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">
    
    <!-- Header -->
    <tr>
      <td style="background:#111111; padding:20px; text-align:center; color:#fff;">
        <img src="https://marketplace.dipanshutech.co.in/frontend/assets/excs_logo.png" alt="{{ config('app.name') }}" style="max-width:180px; height:auto; display:block; margin:0 auto;">
        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;margin-top: 5px;">Newsletter Subscription</p>
      </td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td style="padding:25px 30px; font-size:15px; line-height:1.6; color:#444;">
        <p style="margin:0 0 10px;">Welcome to Our Newsletter 🎉</strong>,</p>
        <p style="margin:0;">Thank you for subscribing to the {{ config('app.name') }} newsletter! You’ll now receive the latest updates, offers, and exclusive deals straight to your inbox.</p>
      </td>
    </tr>

    <!-- Confirmation Message -->
    <tr>
      <td style="padding:0 30px 25px;">
        <h3 style="margin:0 0 15px; font-size:16px; border-bottom:2px solid #f99e1c; display:inline-block; padding-bottom:3px;">What’s Next?</h3>
        <p style="margin:0; font-size:14px; line-height:1.6; color:#555;">
          Make sure to add <strong>support@ondjangobay.com</strong> to your contacts so you never miss an update. Explore our latest products and offers by visiting our website.
        </p>
      </td>
    </tr>

    <!-- CTA -->
    <tr>
      <td style="padding:0 30px 30px; text-align:center;">
        <a href="{{url('/')}}" style="display:inline-block; background:#f99e1c; color:#fff; text-decoration:none; padding:12px 24px; font-size:14px; border-radius:6px; font-weight:500;">Visit Our Website</a>
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
