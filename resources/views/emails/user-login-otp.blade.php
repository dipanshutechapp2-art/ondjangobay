<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login OTP - {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; font-family: 'Segoe UI', Arial, sans-serif; background-color:#f5f7fa; color:#333;">

  <table align="center" width="600" cellpadding="0" cellspacing="0" style="margin:20px auto; background:#ffffff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden;">
    
    <!-- Header -->
    <tr>
      <td style="background:#111111; padding:20px; text-align:center; color:#fff;">
        <img src="https://marketplace.dipanshutech.co.in/frontend/assets/excs_logo.png" alt="{{ config('app.name') }}" style="max-width:180px; height:auto; display:block; margin:0 auto;">
        <p style="margin:10px 0 0; font-size:14px; opacity:0.9;margin-top: 5px;">Login Verification</p>
      </td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td style="padding:25px 30px; font-size:15px; line-height:1.6; color:#444;">
        <p style="margin:0 0 10px;">Hi <strong>{{ $user->name ?? 'User' }}</strong>,</p>
        <p style="margin:0;">Use the following One-Time Password ({{ $user->login_otp}}) to log in to your {{ config('app.name') }} account. This OTP is valid for the next 10 minutes.</p>
      </td>
    </tr>

    <!-- OTP Section -->
    <tr>
      <td style="padding:20px 30px; text-align:center;">
        <p style="margin:0; font-size:22px; font-weight:bold; letter-spacing:2px; color:#f99e1c;">123456</p>
      </td>
    </tr>

    <!-- Note -->
    <tr>
      <td style="padding:0 30px 25px; font-size:14px; line-height:1.6; color:#555;">
        <p style="margin:0;">If you did not request this OTP, please ignore this email. Your account will remain secure.</p>
      </td>
    </tr>

    <!-- CTA -->
    <tr>
      <td style="padding:0 30px 30px; text-align:center;">
        <a href="{{url('/')}}" style="display:inline-block; background:#f99e1c; color:#fff; text-decoration:none; padding:12px 24px; font-size:14px; border-radius:6px; font-weight:500;">Login to Account</a>
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
