<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to OndJangoBay - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background-color:#f5f7fa; color:#333;">

  <table align="center" width="600" cellpadding="0" cellspacing="0" style="margin:20px auto; background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">
    
    <!-- Header -->
    <tr>
      <td style="background:#111111; padding:20px; text-align:center; color:#fff;">
        <img src="https://marketplace.dipanshutech.co.in/frontend/assets/excs_logo.png" alt="{{ config('app.name') }}" style="max-width:180px; height:auto; display:block; margin:0 auto;">
        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;margin-top: 5px;">Welcome {{ config('app.name') }}!</p>
      </td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td style="padding:25px 30px; font-size:15px; line-height:1.6; color:#444;">
        <p style="margin:0 0 10px;">Hi <strong>{{ $user->name }}</strong>,</p>
        <p style="margin:0;">Thank you for signing up with {{ config('app.name') }}! We’re excited to have you on board. You now have access to our latest products, offers, and updates.</p>
      </td>
    </tr>

    <!-- Next Steps -->
    <tr>
      <td style="padding:0 30px 25px;">
        <h3 style="margin:0 0 15px; font-size:16px; border-bottom:2px solid #f99e1c; display:inline-block; padding-bottom:3px;">Getting Started</h3>
        <p style="margin:0; font-size:14px; line-height:1.6; color:#555;">
          Click the button below to complete your profile, explore our products, and start shopping. Make sure to verify your email to stay updated with all exclusive offers!
        </p>
      </td>
    </tr>

    <!-- CTA -->
    <tr>
      <td style="padding:0 30px 30px; text-align:center;">
        <a href="{{url('/')}}" style="display:inline-block; background:#f99e1c; color:#fff; text-decoration:none; padding:12px 24px; font-size:14px; border-radius:6px; font-weight:500;">Complete Your Profile</a>
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
