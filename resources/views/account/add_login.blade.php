@extends('layouts.app_inner')
@section('title', 'Link ' . ucfirst($loginMethod->name))
@section('content')

<main class="main">
    <div class="page-header">
        <div class="container">
            <h1 class="page-title mb-0">Link {{ $loginMethod->name }}</h1>
        </div>
    </div>

    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('account.link_accounts') }}">Linked Accounts</a></li>
                <li>Link {{ $loginMethod->name }}</li>
            </ul>
        </div>
    </nav>

    <div class="page-content pt-2">
        <div class="container">
            <div class="tab tab-vertical row gutter-lg">
                @include('account.sidebar')

                <div class="tab-content mb-6">
                    <div class="tab-pane active in">
                        <h3><i class="w-icon-account"></i> Linked Login Methods</h3>
                        
                        <form id="send-otp-form" method="POST" action="{{ route('user.sendOtp') }}">
                            @csrf
                            <input type="hidden" name="login_method" value="{{ $loginMethod->code }}">
                            <div class="mb-3">
                                <label class="form-label">Identifier</label>
                                @if($loginMethod->code=='email')
                                   <input type="email" name="identifier" class="form-control" placeholder="Email" required>
                                @else
                                  <input type="number" name="identifier" class="form-control" placeholder="Phone" required>
                                @endif
                            </div>
                           
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Send OTP</button>
                            <a href="{{ route('account.link_accounts') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                        
                        <form id="verify-otp-form" action="{{ route('user.storeLogin') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="login_method" value="{{ $loginMethod->code }}">
                            <input type="hidden" name="identifier" id="verify-identifier">
                            <input type="hidden" name="password" id="verify-password">
                            
                            <div class="mb-3">
                                <label class="form-label">Enter OTP</label>
                                <input type="text" name="otp" class="form-control" placeholder="Enter the OTP sent" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Link {{ $loginMethod->name }}</button>
                            <a href="{{ route('account.link_accounts') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- Include SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    @endif

    document.getElementById('send-otp-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending OTP...';
        submitButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'OTP Sent!',
                    text: result.message || 'OTP sent successfully!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Switch to verify form
                    document.getElementById('send-otp-form').style.display = 'none';
                    document.getElementById('verify-otp-form').style.display = 'block';
                    document.getElementById('verify-identifier').value = formData.get('identifier');
                    document.getElementById('verify-password').value = formData.get('password');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed!',
                    text: result.message || 'Failed to send OTP.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error occurred while sending OTP.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            console.error(err);
        } finally {
			
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });

    document.getElementById('verify-otp-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitButton = form.querySelector('button[type="submit"]');
        
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verifying...';
        submitButton.disabled = true;

        form.submit();
    });
</script>

@endsection