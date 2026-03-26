@extends('layouts.app_inner')
@section('title', 'Checkout')
@section('content')
	<!-- Start of Main -->
        <hr/>
		<main class="main checkout">
            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb shop-breadcrumb bb-no">
                        <li class="passed"><a href="{{url('/cart')}}">Shopping Cart</a></li>
                        <li class="active"><a href="{{url('/')}}">Checkout</a></li>
                        <li>Order Complete</li>
                    </ul>
                </div>
            </nav>
			<hr/>
            <!-- End of Breadcrumb -->


            <!-- Start of PageContent -->
            <div class="page-content">
                <div class="container">
                    @if(empty(auth()->id()))
					<div class="login-toggle">
						Returning customer?
						<a href="#" class="show-login font-weight-bold text-uppercase text-dark">Login</a>
					</div>

					<!--<form class="login-content" action="{{ route('login') }}" id="ajax-login-form" method="POST">-->
					<form class="login-content open" id="ajax-login-form" method="POST" style="display: block; overflow: hidden;">
						@csrf
						<p>
							If you have shopped with us before, please enter your details below.
							If you are a new customer, please proceed to the Billing section.
						</p>

						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<label for="email">Username or Email *</label>
									<input type="email" class="form-control form-control-md @error('email') is-invalid @enderror"
										   name="email" id="email" value="{{ old('email') }}" required autofocus>

									@error('email')
										<span class="invalid-feedback d-block" role="alert">
											<strong style="color: red;">{{ $message }}</strong>
										</span>
									@enderror
								</div>
							</div>

							<div class="col-xs-6">
								<div class="form-group">
									<label for="password">Password *</label>
									<input type="password" class="form-control form-control-md @error('password') is-invalid @enderror"
										   name="password" id="password" required>

									@error('password')
										<span class="invalid-feedback d-block" role="alert">
											<strong style="color: red;">{{ $message }}</strong>
										</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group checkbox">
							<input type="checkbox" class="custom-checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
							<label for="remember" class="mb-0 lh-2">Remember me</label>
							<a href="{{ route('user.password.request') }}" class="ml-3">Forgot your password?</a>
						</div>
						<button type="submit" class="btn btn-rounded btn-login">Login</button>
					</form>
				
                    <!--<div class="coupon-toggle">
                        Have a coupon? <a href="#" class="show-coupon font-weight-bold text-uppercase text-dark">Enter your
                            code</a>
                    </div>
                    <div class="coupon-content mb-4">
                        <p>If you have a coupon code, please apply it below.</p>
                        <div class="input-wrapper-inline">
                            <input type="text" name="coupon_code" class="form-control form-control-md mr-1 mb-2" placeholder="Coupon code" id="coupon_code">
                            <button type="submit" class="btn button btn-rounded btn-coupon mb-2" name="apply_coupon" value="Apply coupon">Apply Coupon</button>
                        </div>
                    </div>-->
					
				@else	
					
					@if (session('success'))
						<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
							{{ session('success') }}
						</div><br/>
					@endif

					@if (session('error'))
						<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
							{{ session('error') }}
						</div><br/>
					@endif
					
					<div class="coupon-toggle mb-3">
						Have a coupon? 
						<a href="javascript:void(0);" class="show-coupon font-weight-bold text-uppercase text-dark">
							Enter your code
						</a>
					</div>
					<div class="coupon-content mb-4" style="display: block;"> <!-- Changed to display: block -->
						@if(session('coupon'))
							<p>Coupon Applied: <strong>({{ session('coupon.code') }})</strong> <a href="{{url('/cart/removeCoupon')}}" style="color:red;" onclick="return validateCouponDelete(this);">Remove</a></p>
							<p>Discount: {{formatCurrency(session('coupon.discount'))}}</p>
						@endif
						<p>If you have a coupon code, please apply it below.</p>
						<form action="{{ route('cart.applyCoupon') }}" method="POST" class="coupon11">
							@csrf
							<div class="input-wrapper-inline d-flex align-items-center gap-2">
								<input type="text" name="code" class="form-control form-control-md mr-1 mb-2" 
									   placeholder="Coupon code" id="coupon_code" required>
								<button type="submit" class="btn btn-dark btn-rounded mb-2" 
										name="apply_coupon" value="Apply coupon">
									Apply Coupon
								</button>
							</div>
						</form>			
					</div>

					<script>
					document.addEventListener("DOMContentLoaded", function() {
						const toggleLink = document.querySelector(".show-coupon");
						const couponContent = document.querySelector(".coupon-content");

						toggleLink.addEventListener("click", function(e) {
							e.preventDefault();
							// Toggle visibility with smooth effect
							if (couponContent.style.display === "none" || couponContent.style.display === "") {
								couponContent.style.display = "block";
								couponContent.classList.add("fadeIn");
							} else {
								couponContent.style.display = "none";
							}
						});
					});
					</script>

					<form class="form checkout-form" action="{{ route('checkout.placeOrder') }}" method="POST">
                        @CSRF
						<div class="row mb-9">
                            <div class="col-lg-7 pr-lg-4 mb-4">
							
								@if(count($addresses) > 0)
									<h4>Select an Address</h4><hr/>
									<div class="row gutter-sm">
									@foreach($addresses as $addressList)	
										<div class="col-sm-6 mb-0">
											<div class="ecommerce-address billing-address pr-lg-8">
												<address class="mb-4">
													<label>
													    @if($addressList->is_default=='1')
														    <input type="radio" name="address_id" value="{{ $addressList->id }}" required checked>
														@else
														    <input type="radio" name="address_id" value="{{ $addressList->id }}" required>
                                                        @endif													
													</label>
													<table class="address-table">
														<tbody>
															<tr>
																<th>Name:</th>
																<td>{{$addressList->first_name ?? "" }} {{$addressList->last_name ?? ""}}</td>
															</tr>
															<tr>
																<th>Company:</th>
																<td>{{$addressList->company ?? ""}}</td>
															</tr>
															<tr>
																<th>Address:</th>
																<td>{{$addressList->address_1}} {{$addressList->address_2 }}</td>
															</tr>
															<tr>
																<th>City:</th>
																<td>{{$addressList->city ?? ""}}</td>
															</tr>
															<tr>
																<th>Country:</th>
																<td>{{$addressList->country ?? ""}}</td>
															</tr>
															<tr>
																<th>State:</th>
																<td>{{$addressList->state ?? ""}}</td>
															</tr>
															<tr>
																<th>Postcode:</th>
																<td>{{$addressList->zipcode ?? ""}}</td>
															</tr>
															<tr>
																<th>Phone:</th>
																<td>{{$addressList->phone ?? ""}}</td>
															</tr>
														</tbody>
													</table>
												</address>
											</div>
										</div>
									@endforeach
									<hr/>
									<div class="col-sm-6 mb-2">
									    <label><input type="checkbox" name="use_new" id="use_new_address"> &nbsp;Add new address</label>
									</div>
									<hr/>
									</div><br/>
								@else
									<input type="hidden" name="use_new"  value="on" required>
								@endif
								
							<div id="new-address-form" style="{{ count($addresses) > 0 ? 'display:none;' : '' }}">
							
                                <h3 class="title billing-title text-uppercase ls-10 pt-1 pb-3 mb-0">
                                    Billing Details
                                </h3>
                                <div class="row gutter-sm">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>First name *</label>
											<input type="text" class="form-control form-control-md" id="billing_first_name" name="billing_first_name" placeholder="First name" value="{{ auth()->user()->name ?? old('billing_first_name') }}">
											@error('billing_first_name')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                    </div>
                                   <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Last name *</label>
											<input type="text" class="form-control form-control-md" id="inp-name" name="billing_last_name" placeholder="Last name" value="{{ auth()->user()->last_name ?? old('billing_last_name') }}">
											@error('billing_last_name')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Company name (optional)</label>
                                    <input type="text" class="form-control form-control-md" name="billing_company_name" placeholder="Company name" value="{{ old('billing_company_name') }}">
									@error('billing_company_name')
										<span class="invalid-feedback" role="alert" style="color:red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
                                </div>
                                <div class="form-group">
                                    <label>Country / Region *</label>
                                    <div class="select-box">
                                        <select name="billing_country" id="billing_country" onchange="getStateList(this.value)" class="form-control form-control-md">
                                            <option value="">-Select-</option>
											@if(!empty($country))
												@foreach($country as $countryList)
											        @if($countryList->name==$defaultSetting->country)
														<option value="{{$countryList->name}}" selected>{{$countryList->name}}</option>
													@else
											            <option value="{{$countryList->name}}">{{$countryList->name}}</option>
											        @endif
											    @endforeach
											@endif
                                        </select>
										@error('billing_country')
											<span class="invalid-feedback" role="alert" style="color:red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
										
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Street address *</label>
                                    <input type="text" placeholder="House number and street name" class="form-control form-control-md mb-2" value="{{ old('billing_address_1') }}" name="billing_address_1">
									@error('billing_address_1')
										<span class="invalid-feedback" role="alert" style="color:red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
                                    <input type="text" placeholder="Apartment, suite, unit, etc. (optional)" class="form-control form-control-md" value="{{ old('billing_address_2') }}" name="billing_address_2">
									@error('billing_address_2')
										<span class="invalid-feedback" role="alert" style="color:red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
                                </div>
                                <div class="row gutter-sm">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Town / City *</label>
                                            <input type="text" class="form-control form-control-md" name="billing_city" value="{{ old('billing_city') }}">
											@error('billing_city')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Zipcode </label>
                                            <input type="text" class="form-control form-control-md" name="billing_zip" value="{{ old('billing_zip') }}">
											@error('billing_zip')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!--<div class="form-group">
                                            <label>State *</label>
                                            <div class="select-box">
                                                <select name="billing_state" id="billing_state" class="form-control form-control-md">
                                                     <option value="">-Select-</option>
													 @if(!empty($state))
														@foreach($state as $stateList)
														   <option value="{{$stateList->name}}">{{$stateList->name}}</option>
														@endforeach
													@endif
                                                </select>
												@error('billing_state_id')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>-->
										<div class="form-group">
                                            <label>State *</label>
                                            <div class="select-box">
                                                <select name="billing_state" id="billing_state" class="form-control form-control-md">
                                                    <option value="">-Select-</option>
                                                </select>
												@error('billing_state_id')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone *</label>
                                            <input type="text" class="form-control form-control-md" name="billing_phone" value="{{ auth()->user()->phone ?? old('billing_phone') }}">
											@error('billing_phone')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-7">
                                    <label>Email address *</label>
                                    <input type="email" class="form-control form-control-md" name="billing_email"  value="{{ auth()->user()->email ?? old('billing_email') }}">
									@error('billing_email')
										<span class="invalid-feedback" role="alert" style="color:red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
                                </div>
                                <div class="form-group pb-2">
									<input type="checkbox" class="custom-checkbox" id="toggle-shipping-address" name="shipping_toggle_value" value="1">
									<label for="toggle-shipping-address">Ship to a different address?</label>
								</div>
															
                                <div class="shipping-address-section" style="display: none;">
                                    <div class="row gutter-sm">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label>First name *</label>
                                                <input type="text" class="form-control form-control-md"  value="{{ auth()->user()->name ?? old('shipping_first_name') }}" name="shipping_first_name">
												@error('shipping_first_name')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label>Last name *</label>
                                                <input type="text" class="form-control form-control-md" name="shipping_last_name"  value="{{ auth()->user()->last_name ?? old('shipping_last_name') }}">
												@error('shipping_last_name')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Company name (optional)</label>
                                        <input type="text" class="form-control form-control-md" name="shipping_company_name">
										@error('shipping_company_name')
											<span class="invalid-feedback" role="alert" style="color:red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                    </div>
									<div class="form-group">
                                        <label>Country / Region *</label>
                                        <div class="select-box">
                                            <select name="shipping_country" onchange="getShippingStateList(this.value)" class="form-control form-control-md">
                                                <option value="">-select-</option>
												@if(!empty($country))
													@foreach($country as $countryList)
														@if($countryList->name==$defaultSetting->country)
															<option value="{{$countryList->name}}" selected>{{$countryList->name}}</option>
														@else
															<option value="{{$countryList->name}}">{{$countryList->name}}</option>
														@endif
													@endforeach
												@endif
                                            </select>
											@error('shipping_country')
												<span class="invalid-feedback" role="alert" style="color:red;">
													<strong>{{ $message }}</strong>
												</span>
											@enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Street address *</label>
                                        <input type="text" placeholder="House number and street name" class="form-control form-control-md mb-2" name="shipping_street_address_1">
										@error('shipping_street_address_1')
											<span class="invalid-feedback" role="alert" style="color:red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                        <input type="text" placeholder="Apartment, suite, unit, etc. (optional)" class="form-control form-control-md" name="shipping_street_address_2">
										@error('shipping_street_address_2')
											<span class="invalid-feedback" role="alert" style="color:red;">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                    </div>
                                    <div class="row gutter-sm">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Town / City *</label>
                                                <input type="text" class="form-control form-control-md" name="shipping_city">
												@error('shipping_city')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Zipcode </label>
                                                <input type="text" class="form-control form-control-md" name="shipping_zipcode">
												@error('shipping_zipcode')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!--<div class="form-group">
                                                <label>State</label>
                                                <select name="shipping_state" id="shipping_state" class="form-control form-control-md">
                                                    <option value="" selected>-Select-</option>
													@if(!empty($state))
														@foreach($state as $stateList)
															<option value="{{$stateList->name}}">{{$stateList->name}}</option>
														@endforeach
													@endif
                                                </select>
												@error('shipping_state')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>-->
											 <div class="form-group">
                                                <label>State</label>
                                                <select name="shipping_state" id="shipping_state" class="form-control form-control-md">
                                                    <option value="" selected>-Select-</option>
                                                </select>
												@error('shipping_state')
													<span class="invalid-feedback" role="alert" style="color:red;">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="order-notes">Order notes (optional)</label>
                                    <textarea class="form-control mb-0" id="order-notes" name="order_notes" cols="30" rows="4" placeholder="Notes about your order, e.g special notes for delivery"></textarea>
									@error('order_notes')
										<span class="invalid-feedback" role="alert" style="color:red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-5 mb-4 sticky-sidebar-wrapper">
                                <div class="order-summary-wrapper sticky-sidebar">
                                    <h3 class="title text-uppercase ls-10">Your Order</h3>
                                    <div class="order-summary">
                                        <table class="order-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">
                                                        <b>Product</b>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
												@php $subtotal = 0; @endphp

												@foreach ($cartItems as $item)
													<tr class="bb-no">
														<td class="product-name">
															{{ $item['name'] }} <i class="fas fa-times"></i>
															<span class="product-quantity">{{ $item['quantity'] }}</span>
															
															@if (!empty($item['variant_text']))
																<div class="text-muted small" style="font-size: 11px;font-weight: bold;">{{$item['variant_text']}}</div>
															@endif
															@if (!empty($item['vendor']))
															  <p class="text-muted small" style="font-size: 11px;"><b style="font-size: 11px;">Vendor:</b> {{$item['vendor']}} <br/><b style="font-size: 11px;">Orgin:</b> {{$item['origin']}}</p>
															@endif
															
														</td>
														<td class="product-total">{{formatCurrency($item['subtotal'])}}</td>
													</tr>
													@php $subtotal += $item['subtotal']; @endphp
												@endforeach
												
												<tr class="cart-subtotal bb-no">
													<td><b>Subtotal</b></td>
													<td><b>{{formatCurrency($subtotal)}}</b></td>
												</tr>
											</tbody>
                                            <tfoot>
                                                <tr class="shipping-methods">
                                                    <td colspan="2" class="text-left">
                                                        <h4 class="title title-simple bb-no mb-1 pb-0 pt-3">Shipping
                                                        </h4>
                                                        <ul id="shipping-method" class="mb-4">
                                                            <li>
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="free-shipping" class="custom-control-input" name="shipping" checked>
                                                                    <label for="free-shipping" class="custom-control-label color-dark">Free
                                                                        Shipping</label>
                                                                </div>
                                                            </li>
                                                            <!--<li>
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="local-pickup" class="custom-control-input" name="shipping">
                                                                    <label for="local-pickup" class="custom-control-label color-dark">Local
                                                                        Pickup</label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="flat-rate" class="custom-control-input" name="shipping">
                                                                    <label for="flat-rate" class="custom-control-label color-dark">Flat
                                                                        rate: $5.00</label>
                                                                </div>
                                                            </li>-->
                                                        </ul>
                                                    </td>
                                                </tr>
												@if(session('coupon'))
													<tr class="order-total">
														<th><b>Coupon Applied</b>
														<span>({{ session('coupon.code') }}) <a href="{{url('/cart/removeCoupon')}}" style="color:red;" onclick="return validateCouponDelete(this);">Remove</a></span>
														</th>
														<td><b>{{formatCurrency(session('coupon.discount'))}}</b></td>
													</tr>
												@endif
												
                                                <tr class="order-total">
                                                    <th><b>Total</b></th>
													@if(session('coupon'))
													  <td><b>{{formatCurrency($subtotal-session('coupon.discount'))}}</b></td>
												    @else
														<td><b>{{formatCurrency($subtotal)}}</b></td>
													@endif
                                                </tr>
                                            </tfoot>
                                        </table>
											<div class="payment-methods" id="payment_method">
												<h4 class="title font-weight-bold ls-25 pb-0 mb-1">Payment Methods</h4><br/>
												<div class="accordion payment-accordion">
													@foreach($paymentGatewayList as $paymentList)
														@php
															// dynamic id for input
															$input_id = 'payment_'.strtolower($paymentList->name);
															// dynamic id for button/form container
															$container_id = 'section_'.strtolower($paymentList->name);
														@endphp
														<div class="card">
															<input type="radio" 
																   id="{{ $input_id }}" 
																   name="payment_method" 
																   value="{{ strtolower($paymentList->name) }}" 
																   @if($paymentList->is_default) checked @endif>
															<label for="{{ $input_id }}">{{ $paymentList->name }}</label>
														</div>
													@endforeach
												</div>
											</div>

											<div id="payment-sections">
												@foreach($paymentGatewayList as $paymentList)
													@php $container_id = 'section_'.strtolower($paymentList->name); @endphp
													<div id="{{ $container_id }}" class="payment-section d-none">
														@if(strtolower($paymentList->name) === 'cod')
															<button type="submit" class="btn btn-dark btn-block btn-rounded">Place Order</button>
														@elseif(strtolower($paymentList->name) === 'paypal')
															<button type="submit" class="btn btn-primary btn-block btn-rounded">Pay with PayPal</button>
														@elseif(strtolower($paymentList->name) === 'stripe')
															<button type="submit" class="btn btn-success btn-block btn-rounded">Pay with Stripe</button>
														@elseif(strtolower($paymentList->name) === 'wallet')
															<button type="submit" class="btn btn-info btn-block btn-rounded">Pay with Wallet</button>
														@else
															<button type="submit" class="btn btn-secondary btn-block btn-rounded">{{ $paymentList->name }}</button>
														@endif
													</div>
												@endforeach
											</div>

										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
				
				@endif
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->

	
<script>
function validateCouponDelete(msg){
	var confrm = confirm('Do you want to remove?.');
	if(confrm==false){
		return false;
	}
}
/* document.addEventListener("DOMContentLoaded", function () {
    const codButton = document.getElementById("cod_button");
    const paypalForm = document.getElementById("paypal_form");
    const stripeButton = document.getElementById("stripe_button");
    const walletButton = document.getElementById("payment_wallet");

    document.querySelectorAll("input[name='payment_method']").forEach(input => {
        input.addEventListener("change", function () {
            codButton.classList.add("d-none");
            paypalForm.classList.add("d-none");
            stripeButton.classList.add("d-none");

            if (this.value === "cod") {
                codButton.classList.remove("d-none");
            } else if (this.value === "paypal") {
                paypalForm.classList.remove("d-none");
            } else if (this.value === "stripe") {
                stripeButton.classList.remove("d-none");
            }
        });
    });
}); */
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const paymentInputs = document.querySelectorAll("input[name='payment_method']");
    const sections = document.querySelectorAll(".payment-section");

    function toggleSections(selectedValue) {
        sections.forEach(section => section.classList.add("d-none")); // hide all
        const activeSection = document.getElementById("section_" + selectedValue);
        if (activeSection) {
            activeSection.classList.remove("d-none"); // show selected
        }
    }
    paymentInputs.forEach(input => {
        input.addEventListener("change", function () {
            toggleSections(this.value);
        });
    });

    const defaultInput = document.querySelector("input[name='payment_method']:checked");
    if (defaultInput) {
        toggleSections(defaultInput.value);
    }
});
</script>



<script>
document.getElementById('use_new_address')?.addEventListener('change', function() {
    const form = document.getElementById('new-address-form');
    form.style.display = this.checked ? 'block' : 'none';
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('use_new_address');
    const form = document.getElementById('new-address-form');
    const newAddressFields = form.querySelectorAll('input[name]:not([type=hidden]), select[name]');

    // List of optional field names (they will NOT be set as required)
    const optionalFields = [
        'billing_company_name',
        'billing_address_2',
        'billing_email',
        'shipping_company_name',
        'shipping_street_address_2',
        'order_notes'
    ];

    function toggleNewAddressFields() {
        if (checkbox.checked) {
            form.style.display = 'block';

            /* newAddressFields.forEach(field => {
                if (!optionalFields.includes(field.name)) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            }); */
        } else {
            form.style.display = 'none';
           /*  newAddressFields.forEach(field => {
                field.removeAttribute('required');
            }); */
        }
    }

    checkbox?.addEventListener('change', toggleNewAddressFields);

    // Trigger on page load in case of old value
    toggleNewAddressFields();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('toggle-shipping-address');
    const shippingSection = document.querySelector('.shipping-address-section');

    // Function to copy billing values to shipping
    function copyBillingToShipping() {
        const mappings = {
            billing_first_name: 'shipping_first_name',
            billing_last_name: 'shipping_last_name',
            billing_company_name: 'shipping_company_name',
            billing_country: 'shipping_country',
            billing_address_1: 'shipping_street_address_1',
            billing_address_2: 'shipping_street_address_2',
            billing_city: 'shipping_city',
            billing_zip: 'shipping_zipcode',
            billing_state: 'shipping_state'
        };

        for (let billingId in mappings) {
            const shippingName = mappings[billingId];
            const billingInput = document.querySelector(`[name="${billingId}"]`);
            const shippingInput = document.querySelector(`[name="${shippingName}"]`);

            if (billingInput && shippingInput) {
                shippingInput.value = billingInput.value;
            }
        }
    }

    // Function to toggle shipping section
    function toggleShippingSection() {
        if (checkbox.checked) {
            shippingSection.style.display = 'block';
            copyBillingToShipping();
        } else {
            shippingSection.style.display = 'none';
        }
    }

    if (checkbox && shippingSection) {
        checkbox.addEventListener('change', toggleShippingSection);
        toggleShippingSection(); // Initial state on page load
    }
	
	getStateList('{{$defaultSetting->country}}');
});
</script>
<script>
   
    function getStateList(countryName) {
        if (countryName) {
			 const getStatesBaseUrl = "{{ url('/checkout/get-states') }}/";
            $.ajax({
                url: getStatesBaseUrl + countryName,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#billing_state').empty().append('<option value="">-Select-</option>');
                    $('#shipping_state').empty().append('<option value="">-Select-</option>');
                    $.each(data, function(key, value) {
                        $('#billing_state').append('<option value="' + value.name + '">' + value.name + '</option>');
                        $('#shipping_state').append('<option value="' + value.name + '">' + value.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching states:', error);
                }
            });
        } else {
            $('#billing_state').empty().append('<option value="">-Select-</option>');
            $('#shipping_state').empty().append('<option value="">-Select-</option>');
        }
    }
	
	function getShippingStateList(countryName) {
        if (countryName) {
			 const getStatesBaseUrl = "{{ url('/checkout/get-states') }}/";
            $.ajax({
                url: getStatesBaseUrl + countryName,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#shipping_state').empty().append('<option value="">-Select-</option>');
                    $.each(data, function(key, value) {
                        $('#shipping_state').append('<option value="' + value.name + '">' + value.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching states:', error);
                }
            });
        } else {
            $('#shipping_state').empty().append('<option value="">-Select-</option>');
        }
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () { 
    $('#ajax-login-form').submit(function (e) { 
        e.preventDefault(); 

        const form = $(this);
        const url = "{{ route('login') }}";
        const token = $('input[name="_token"]').val();

        form.find('.invalid-feedback').remove();
        form.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (response) {

                window.location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<span class="invalid-feedback d-block" role="alert"><strong style="color: red;">${messages[0]}</strong></span>`);
                    });
                } else {
                    alert('Login failed. Please try again.');
                }
            }
        });
    });
});

</script>


@endsection
        