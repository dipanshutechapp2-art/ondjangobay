@extends('layouts.app_inner')
@section('title', 'Generate Magic Link')
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
                        <li>Generate Magic Link</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content contact-us ">
                <div class="container">
                    <section class="contact-section mb-10">
                        <div class="row gutter-lg   mb-5 justify-content-center" >
							<div class="col-lg-5  ">
							    <div class="register-form-box">
								<h4 class="justify-content-center title mb-3">GENERATE MAGIC LINK FOR LOGIN</h4>
								<div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
									{{ __('Magic link login made easy! Enter your email and we’ll send a link that lets you access your account safely—no password needed.') }}
								</div>					
								@if(session('success'))
									<div class="alert alert-success alert-simple alert-inline show-code-action">{{ session('success') }}</div>
								@endif
								@if(session('error'))
									<div class="alert alert-error alert-simple alert-inline show-code-action">
										{{ session('error') }}
									</div>
								@endif
								<form id="loginFormMagic" method="POST" action="{{ route('magic.link.send') }}">
								 @csrf
								  <div class="form-group">
									<x-input-label for="email" :value="__('Email')" />
									<x-text-input id="emailMagic" class="form-control @error('email') is-invalid @enderror"  type="email" name="email" :value="old('email')" placeholder="Email" required autofocus autocomplete="username" />
									@error('email')
										<span class="invalid-feedback d-block" role="alert">
											<strong style="color: red;">{{ $message }}</strong>
										</span>
									@enderror
								  </div>
								   <div class="login-btn">
										<button type="submit" class="btn btn-primary">Submit</button>
								   </div>
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
