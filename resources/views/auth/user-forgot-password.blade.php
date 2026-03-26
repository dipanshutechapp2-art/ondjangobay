@extends('layouts.app_inner')
@section('title', 'Forgot password')
@section('content')


<style>
   .register-form-box
 {
    
box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    border-radius:20px;
    padding: 30px;
}

.btn-registration{
    border-color: #F99E1C;
    background-color: #F99E1C;
}

.btn-registration:hover{
    border-color: #f6624e;
    background-color: #f6624e;
}
</style>

	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <!--<div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Forgot password</h1>
                </div>
            </div>-->
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav  pb-1">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Forgot password</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content contact-us ">
                <div class="container">
               
                    <section class="contact-section mb-10">
                        <div class="row gutter-lg   mb-5 justify-content-center" >
							<div class="col-lg-5">
							    	<div class="register-form-box">
								<h4 class="mb-2">FORGOT PASSWORD</h4>
								<div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>						<!-- Session Status -->
									<!-- Session Status -->								
								@if(session('status')) <div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">{{ session('status') }}</div> @endif
								
								@if($errors->any()) <div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">{{ $errors->first() }}</div> @endif
								
								<form class="form contact-us-form" action="{{ route('user.password.email') }}" method="post">
									@CSRF
									<div class="form-group">
										<label for="email">Email or Phone</label>
										<x-text-input id="email" class="block mt-1 w-full form-control"
											type="text" name="identifier" :value="old('identifier')"
											placeholder="Enter your email or phone number" required autocomplete="username" />
									</div>

									<div class="flex items-center justify-end">
										<a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('user.login') }}">
											{{ __('Already registered?') }}
										</a>
									</div>
									<br/>

									<button type="submit" class="btn btn-primary btn-rounded text-white">{{ __('Submit') }}</button>
								</form>
									</div>
							</div>
                        </div>
                    </section>
                    <!-- End of Contact Section -->
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
@endsection
