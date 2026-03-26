<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php 
    use App\Models\ThemeSetting;
    $setting = ThemeSetting::first();
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <meta name="keywords" content="Excs-ecommerce">
    <meta name="description" content="Excs is a big ecommerce.">
    <meta name="author" content="Excs">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('uploads/setting') }}/{{ $setting->favicon }}">

    <!-- WebFont.js -->
    <script>
        WebFontConfig = {
            google: { families: ['Poppins:400,500,600,700,800','Seoge Script:400,500,600,700,800']}
        };
        (function (d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = 'assets/js/webfont.js';
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>

     <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-regular-400-1.woff2')}}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-solid-900-1.woff2')}}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/vendor/fontawesome-free/webfonts/fa-brands-400-1.woff2')}}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('frontend/assets/fonts/excs-1.ttf?png09e')}}" as="font" type="font/ttf" crossorigin="anonymous">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min-1.css')}}">

    <!-- Plugins CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min-1.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/animate/animate.min-1.css')}}">
    <!--<link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/vendor/magnific-popup/magnific-popup.min-1.css')}}">-->

    <!-- Default CSS -->
   <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style.css') }}?v={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/custom.css') }}?v={{ time() }}">

</head>

<body class="home-test">
    <div class="page-wrapper">
        <h1 class="d-none">Escx</h1>
        <!-- Start of Header -->
        <header class="header">
		{{-- @include('layouts.top_header') --}}
            <!-- End of Header Top -->

            <div class="header-middle">
                <div class="container">
                    <div class="header-left mr-md-4">
                        <a href="{{url('/')}}" class="mobile-menu-toggle  w-icon-hamburger">
                        </a>
                        <a href="{{url('/')}}" class="logo ml-lg-0">
                            <img src="{{ asset('uploads/setting') }}/{{ $setting->header_logo }}" alt="logo" width="230">
                        </a>
                        @include('layouts.top_menu')
                    </div>
                    
                    
                    <div class="header-right ml-2 header-top-right-area">
                        	<!--download app -->
                        <div class="download-apps-main">
							<div class="download-apps da-toggle-btn">
								<div class="download-app-icon"><i class="w-icon-ios"></i></div>
								<div class="download-app-text">
									<span>
										<span class="download-app-">Download The</span> Apps
									</span>
								</div>
							</div>
							<div class="download-app-wraps togglable-div">
								<div class="download-app-buttons">
									<div class="download-app-qr"><img src="{{asset('/frontend/assets/customer_qr_code_app.png')}}" /></div>
									<div class="download-app-details">
										<h4 class="download-app-title">Download the Ondjangobay app</h4>
										<p class="download-app-subtitle">Scan the QR code to download</p>
										
										<div class="download-apps-btn-design">
											<a href="#" class="download-btn-style"><img src="{{asset('/frontend/assets/app-store.png')}}" /></a>
											<a target="_blank" href="https://play.google.com/store/apps/details?id=com.ondjangobay.customer" class="download-btn-style"><img src="{{asset('/frontend/assets/playstore.png')}}"/></a>
										</div>
									</div>
								</div>
							</div>
                        </div>
                        
                      
                        
                        <!--login with dropdown -->
						<div class="bay-user-menu">
							<div class="login-icon-wrapper hover-it">
								<div class="login-icon-box">
								   
									@if(!empty(Auth::user()->image))
										<img id="preview" src="{{asset('/uploads/users/')}}/{{Auth::user()->image}}" alt="Image Preview"  class="img-fluid" style="border-radius:100%;height:30px;"/>
									@else
										 <i class="w-icon-account"></i>
									@endif
									
								</div>
								<div class="login-welcome">
									<p class="welcome-login">Welcome</p>
									@if(Auth::check())
									  <p class="login-or-register">{{ Auth::user()->name ?? "No user" }}</p>
								    @else
									  <p class="login-or-register"> Sign in / Register</p>
								    @endif
								</div>
							</div>
							
							<div class="bay-dropdown toggle-div">
								  <!--after login -->
								    <div class="after-login-div">
									    <div class="after-login-avtr">
										    @if(!empty(Auth::user()->image))
												<img id="preview" src="{{asset('/uploads/users/')}}/{{Auth::user()->image}}" alt="Image Preview"  class="img-fluid" />
											@else
												<img src="{{asset('/frontend/assets/no-image.webp')}}" class="img-fluid" />
											@endif
										</div>
									    <div class="after-login-details"><span>{{ Auth::user()->name ?? "No user" }}</span></div>
								    </div><hr/>
								  <!--after login -->
								 @if(!Auth::check()) 
									<div class="login-register-btn">
										<a href="{{url('/user/login')}}"><button class="marketplace-btn marketplace-login-btn" id="loginBtn-test">Sign In</button></a>
										<a href="{{url('/magic-link')}}"><button class="marketplace-btn marketplace-login-btn" id="sendMagicLinkModel-test">Sign in via magic link</button></a>
										<!--<button class="marketplace-btn marketplace-register-btn" id="registerBtn">Register</button>-->
										<a href="{{url('/register')}}"><button class="marketplace-btn marketplace-login-btn" id="registerBtn-test">Register</button></a>
										<a href="{{url('/register-as-vendor')}}"><button class="marketplace-btn marketplace-login-btn" id="registerBtn-test">Register as vendor</button></a>
										{{-- <a href="{{url('/compare')}}"><button class="marketplace-btn marketplace-login-btn" id="registerBtn-test">Compare Products</button></a> --}}
									</div>
									
								@endif
								 
								@if(Auth::check())
									<div class="login-dropdown-card">
										<ul>
										    @if(!empty(Auth::user()->role) && Auth::user()->role=='admin')
												<li><a href="{{url('/admin/dashboard')}}"><i class="w-icon-account"></i><span>My Account</span></a></li><hr/>
											@elseif(!empty(Auth::user()->role) && Auth::user()->role=='vendor')
												<li><a href="{{url('/vendor/dashboard')}}"><i class="w-icon-account"></i><span>My Account</span></a></li><hr/>
											@else
											   <li><a href="{{url('/my-account')}}"><i class="w-icon-account"></i><span>My Account</span></a></li><hr/>
											   <li><a href="{{url('/account/orders')}}"><i class="w-icon-orders"></i><span>My Order</span></a></li><hr/>
												<li><a href="{{url('/wallet')}}"><i class="w-icon-wallet"></i><span>My Wallet</span></a></li><hr/>
												<li><a href="{{url('/account/wishlist')}}"><i class="w-icon-gift"></i><span>Wishlist</span></a></li><hr/>
												{{-- <li><a href="{{url('/compare')}}"><i class="w-icon-compare"></i><span>Compare</span></a></li><hr/> --}}
											@endif
											<!--<li><a href="{{url('/account/orders')}}"><i class="w-icon-dashboard"></i><span>Settings</span></a></li><hr/>-->
											<li><a style="cursor:pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="w-icon-logout"></i><span>Logout</span></a>
											<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
												@csrf
											</form>
											</li>
										</ul>
									</div>
								@endif
							</div>
						</div>
                        <!--login with dropdown -->
                        
                        
                        <!-- login register popup  -->
						 <!-- Login Modal -->
						  <div id="loginModal" class="modalsMarketPlaceSearch">
							 <div class="modalsMarketPlaceSearch-content">
								<span class="search_closesMarketPlace"><i class="w-icon-times-solid"></i></span>
								<div class="modalsMarketPlaceSearch-body">
								  <div class="login-wrap">
										<div id="loginLoader" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
											<img src="{{ asset('/frontend/assets/loader.gif') }}" alt="Loading..." style="width:50px; height:50px;">
										</div>
										<h4 class="popup-heading">Register/Sign in</h4>
										<hr/>
										<form id="loginForm" method="POST" action="{{ route('login') }}">
										 @csrf
										  <div class="form-group">
											<x-input-label for="email" :value="__('Email')" />
											<x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
											<div class="error" id="email_error" style="color:red;"></div>
										  </div>

										  <div class="form-group">
											<x-input-label for="password" :value="__('Password')" />
											<x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
											<div class="error" id="password_error" style="color:red;"></div>
										  </div>

										  <div class="form-check remember-me">
											<label for="remember_me" class="inline-flex items-center">
												<input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
												<span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
											</label>
										  </div>

									   <div class="login-btn">
											  <button type="submit" class="btn btn-primary">Log In</button>
									   </div>
										  
										   <div class="form-links">
											<a href="{{ route('user.password.request') }}">Forgot your password?</a>
											<a href="{{ url('register') }}">Register</a>
										  </div>

										 <div class="social-icons social-icon-border-color d-flex justify-content-center">
											<a href="#" class="facebook-login social-icon social-google fab fa-google"onclick="signInWithGoogle()"></a>
											
											<a href="#" class="google-login social-icon social-facebook w-icon-facebook" onclick="signInWithFacebook()"></a>
											</div>
											<hr/>
										</form>
								  </div>
								</div>
							 </div>
						  </div>
						<!-- Login By Magic Link Modal start -->
						  <div id="loginModalMagicLink" class="modalsMarketPlaceSearch">
							 <div class="modalsMarketPlaceSearch-content">
								<span class="search_closesMarketPlace"><i class="w-icon-times-solid"></i></span>
								<div class="modalsMarketPlaceSearch-body">
								  <div class="login-wrap">
										<div id="loginLoaderMagic" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
											<img src="{{ asset('/frontend/assets/loader.gif') }}" alt="Loading..." style="width:50px; height:50px;">
										</div>
										<h4 class="popup-heading">Generate magic link for login</h4>
										<hr/>
										<div class="error" id="email_success_magic" style="color:green;"></div>
										<form id="loginFormMagic" method="POST" action="{{ route('magic.link.send') }}">
										 @csrf
										  <div class="form-group">
											<x-input-label for="email" :value="__('Email')" />
											<x-text-input id="emailMagic" class="form-control @error('email') is-invalid @enderror"  type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
											<div class="error" id="email_error_magic" style="color:red;"></div>
										  </div>
										   <div class="login-btn">
												<button type="submit" class="btn btn-primary">Submit</button>
										   </div>
										</form>
								  </div>
								</div>
							 </div>
						  </div>
						  <!-- Login By Magic Link Modal end -->
						 
						<!-- Register Modal -->
						  <div id="registerModal" class="modalsMarketPlaceSearch">
							 <div class="modalsMarketPlaceSearch-content">
								<span class="search_closesMarketPlace"><i class="w-icon-times-solid"></i></span>
								<div class="modalsMarketPlaceSearch-body">
									<div class="login-wrap">
										<div id="loginLoaderRegister" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:9999;">
											<img src="{{ asset('/frontend/assets/loader.gif') }}" alt="Loading..." style="width:50px; height:50px;">
										</div>
										<h4 class="popup-heading">Register</h4><hr/>
									   <form id="registerForm" class="form contact-us-form" action="{{ route('register') }}" method="post">
									     @CSRF
										<!-- Full Name -->
										<div class="form-group">
										  <label for="fullname">Name</label>
										  <x-text-input id="name" class="block mt-1 w-full form-control"
												type="text" name="name" :value="old('name')"
												placeholder="Enter your full name" required autofocus />
											<span class="error" id="nameError" style="color: red;"></span>
										</div>

										<!-- Email -->
										<div class="form-group">
										  <label for="reg-email">Your Email</label>
											<x-text-input id="email" class="block mt-1 w-full form-control"
												type="email" name="email" :value="old('email')"
												placeholder="Enter your email address" required autocomplete="username" />
											    <span class="error" id="emailError" style="color: red;"></span>
										</div>

										<!-- Password -->
										<div class="form-group">
										  <label for="reg-password">Password</label>
										    <x-text-input id="password" class="block mt-1 w-full form-control"
												type="password" name="password"
												placeholder="Enter your password" required autocomplete="new-password" />
											<span class="error" id="passwordError" style="color: red;"></span>
										</div>

										<!-- Confirm Password -->
										<div class="form-group">
										    <label for="confirm-password">Confirm Password</label>
											<x-text-input id="password_confirmation" class="block mt-1 w-full form-control"
												type="password" name="password_confirmation"
												placeholder="Re-enter your password" required autocomplete="new-password" />
											<span class="error" id="password_confirmationError" style="color: red;"></span>
										</div>

										<!-- Register Button -->
										<div class="login-btn">
										  <button type="submit" class="btn btn-primary">Register</button>
										</div>

										<!-- Already Registered -->
										<div class="form-links">
										  <a href="{{ route('login') }}">Already registered? Log In</a>
										</div>

										<!-- Social Register -->
										<div class="social-icons social-icon-border-color d-flex justify-content-center">
										   <a href="#" class="facebook-login social-icon social-google fab fa-google"onclick="signInWithGoogle()"></a>
											
											<a href="#" class="google-login social-icon social-facebook w-icon-facebook" onclick="signInWithFacebook()"></a>
										</div>
										<hr/>
									  </form>
									</div>
								</div>
							 </div>
						  </div>
						 <!-- login register popup end -->
						 
						 
                        <!--<span class="divider mr-6 d-xl-show"></span>-->
						<div class="header-call   d-lg-flex align-items-center">
							<div class="header-right">
								<div class="dropdown currency-dropdown">
									<a href="#currency">{{ session('currency_code') ?? getDefaultSelectedCurrencyCode() }}</a>
									<div class="dropdown-box">
										@foreach(App\Models\Currency::orderBy('display_order','ASC')->get() as $currency)
											<a href="{{ route('currency.switch', $currency->code) }}">{{ $currency->code }}</a>
										@endforeach
									</div>
								</div>
								<div class="dropdown language-dropdown notranslate">
									<a href="#language" id="selected-language" class="notranslate">
										<img id="selected-language-flag" src="{{ asset('frontend/assets/images/flags/eng-1.png') }}" width="14" height="8" class="dropdown-image"> ENG
									</a>
									<div class="dropdown-box notranslate">
										<a href="javascript:void(0);" class="notranslate"
										   onclick="translateLanguage('pt')"
										   data-lang="pt"
										   data-flag="{{ asset('frontend/assets/images/flags/por-1.png') }}"
										   data-label="POR"
											>
											<img src="{{ asset('frontend/assets/images/flags/por-1.png') }}" width="14" height="8" class="dropdown-image"> POR
										</a>
										<a href="javascript:void(0);" class="notranslate"
										   onclick="translateLanguage('en')"
										   data-lang="en"
										   data-flag="{{ asset('frontend/assets/images/flags/eng-1.png') }}"
										   data-label="ENG"
										>
											<img src="{{ asset('frontend/assets/images/flags/eng-1.png') }}" width="14" height="8" class="dropdown-image"> ENG
										</a>
										<a href="javascript:void(0);" class="notranslate"
										   onclick="translateLanguage('fr')"
										   data-lang="fr"
										   data-flag="{{ asset('frontend/assets/images/flags/fra-1.png') }}"
										   data-label="FRA"
										>
											<img src="{{ asset('frontend/assets/images/flags/fra-1.png') }}" width="14" height="8" class="dropdown-image"> FRA
										</a>
										
										<a href="javascript:void(0);" class="notranslate"
										   onclick="translateLanguage('zh-CN')"
										   data-lang="zh-CN"
										   data-flag="{{ asset('frontend/assets/images/flags/chn-1.png') }}"
										   data-label="ZH">
											   <img src="{{ asset('frontend/assets/images/flags/chn-1.png') }}" width="14" height="8">&nbsp; ZH
										</a>
									</div>
								</div>
								<div id="google_translate_element" style="display:none;"></div>
							</div>
						</div>
                        <!--<a class="wishlist label-down link d-xs-show" href="{{url('/account/wishlist')}}">
                            <i class="w-icon-heart"></i>
                            <span class="wishlist-label d-lg-show">Wishlist</span>
                        </a>
                        <a class="compare label-down link d-xs-show" href="#">
                            <i class="w-icon-compare"></i>
                            <span class="compare-label d-lg-show">Compare</span>
                        </a>-->
                        @include('layouts.shoping_cart')
                    </div>
                    
                    
                    
                    
                    
                    
                </div>
            </div>
            <!-- End of Header Middle -->


            <div class="header-bottom sticky-content fix-top sticky-header has-dropdown">
                <div class="container">
                    <div class="inner-wrap">
                        <div class="header-left flex-1">
                            <div class="dropdown category-dropdown show-dropdown listed-menu-parent" data-visible="true">
                                <a href="{{url('/')}}" class="category-toggle text-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-display="static" title="All Departments">
                                    <i class="w-icon-category"></i>
                                    <span>All Departments</span>
                                </a>
                                <div class="dropdown-box listed-menu">
                                    <ul class="menu vertical-menu category-menu">
										@php 
											use App\Models\Category;
											$categories = Category::with('children')->where('status', 1)->whereNull('parent_id')->get();
										@endphp

										@foreach($categories as $category)
											<li>
												<a href="{{ url('/shop') }}/?category={{$category->id}}">
											   <span class="category-icon" data-desktop="{{ asset('uploads/categories/' . $category->desktop_image) }}"
											  data-mobile="{{ asset('uploads/categories/' . $category->mobile_image) }}">
											@if($category->desktop_image || $category->mobile_image)
												<img src="" alt="{{ $category->name }}"
													 style="width: 20px; height: 20px; object-fit: contain; display: inline-block; margin-right: 5px;">
											@else
												<i class="w-icon-tshirt"></i>
											@endif
										</span>
										{{ $category->name }}

												</a>

												@if($category->children && $category->children->count())
													<ul class="megamenu megamenu-ul">
														@foreach($category->children as $child)
															<li class="megamenu-li">
															   
																
																@if($child->desktop_image)
																	<a href="{{ url('/shop') }}/?category={{$child->id}}">
																		 <img alt="{{ $child->name }}"
																			 src="{{ asset('/uploads/categories/') }}/{{ $child->desktop_image ?? '' }}"
																			 >
																		 
																	</a>
																@endif
															 <h4 class="menu-title megamenu-title-list"><a href="{{ url('/shop') }}/?category={{$child->id}}">{{ $child->name }}	</a></h4>
															</li>
														@endforeach
													</ul>
												@endif
											</li>
										@endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form method="get" action="{{url('/shop')}}" class="header-search hs-expanded hs-round bg-white br-xs d-md-flex input-wrapper mr-4 ml-4">
								@php 
									$categoriesList = Category::where('status','1')->get();
								@endphp
								
								<div class="select-box border-no">
									<select id="category" name="category" class="form-select">
									  <option value="">All Categories</option>
									    @php renderParentCategories($categories, old('category', request('category')));@endphp
									</select>
								</div>
								
								<!--<div class="select-box border-no">
									<select id="category" name="category" class="form-select">
									  <option value="">All Categories</option>
									    @php
											renderCategorySingleOptions($categoriesList, null, 0, old('category', request('category')));
										@endphp
									</select>
								</div>-->
								
								<div class="wrap-suggestion">
    
                                <input type="text" class="form-control text-light border-no" id="ajax-search" name="search"  placeholder="Search in..." value="{{ request('search') }}" autocomplete="off">
                                
                               
                                
							 <ul id="search-results" class="suggestions-list"></ul> 
								</div>
                                <button type="submit" class="btn btn-search border-no" type="submit"><i class="w-icon-search"></i></button>
                            </form>
                        </div>
                        <div class="header-right ml-4 daily-deals">
                            @if(is_track_order()>0)
								<a href="{{url('/')}}" class="d-xl-show"><i class="w-icon-map-marker"></i>Track Order</a>
					        @endif
                            <a href="{{url('/')}}"><i class="w-icon-sale "></i>Daily Deals</a>
                        </div>
                    </div>
                </div>
            </div>
            
     


			<!-- Market Place New Desktop Menu -->
			<div class="market-place-new-desktop-menu">
				<nav class="market-place-navbar marketplace-scrollbar-wrapper">
				  <ul class="marketplace-scrollbar-horizontal-scroll" id="marketplace-scrollbar-mainMenu">
					@foreach($categories as $index => $category)
					  <li onmouseover="marketPlaceOpenMegaMenu('{{ $category->slug ?? 'cat'.$category->id }}')">
						@if($category->children->isEmpty())
						  <a href="{{ url('/shop') }}/?category={{ $category->id }}">{{ $category->name }}</a>
						@else
						  <a href="#">{{ $category->name }}</a>
						@endif
					  </li>
					@endforeach
				  </ul>

				  <!-- Arrows -->
				  <div class="marketplace-scrollbar-arrows">
					<button class="marketplace-scrollbar-btn marketplace-scrollbar-prev">
					  <i class="w-icon-angle-left"></i>
					</button>
					<button class="marketplace-scrollbar-btn marketplace-scrollbar-next">
					  <i class="w-icon-angle-right"></i>
					</button>
				  </div>
				  <!-- Arrows end -->
				</nav>
				 

				<script>
				(function() {
				  const container = document.querySelector('.marketplace-scrollbar-horizontal-scroll');
				  const nextBtn = document.querySelector('.marketplace-scrollbar-next');
				  const prevBtn = document.querySelector('.marketplace-scrollbar-prev');

				  if (!container || !nextBtn || !prevBtn) return;

				  const scrollAmount = 200; // px per click

				  // Arrows functionality
				  nextBtn.addEventListener('click', () => {
					container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
				  });

				  prevBtn.addEventListener('click', () => {
					container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
				  });

				  // Touch/swipe functionality
				  let isDown = false;
				  let startX;
				  let scrollLeft;

				  container.addEventListener('mousedown', (e) => {
					isDown = true;
					startX = e.pageX - container.offsetLeft;
					scrollLeft = container.scrollLeft;
					container.classList.add('active');
				  });

				  container.addEventListener('mouseleave', () => isDown = false);
				  container.addEventListener('mouseup', () => isDown = false);
				  container.addEventListener('mousemove', (e) => {
					if (!isDown) return;
					e.preventDefault();
					const x = e.pageX - container.offsetLeft;
					const walk = (x - startX) * 1; // scroll-fast multiplier
					container.scrollLeft = scrollLeft - walk;
				  });

				  // Touch events for mobile
				  container.addEventListener('touchstart', (e) => {
					startX = e.touches[0].pageX - container.offsetLeft;
					scrollLeft = container.scrollLeft;
				  });

				  container.addEventListener('touchmove', (e) => {
					const x = e.touches[0].pageX - container.offsetLeft;
					const walk = (x - startX) * 1; // scroll speed
					container.scrollLeft = scrollLeft - walk;
				  });
				})();
				</script>


				<!-- Mega Menu -->
				<div class="market-place-mega-menu" id="market-place-megaMenu">
					<!-- Left Sidebar -->
					<div class="market-place-mega-left">
						<ul>
							@foreach($categories as $index => $category)
								<li data-type="{{ $category->slug ?? 'cat'.$category->id }}" class="{{ $index === 0 ? 'active' : '' }}">
									@if($category->children->isEmpty())
									  <a href="{{ url('/shop') }}/?category={{ $category->id }}">{{ $category->name }} </a>
									  <i class="w-icon-angle-right"></i>
									@else
									  {{ $category->name }}
									  <i class="w-icon-angle-right"></i>
									@endif
								</li>
							@endforeach
						</ul>
					</div>

					<!-- Right Panels -->
					@foreach($categories as $index => $category)
						<div class="market-place-mega-right {{ $index === 0 ? 'active' : '' }}" id="market-place-submenu-{{ $category->slug ?? 'cat'.$category->id }}">
							
							<!--category with subcategory -->
							<div class="category-with-subcategory">
								<div class="menu-left-panel">
									<div class="menu-left-panel-box">
										<div class="main-category-group">
											@if($category->children->isEmpty())
												<a href="{{ url('/shop') }}/?category={{ $category->id }}"><h3>{{ $category->name }}</h3></a>
											@else
												<h3>{{ $category->name }}</h3>
											@endif

											<div class="market-place-sub-grid">
												@foreach($category->children as $child)
													<div>
														<a href="{{ url('/shop') }}/?category={{ $child->id }}">
															@if(!empty($child->desktop_image))
																<img src="{{ asset('/uploads/categories/' . $child->desktop_image) }}" alt="{{ $child->name }}" width="80" height="80">
															@else
																<img src="{{ asset('/frontend/assets/no-image.webp') }}" alt="{{ $child->name }}" width="80" height="80">
															@endif
															<p>{{ $child->name }}</p>
														</a>
													</div>
												@endforeach
											</div>
										</div>  
									</div>
								</div>

								<!--  CHILED MEGA MENU START-->    
								<div class="menu-right-panel">
									<div class="menu-right-panel-box">       
										@foreach($category->children as $child)
											<div class="subcategory-group">
												<h3 class="right-panel-heading">
													<a href="{{ url('/shop') }}/?category={{ $child->id }}">{{ $child->name }}</a>
												</h3>

												<div class="market-place-sub-grid">
													@if($child->children && $child->children->count())
														@foreach($child->children as $grandchild)
															<div>
																<a href="{{ url('/shop') }}/?category={{ $grandchild->id }}">
																	@if(!empty($grandchild->desktop_image))
																		<img src="{{ asset('/uploads/categories/' . $grandchild->desktop_image) }}" alt="{{ $grandchild->name }}" width="80" height="80">
																	@else
																		<img src="{{ asset('/frontend/assets/no-image.webp') }}" alt="{{ $grandchild->name }}" width="80" height="80">
																	@endif
																	<p>{{ $grandchild->name }}</p>
																</a>
															</div>
														@endforeach
													@else
														<div>
															<a href="{{ url('/shop') }}/?category={{ $child->id }}">
																@if(!empty($child->desktop_image))
																	<img src="{{ asset('/uploads/categories/' . $child->desktop_image) }}" alt="{{ $child->name }}" width="80" height="80">
																@else
																	<img src="{{ asset('/frontend/assets/no-image.webp') }}" alt="{{ $child->name }}" width="80" height="80">
																@endif
																<p>{{ $child->name }}</p>
															</a>
														</div>
													@endif
												</div>
											</div>
										@endforeach
									</div>
								</div>
								<!--  CHILED MEGA MENU END-->      
							</div>
							<!--category with subcategory end -->

						</div>
					@endforeach

				</div>
			</div>
			<!-- Market Place New Desktop Menu End -->   
        </header>
        <!-- End of Header -->
		
		
		
		<!-- Start of Main -->
		   
		   @yield('content')
		
		<!-- End of Main -->
		
		
		<!-- Start of Footer -->
        <footer class="footer appear-animate" data-animation-options="{
            'name': 'fadeIn'
        }">
            <div class="footer-newsletter">
                <div class="container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-xl-5 col-lg-6">
                            <div class="icon-box icon-box-side text-white">
                                <div class="icon-box-icon d-inline-flex">
                                    <i class="w-icon-envelop3"></i>
                                </div>
                                <div class="icon-box-content">
                                    <h4 class="icon-box-title text-white text-uppercase font-weight-bold">Subscribe To
                                        Our Newsletter</h4>
                                    <p class="text-white">Get all the latest information on Events, Sales and Offers.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7 col-lg-6 col-md-9 mt-4 mt-lg-0 ">
                            <form id="newsletter-form" class="input-wrapper input-wrapper-inline input-wrapper-rounded">
							    @CSRF
                                <input type="email" id="newsletter-email" class="form-control mr-2 bg-white pl-3" name="email" id="email" placeholder="Your E-mail Address" required>
                                <button class="btn btn-primary btn-rounded" type="submit">Subscribe
                                    <i class="w-icon-long-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                @include('layouts.top_footer')
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
    <!-- End of Page-wrapper -->

    <!-- Start of Sticky Footer -->
	@include('layouts.bottom_footer')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('newsletter-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('newsletter-email').value;
    const token = document.querySelector('input[name="_token"]').value;
	
	Swal.fire({
        title: 'Subscribing...',
        html: 'Please wait while we process your request.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
	
    fetch("{{ route('newsletter.subscribe') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email })
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();

            if (errorData.errors) {
                const allErrors = Object.values(errorData.errors)
                    .flat()
                    .join('\n');

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: allErrors
                });
            }

            throw new Error('Validation failed');
        }

        return response.json();
    })
    .then(data => {
        Swal.fire({
            icon: 'success',
			iconColor: '#00c853',
			confirmButtonText: 'Awesome!',
			confirmButtonColor: '#00c853',
			background: '#f4f6f8',
		    timerProgressBar: true,
            title: data.message || 'Subscribed!',
            timer: 4000,
            showConfirmButton: true
        });

        document.getElementById('newsletter-form').reset();
    })
    .catch(error => {
        console.error(error);
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const isMobile = window.innerWidth <= 768;

        document.querySelectorAll('.category-icon').forEach(function (icon) {
            const img = icon.querySelector('img');
            const desktopImage = icon.dataset.desktop;
            const mobileImage = icon.dataset.mobile;

            if (img) {
                img.src = isMobile ? mobileImage : desktopImage;
            }
        });
    });
</script>
<script>
    window.currency       = @json(getDefaultSelectedCurrency());
    window.currencyInfo   = @json(formatCurrencyPriceCalculateViaJs());
    window.baseProductUrl = "{{ url('/product') }}";
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const siteUrl = "{{ url('/') }}";

    loadCart();

    // Toggle cart dropdown
    document.querySelector('.cart-toggle')?.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('.cart-dropdown').classList.toggle('opened');
        document.querySelector('.cart-overlay').classList.toggle('active');
    });

    // Close dropdown
    document.querySelectorAll('.cart-dropdown .btn-close').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector('.cart-dropdown').classList.remove('opened');
            document.querySelector('.cart-overlay').classList.remove('active');
        });
    });

    // Add to cart
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-check-product');
        if (button) {
            e.preventDefault();
            const productId = button.dataset.id;
            const name      = button.dataset.name;
            const slug      = button.dataset.slug;
            const quantity  = parseInt(document.querySelector('#product-quantity')?.value) || 1;
            const hasVariant = button.dataset.hasVariant === '1';

            if (hasVariant) {
                window.location.href = `${siteUrl}/product/${slug}`;
                return;
            }

            fetch(`${siteUrl}/cart/add`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderNewCart(data.cart_items, data.cart_total);
					document.querySelector('.cart-dropdown').classList.add('opened');
                    document.querySelector('.cart-overlay').classList.add('active');
                    showToast(name + " added to cart!");
                } else {
                    errorMsgShowToast(data.message);
                }
            });
        }
    });

    // Remove from cart
    document.body.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.product-cart .btn-close');
        if (removeBtn) {
            e.preventDefault();
            const cartKey = removeBtn.dataset.key;

            fetch("{{ url('/cart/remove') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cart_key: cartKey })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderNewCart(data.cart_items, data.cart_total);
                } else {
                    alert('Failed to remove item from cart');
                }
            });
        }
    });
});

// Load cart
function loadCart() {
    const siteUrl = "{{ url('/') }}";
    fetch(`${siteUrl}/cart/get`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderNewCart(data.cart_items, data.cart_total);
            }
        });
}
function formatCurrencyPriceCalculateJS(amount, rate = 1) {
    return amount * rate;
}

function priceToLocaleString(amount=0) {
    return amount.toLocaleString('en-US', {
		  minimumFractionDigits: 2,
		  maximumFractionDigits: 2
		});
}
// Render cart dropdown
function renderNewCart(cartItems, cartTotal) {
    const productsContainer = document.querySelector('.cart-dropdown .products');
    const cartCount = document.querySelector('.cart-count');
    const cartTotalEl = document.querySelector('.cart-total .price');
    
    const currency = window.currency;
    const currencyInfo = window.currencyInfo;
    const baseProductUrl = window.baseProductUrl;
    
    productsContainer.innerHTML = ''; // Clear old content

    if (cartItems.length === 0) {
        productsContainer.innerHTML = '<p class="text-center">Your cart is empty.</p>';
        cartCount.textContent = '0';
        cartTotalEl.textContent = currencyInfo.symbol + '0.00';
        return;
    }

    let totalQuantity = 0;

    cartItems.forEach(item => {
        totalQuantity += item.quantity;

        // Calculate price converted via rate
        const currencyCalPriceInfo  = formatCurrencyPriceCalculateJS(item.price, currencyInfo.rate);
        const currencyCalPrice 	    = priceToLocaleString(currencyCalPriceInfo);
		
        const product = document.createElement('div');
        product.className = 'product product-cart';
        product.innerHTML = `
            <div class="product-detail">
                <a href="${baseProductUrl}/${item.slug}" class="product-name">${item.name}</a>
                ${item.variant_text ? `<div class="text-muted small" style="font-size: 11px;font-weight: bold;">${item.variant_text}</div><br/>` : ''}
                <p class="text-muted small" style="font-size: 11px;"><b>Vendor:</b> ${item.vendor} <br/><b>Origin:</b> ${item.origin}</p>
                <div class="price-box">
                    <span class="product-quantity">${item.quantity}</span>
                    <span class="product-price">${currencyInfo.symbol}${currencyCalPrice}</span>
                </div>
            </div>
            <figure class="product-media">
                <a href="${baseProductUrl}/${item.slug}">
                    <img src="${item.image}" alt="${item.name}" width="84" height="94">
                </a>
            </figure>
            <button class="btn btn-link btn-close" data-key="${item.cart_key}">
                <i class="fas fa-times"></i>
            </button>
        `;
        productsContainer.appendChild(product);
    });

    cartCount.textContent = totalQuantity;
    const convertedTotalInfo = cartTotal * currencyInfo.rate;
	const convertedTotal    = priceToLocaleString(convertedTotalInfo);
    cartTotalEl.textContent = `${currencyInfo.symbol}${convertedTotal}`;
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.visibility = 'visible';
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.visibility = 'hidden';
    }, 3000);
}

function errorMsgShowToast(message) {
    const toast = document.getElementById('toastError');
    toast.textContent = message;
    toast.style.visibility = 'visible';
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.visibility = 'hidden';
    }, 3000);
}
</script>
<div id="toast" style="
    visibility: hidden;
    opacity: 0;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-10px);
    background: linear-gradient(135deg, #4caf50, #2e7d32); /* Green gradient */
    color: #fff;
    padding: 14px 20px;
    border-radius: 6px;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: opacity 0.4s ease, transform 0.4s ease;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 280px;
    max-width: 400px;
"></div>

<div id="toastError" style="
    visibility: hidden;
    opacity: 0;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-10px);
    background: linear-gradient(135deg, #f44336, #c62828); /* Red error gradient */
    color: #fff;
    padding: 14px 20px;
    border-radius: 6px;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: opacity 0.4s ease, transform 0.4s ease;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 280px;
    max-width: 400px;
"></div>




    <!-- End of Newsletter popup -->
    <!-- Plugin JS File -->
    <script src="{{ asset('frontend/assets/vendor/jquery/jquery.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/parallax/parallax.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/jquery.plugin/jquery.plugin.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/imagesloaded/imagesloaded.pkgd.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/isotope/isotope.pkgd.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/skrollr/skrollr.min-1.js')}}"></script>
    <!--<script src="{{ asset('frontend/assets/vendor/magnific-popup/jquery.magnific-popup.min-1.js')}}"></script>-->
    <script src="{{ asset('frontend/assets/vendor/zoom/jquery.zoom-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/vendor/jquery.countdown/jquery.countdown.min-1.js')}}"></script>

    <!-- Main JS -->
    <script src="{{ asset('frontend/assets/js/main.min-1.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/custom.js') }}?v={{ time() }}"></script>
     <script src="{{ asset('frontend/assets/js/mega-menu.js') }}?v={{ time() }}"></script>
   

	
<script>
$(document).ready(function(){
    
	const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
	
	const siteUrl = "{{ url('/') }}";
	$('.btn-wishlist').click(function () {
		
		if(isLoggedIn) {
			
			var productId = $(this).data('id');
				$.ajax({
					url: siteUrl+'/account/wishlist/add',
					type: 'POST',
					data: {
						product_id: productId,
						_token: '{{ csrf_token() }}'
					},
					success: function (res) {
						showToast(res.message);
					}
				});
				
			}else{
				errorMsgShowToast('Please login first.');
			}
		});	
		
		
		$(document).on('click', '.btn-remove-wishlist', function () {
			const productId = $(this).data('id');

			$.ajax({
				url: siteUrl + '/account/wishlist/remove/' + productId,
				type: 'DELETE',
				data: {
					_token: '{{ csrf_token() }}'
				},
				success: function (res) {
					showToast(res.message);
					location.reload();
				}
			});
		});	
});



</script>



<script>
$(function(){ 
  const $input = $('#ajax-search');
  const $list = $('#search-results');
  let selected = -1;

  const hideList = () => $list.addClass('d-none').empty();

  $input.on('input', function(){
    const q = this.value.trim();
    clearTimeout($input.data('delay'));
    if (q.length < 1) return hideList();

    const delay = setTimeout(() => {
      $.get("{{ route('search.suggestions') }}", { q }, function(data){
        if (!data || !data.length) return hideList();

        selected = -1; $list.removeClass('d-none').empty();

        data.forEach(item => {
          $list.append(`
            <li class="list-group-item list-group-item-action"
                data-url="${item.url}">
              <small class="text-muted">${item.type}</small><br>
              ${item.label}
            </li>`);
        });
      });
    }, 200);

    $input.data('delay', delay);
  });

  $input.on('keydown', function(e) {
    if ($list.hasClass('d-none')) return;
    const $items = $list.find('li');
    const max = $items.length - 1;

    if (e.key === 'ArrowDown' && selected < max) {
      e.preventDefault();
      selected++;
    }
    if (e.key === 'ArrowUp' && selected > 0) {
      e.preventDefault();
      selected--;
    }
    if (e.key === 'Enter') {
      e.preventDefault();
      if (selected >= 0) {
        window.location = $items.eq(selected).data('url');
        return;
      }
    }

    $items.removeClass('active');
    if (selected >= 0) $items.eq(selected).addClass('active');
  });

  $list.on('click', 'li', function(){
    window.location = $(this).data('url');
  });

  $('body').on('click', function(e){
    if (!$(e.target).closest($input).length) hideList();
  });
});


$('#select2-search').select2({
  placeholder: 'Search products or brands',
  minimumInputLength: 2,
  ajax: {
    url: "{{ route('search.suggestions') }}",
    dataType: 'json',
    delay: 250,
    processResults: data => ({
      results: data.map(item => ({
        id: item.url,
        text: `[${item.type}] ${item.label}`
      }))
    }),
    cache: true
  }
}).on('select2:select', e => {
  window.location = e.params.data.id;
});


</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.mobile-menu .category-toggle');

    toggles.forEach(toggle => {
      toggle.addEventListener('click', function (e) {
        const parentLi = this.closest('.has-children');

        // Ignore clicks if it doesn't have children
        if (!parentLi) return;

        // Toggle expanded class
        parentLi.classList.toggle('expanded');

        // Toggle subcategory visibility
        const submenu = parentLi.querySelector('.subcategory');
        if (submenu) {
          submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        }
      });
    });
  });
</script>



<!--chatbot UI -->
<!-- Chatbot Widget -->
<div class="ondjangobay-chatbot-container">
  <button id="ondjangobay-chatbot-toggle" class="ondjangobay-chatbot-toggle">💬</button>
  
  <div id="ondjangobay-chatbot-box" class="ondjangobay-chatbot-box">
    <div class="ondjangobay-chatbot-header">
      <span>💬 Ondjangobay Chat</span>
      <button id="ondjangobay-chatbot-close" class="ondjangobay-chatbot-close">&times;</button>
    </div>
    
    <div id="ondjangobay-chatbot-messages" class="ondjangobay-chatbot-messages">
      <div class="ondjangobay-bot-message">👋 Hi there! How can I assist you today?</div>
    </div>
    
    <div class="ondjangobay-chatbot-input">
      <input type="text" id="ondjangobay-user-input" class="ondjangobay-user-input" placeholder="Type your message..." />
      <button id="ondjangobay-send-btn" class="ondjangobay-send-btn">➤</button>
    </div>
  </div>
</div>

 

<script>
const toggleBtn = document.getElementById('ondjangobay-chatbot-toggle');
const chatbotBox = document.getElementById('ondjangobay-chatbot-box');
const closeBtn = document.getElementById('ondjangobay-chatbot-close');
const sendBtn = document.getElementById('ondjangobay-send-btn');
const userInput = document.getElementById('ondjangobay-user-input');
const messages = document.getElementById('ondjangobay-chatbot-messages');

// Open chat
toggleBtn.addEventListener('click', () => {
  chatbotBox.style.display = 'flex';
  toggleBtn.style.display = 'none';
});

// Close chat
closeBtn.addEventListener('click', () => {
  chatbotBox.style.display = 'none';
  toggleBtn.style.display = 'block';
});

// Send on button click
sendBtn.addEventListener('click', sendMessage);

// Send on Enter key
userInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') sendMessage();
});

function sendMessage() {
  const text = userInput.value.trim();
  if (text === "") return;
  
  // Add user message
  const userMsg = document.createElement('div');
  userMsg.className = 'ondjangobay-user-message';
  userMsg.textContent = text;
  messages.appendChild(userMsg);
  
  userInput.value = "";
  messages.scrollTop = messages.scrollHeight;

  // Fake bot reply
  setTimeout(() => {
    const botMsg = document.createElement('div');
    botMsg.className = 'ondjangobay-bot-message';
    botMsg.textContent = "🤖 Thanks for your message! We'll get back to you.";
    messages.appendChild(botMsg);
    messages.scrollTop = messages.scrollHeight;
  }, 1000);
}
</script>

<!--chatbot UI end-->

 
 <!--Login Facebook & Google start-->
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
 <!--Login Facebook & Google end-->
 
<!--Login popup via ajax start-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
	const loader = document.getElementById('loginLoader');
	
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
		
		loader.style.display = 'block';
        
        document.querySelectorAll('.error').forEach(el => el.innerHTML = '');

        const formData = new FormData(loginForm);

        fetch(loginForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
			loader.style.display = 'none';
            if (response.ok) {
                window.location.reload(); 
            } else {
                const data = await response.json();
                if (data.errors) {
                    if (data.errors.email) {
                        document.getElementById('email_error').innerText = data.errors.email[0];
                    }
                    if (data.errors.password) {
                        document.getElementById('password_error').innerText = data.errors.password[0];
                    }
                } else if (data.message) {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Login failed:', error);
        });
    });
});
</script>
 <!--Login popup via ajax end -->
 
 <!--Login magic link popup via ajax start-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const loginFormMagic = document.getElementById('loginFormMagic');
	const loadermagic = document.getElementById('loginLoaderMagic');
	
    loginFormMagic.addEventListener('submit', function (e) {
        e.preventDefault();
		
		loadermagic.style.display = 'block';
        
        document.querySelectorAll('.error').forEach(el => el.innerHTML = '');

        const formData = new FormData(loginFormMagic);

        fetch(loginFormMagic.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
			loadermagic.style.display = 'none';
            $('#email_success_magic').show();
			if (response.ok) { 
				const data = await response.json();
				$('#emailMagic').val('');
				document.getElementById('email_success_magic').innerText = data.message;				
               // window.location.reload();
				setTimeout(() => {
					$('#email_success_magic').hide();
				}, 4000);
			   
            } else {
                const data = await response.json();
                if (data.errors) {
                    if (data.errors.email) {
						$('#email_error_magic').show();
                        document.getElementById('email_error_magic').innerText = data.errors.email[0];
						setTimeout(() => {
							$('#email_error_magic').hide();
						}, 4000);
                    }
                } else if (data.message) {
                    alert(data.message);
                }
				
            }
        })
        .catch(error => {
            console.error('Login failed:', error);
        });
    });
	
});
</script>
 <!--Login magic link popup via ajax end -->
 
  <!--Register popup via ajax start -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');
	const loaderReg = document.getElementById('loginLoaderRegister');
	
    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
		loaderReg.style.display = 'block';
		
        document.querySelectorAll('.error').forEach(el => el.innerText = '');

        const formData = new FormData(registerForm);

        fetch(registerForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
			loaderReg.style.display = 'none';
            if (response.ok) {
                window.location.reload();
			}else{
                const data = await response.json();
                if (data.errors) { 
                    if (data.errors.name) {
                        document.getElementById('nameError').innerText = data.errors.name[0];
                    }
                    if (data.errors.email) {
                        document.getElementById('emailError').innerText = data.errors.email[0];
                    }
                    if (data.errors.password) {
                        document.getElementById('passwordError').innerText = data.errors.password[0];
                    }
                    if (data.errors.password_confirmation) {
                        document.getElementById('password_confirmationError').innerText = data.errors.password_confirmation[0];
                    }
                }
            }
        })
        .catch(error => {
            console.error('Registration failed:', error);
        });
    });
});
</script>
  <!--Register popup via ajax end -->
  
 <!--Google translate js start -->
<script type="text/javascript">
	document.addEventListener("GoogleTranslateReady", function () {
		
		const savedLang = localStorage.getItem('selectedLanguage');
		if (savedLang) {
			const { lang, flagSrc, label } = JSON.parse(savedLang);

			const applySavedLanguage = () => {
				const selectField = document.querySelector(".goog-te-combo");
				if (selectField) {
					selectField.value = lang;
					selectField.dispatchEvent(new Event('change', { bubbles: true }));

					const selectedLangLink = document.getElementById('selected-language');
					if (selectedLangLink) {
						selectedLangLink.innerHTML = `<img src="${flagSrc}" width="14" height="8" class="dropdown-image"> ${label}`;
					}
				} else {
					setTimeout(applySavedLanguage, 200);
				}
			};

			applySavedLanguage();
		}
	});

	function googleTranslateElementInit() {
		new google.translate.TranslateElement({
			pageLanguage: 'en',
			autoDisplay: false
		}, 'google_translate_element');
		document.dispatchEvent(new Event('GoogleTranslateReady'));
	}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script>
	function translateLanguage(lang) {
		const selectField = document.querySelector(".goog-te-combo");

		if (selectField) {
			selectField.value = lang;
			selectField.dispatchEvent(new Event('change', { bubbles: true }));

			const langLink = document.querySelector(`.dropdown-box a[data-lang="${lang}"]`);
			if (langLink) {
				const flagSrc = langLink.getAttribute('data-flag');
				const label = langLink.getAttribute('data-label');

				const selectedLangLink = document.getElementById('selected-language');
				if (selectedLangLink) {
					selectedLangLink.innerHTML = `<img src="${flagSrc}" width="14" height="8" class="dropdown-image"> ${label}`;
				}

				localStorage.setItem('selectedLanguage', JSON.stringify({
					lang,
					flagSrc,
					label
				}));
			}
		} else {
			console.warn("Google Translate not initialized yet.");
		}
	}
</script>
<!--Google translate js end -->
 <!-- COMPARE PRODUCT START-->
<script>
function toggleComparison(productId) {
    const button = document.getElementById(`comparison-btn-${productId}`);
    
    fetch(`{{ route('compare.index') }}/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { 
			showToast(data.message);
        } else {
			errorMsgShowToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
		errorMsgShowToast('Error adding to comparison');
    });
}
</script>
<!-- COMPARE PRODUCT END-->
 

</body>
</html>