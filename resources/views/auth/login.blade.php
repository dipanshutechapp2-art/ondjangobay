<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
a.google-login {
display: flex;
  align-items: center;
  justify-content: center; 
  gap: 8px; 
  padding: 10px 16px;
  text-decoration: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #fff;
  color: #333;
  font-weight: 500;
  font-size: 14px;
}

a.facebook-login {
display: flex;
  align-items: center;
  justify-content: center; 
  gap: 8px; 
  padding: 10px 16px;
  text-decoration: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #fff;
  color: #333;
  font-weight: 500;
  font-size: 14px;
}
</style>

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



<x-guest-layout>
    <!-- Session Status -->
    
	@if(session('success'))
		<div class="alert alert-success alert-simple alert-inline show-code-action" style="color:green;font-weight:bold;">{{ session('success') }}</div> <br/>
	@endif
	@if(session('error'))
		<div class="alert alert-error alert-simple alert-inline show-code-action" style="color:red;font-weight:bold;">
			{{ session('error') }}
		</div><br/>
	@endif
	
	<x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
			&nbsp;&nbsp;&nbsp;
			<!--<a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ url('register') }}">
                    Register
                </a>-->

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
	
<!--<a href="{{ url('auth/google') }}" class="google-login" onclick="signInWithGoogle()"><img src="{{asset('/frontend/assets/google.png')}}" style="height:20px;"/>Login with Google</a>&nbsp;&nbsp;
<a href="{{ url('auth/facebook') }}" class="facebook-login" onclick="signInWithFacebook()"><img src="{{asset('/frontend/assets/fb.png')}}" style="height:20px;"/>Login with Facebook</a>-->

<!--<a style="cursor:pointer" class="google-login" onclick="signInWithGoogle()">
    <img src="{{ asset('/frontend/assets/google.png') }}" style="height:20px;" />
    Login with Google
</a><br/>

<a style="cursor:pointer" class="facebook-login" onclick="signInWithFacebook()">
    <img src="{{ asset('/frontend/assets/fb.png') }}" style="height:20px;" />
    Login with Facebook
</a>-->

</x-guest-layout>
