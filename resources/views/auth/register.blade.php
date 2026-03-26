@extends('layouts.app_inner')
@section('title', 'Register')
@section('content')

<!-- Add reCAPTCHA script in head -->
<head>
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
</head>

	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <!--<div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Register</h1>
                </div>
            </div>-->
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav  pb-1">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Register</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content contact-us ">
                <div class="container">
               
                    <section class="contact-section mb-10">
                        <div class="row gutter-lg   mb-5 justify-content-center" >
								
							    	<div class="col-lg-6 col-md-5 mb-md-0 mb-4  ">
										@if(session('status')) <div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">{{ session('status') }}</div> @endif
										
										@if($errors->any()) <div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">{{ $errors->first() }}</div> @endif
							    	    <div class="register-form-box">
								<h4 class=" mb-3">REGISTER HERE</h4>
								<form class="form contact-us-form" id="register-form" action="{{ route('register') }}" method="post">
									@CSRF
									<div class="form-group">
										<label for="name">Name</label>
										<x-text-input id="name" class="block mt-1 w-full form-control"
											type="text" name="name" :value="old('name')"
											placeholder="Enter your full name" required autofocus />
										@error('name')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="form-group">
										<label for="email">Email</label>
										<x-text-input id="email" class="block mt-1 w-full form-control"
											type="email" name="email" :value="old('email')"
											placeholder="Email" required autocomplete="username" />
										@error('email')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
									<div class="form-group">
										<label for="email">Phone</label>
										<x-text-input id="phone" class="block mt-1 w-full form-control"
											type="phone" name="phone" :value="old('phone')"
											placeholder="Phone" required autocomplete="phone" />
										@error('phone')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="form-group">
										<label for="password">Password</label>
										<x-text-input id="password" class="block mt-1 w-full form-control"
											type="password" name="password"
											placeholder="Enter your password" required autocomplete="new-password" />
										@error('password')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>

									<div class="form-group">
										<label for="password_confirmation">Confirm Password</label>
										<x-text-input id="password_confirmation" class="block mt-1 w-full form-control"
											type="password" name="password_confirmation"
											placeholder="Re-enter your password" required autocomplete="new-password" />
										@error('password_confirmation')
											<span class="invalid-feedback" role="alert" style="color: red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
									</div>
									
									<!-- reCAPTCHA for Register -->
									<div class="form-group">
										<div id="register-recaptcha"></div>
										<input type="hidden" name="g-recaptcha-response" id="register-recaptcha-response">
										@error('g-recaptcha-response')
											<span class="text-danger">{{ $message }}</span>
										@enderror
									</div>
									
									<div class="flex items-center justify-end">
										<a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('user.login') }}">
											{{ __('Already registered?') }}
										</a>
									</div>
									<br/>

									<button type="submit" class="btn btn-primary btn-rounded text-white" id="register-submit-btn">Submit</button>
								</form>
							 <!--social login -->
							 <div class="social-login">
								<a style="cursor:pointer" class="google-login" onclick="signInWithGoogle()">
									<img src="{{ asset('/frontend/assets/google.png') }}" style="height:20px;" />
									Login with Google
								</a><br/>

								<a style="cursor:pointer" class="facebook-login" onclick="signInWithFacebook()">
									<img src="{{ asset('/frontend/assets/fb.png') }}" style="height:20px;" />
									Login with Facebook
								</a>
								<!--<button id="appleLoginBtn" style="padding:10px 20px;background:black;color:white;border:none;border-radius:6px;cursor:pointer;">
										Sign in with Apple
									  </button>-->
								</div>
							</div>
							</div>
							
							<!--<div class="col-md-4">
							       <div class="register-form-box">
							    
								<h4 class="justify-content-center mb-1">LOGIN</h4>
								<div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Log in easily—just enter your email and password to continue where you left off.') }}
    </div>						
								<form class="form contact-us-form" id="login-form" action="{{ route('user.login') }}" method="post">
									@CSRF
									<div class="form-group">
										<label for="email">Email or Phone</label>
										<x-text-input id="identifier" class="block mt-1 w-full form-control"
											type="text" name="identifier" :value="old('identifier')"
											placeholder="Enter your email or phone number" required autocomplete="username" />
									</div>
									
									<div class="form-group">
										<label for="password">Password</label>
										<x-text-input id="password-login" class="block mt-1 w-full form-control"
											type="password" name="password" :value="old('password')"
											placeholder="Password" required autocomplete="username" />
									</div>
									
									<div class="form-group">
										<div id="login-recaptcha"></div>
										<input type="hidden" name="g-recaptcha-response" id="login-recaptcha-response">
										@error('g-recaptcha-response')
											<span class="text-danger">{{ $message }}</span>
										@enderror
									</div>
									
									<div class="flex items-center justify-end">
										<a href="{{ route('user.password.request') }}">Forgot your password?</a>
										 / <a href="{{ url('register') }}">Register</a>
									</div>
									<br/>
									
									<button type="submit" class="btn btn-primary btn-rounded text-white" id="login-submit-btn">{{ __('Submit') }}</button>
								</form>
							
							 <div class="social-login">
							     	<a style="cursor:pointer" class="google-login" onclick="signInWithGoogle()">
									<img src="{{ asset('/frontend/assets/google.png') }}" style="height:20px;" />
									Login with Google
								</a> 

								<a style="cursor:pointer" class="facebook-login" onclick="signInWithFacebook()">
									<img src="{{ asset('/frontend/assets/fb.png') }}" style="height:20px;" />
									Login with Facebook
								</a>
							 </div>
							</div>
							</div> -->

                        </div>
                    </section>
                    <!-- End of Contact Section -->
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import {
        getAuth,
        signInWithPopup,
        GoogleAuthProvider,
        FacebookAuthProvider
    } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
 
	const firebaseConfig = {
        apiKey: "AIzaSyCcuUxjs4Co2MU9o-7X7pitGDecCpKh7mA",
        authDomain: "exco-e7b3f.firebaseapp.com",
        projectId: "exco-e7b3f"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    window.signInWithGoogle = async () => {
        const provider = new GoogleAuthProvider();
        try {
            const result = await signInWithPopup(auth, provider);
            const token = await result.user.getIdToken();
            sendTokenToLaravel(token);
        } catch (error) {
            alert("Google login failed: " + error.message);
        }
    };

    window.signInWithFacebook = async () => {
		const provider = new FacebookAuthProvider();

		try {
			const result = await signInWithPopup(auth, provider);
			const token = await result.user.getIdToken();
			sendTokenToLaravel(token);

		} catch (error) {
			if (error.code === 'auth/account-exists-with-different-credential') {
				const email = error.customData?.email;

				alert(`An account already exists with ${email} using a different provider (e.g., Google). Please log in using the original method.`);

			} else {
				alert("Facebook login failed: " + error.message);
				console.error(error);
			}
		}
	};

    function sendTokenToLaravel(token) {
		const baseURL = "{{ url('/firebase-login') }}";
		fetch(baseURL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ token })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "{{ url('/my-account') }}";
            } else {
                alert(data.message || "Login failed.");
            }
        });
    }
</script>

<script>
// Global variables to store reCAPTCHA widget IDs
var registerWidgetId, loginWidgetId;

// reCAPTCHA callback function
function onRecaptchaLoad() {
    console.log('reCAPTCHA loaded, rendering widgets...');
    
    // Render reCAPTCHA for register form
    registerWidgetId = grecaptcha.render('register-recaptcha', {
        'sitekey': '{{ config('services.recaptcha.site_key') }}',
        'callback': function(response) {
            console.log('Register reCAPTCHA verified:', response);
            document.getElementById('register-recaptcha-response').value = response;
        },
        'expired-callback': function() {
            console.log('Register reCAPTCHA expired');
            document.getElementById('register-recaptcha-response').value = '';
        }
    });
    
    // Render reCAPTCHA for login form
    loginWidgetId = grecaptcha.render('login-recaptcha', {
        'sitekey': '{{ config('services.recaptcha.site_key') }}',
        'callback': function(response) {
            console.log('Login reCAPTCHA verified:', response);
            document.getElementById('login-recaptcha-response').value = response;
        },
        'expired-callback': function() {
            console.log('Login reCAPTCHA expired');
            document.getElementById('login-recaptcha-response').value = '';
        }
    });
    
    console.log('reCAPTCHA widgets rendered:', {registerWidgetId, loginWidgetId});
}

// Form submission handlers for reCAPTCHA validation
document.getElementById('register-form').addEventListener('submit', function(e) {
    const recaptchaResponse = document.getElementById('register-recaptcha-response').value;
    console.log('Register form submission - reCAPTCHA response:', recaptchaResponse);
    
    if (!recaptchaResponse) {
        e.preventDefault();
        alert('Please complete the reCAPTCHA verification for registration.');
        return false;
    }
    
    const submitBtn = document.getElementById('register-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Processing...';
});

document.getElementById('login-form').addEventListener('submit', function(e) {
    const recaptchaResponse = document.getElementById('login-recaptcha-response').value;
    console.log('Login form submission - reCAPTCHA response:', recaptchaResponse);
    
    if (!recaptchaResponse) {
        e.preventDefault();
        alert('Please complete the reCAPTCHA verification for login.');
        return false;
    }
    
    const submitBtn = document.getElementById('login-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Processing...';
});

// Reset reCAPTCHA when clicking on other form
document.addEventListener('click', function(e) {
    // If user clicks on login form elements and register reCAPTCHA is completed, reset it
    if (e.target.closest('#login-form') && document.getElementById('register-recaptcha-response').value) {
        grecaptcha.reset(registerWidgetId);
        document.getElementById('register-recaptcha-response').value = '';
    }
    
    // If user clicks on register form elements and login reCAPTCHA is completed, reset it
    if (e.target.closest('#register-form') && document.getElementById('login-recaptcha-response').value) {
        grecaptcha.reset(loginWidgetId);
        document.getElementById('login-recaptcha-response').value = '';
    }
});
</script>

<style>
/* Custom styling for reCAPTCHA */
.g-recaptcha {
    margin: 15px 0;
}

.register-form-box {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.social-login {
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 20px;
}

.google-login, .facebook-login {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.google-login:hover {
    background: #f8f9fa;
    border-color: #4285f4;
}

.facebook-login:hover {
    background: #f8f9fa;
    border-color: #1877f2;
}

.alert {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
</style>
@endsection