@extends('layouts.app_inner')
@section('title', 'Checkout')
@section('content')
    <!-- Start of Main -->
    <hr />
    <main class="main checkout">
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb shop-breadcrumb bb-no">
                    <li class="passed"><a href="{{ url('/my-community-deal') }}">My Community Deal</a></li>
                    <li class="active"><a>Checkout</a></li>
                    <li>Order Complete</li>
                </ul>
            </div>
        </nav>
        <hr />
        <!-- End of Breadcrumb -->

        <!-- Start of PageContent -->
        <div class="page-content">
            <div class="container">
                @if (empty(auth()->id()))
                    <div class="login-toggle">
                        Returning customer?
                        <a href="#" class="show-login font-weight-bold text-uppercase text-dark">Login</a>
                    </div>
                    <form class="login-content" id="ajax-login-form" method="POST"
                        style="display: none; overflow: hidden;">
                        @csrf
                        <p>
                            If you have shopped with us before, please enter your details below.
                            If you are a new customer, please proceed to the Billing section.
                        </p>

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="email">Username or Email *</label>
                                    <input type="email"
                                        class="form-control form-control-md @error('email') is-invalid @enderror"
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
                                    <input type="password"
                                        class="form-control form-control-md @error('password') is-invalid @enderror"
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
                            <input type="checkbox" class="custom-checkbox" id="remember" name="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="mb-0 lh-2">Remember me</label>
                            <a href="{{ route('user.password.request') }}" class="ml-3">Forgot your password?</a>
                        </div>
                        <button type="submit" class="btn btn-rounded btn-login">Login</button>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const loginToggle = document.querySelector('.show-login');
                            const loginForm = document.querySelector('.login-content');

                            if (loginToggle && loginForm) {
                                loginToggle.addEventListener('click', function(e) {
                                    e.preventDefault();

                                    if (loginForm.style.display === 'none') {
                                        loginForm.style.display = 'block';
                                        loginForm.style.height = '0';
                                        loginForm.style.transition = 'all 0.3s ease';
                                        setTimeout(() => {
                                            loginForm.style.height = loginForm.scrollHeight + 'px';
                                        }, 10);
                                        loginToggle.textContent = 'Hide Login';
                                    } else {
                                        loginForm.style.height = '0';
                                        setTimeout(() => {
                                            loginForm.style.display = 'none';
                                        }, 300);
                                        loginToggle.textContent = 'Login';
                                    }
                                });
                            }
                        });
                    </script>
                @endif

                @if (session('success'))
                    <div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
                        {{ session('success') }}
                    </div><br />
                @endif

                @if (session('error'))
                    <div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
                        {{ session('error') }}
                    </div><br />
                @endif

                <!-- Billing & Shipping Form -->
                <form class="form checkout-form" action="{{ route('partner.checkout.process', $product->id) }}"
                    method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" name="quantity" value="{{ $quantity }}">
                    <input type="hidden" name="selected_address_type" id="selected_address_type" value="{{ count($addresses) > 0 ? 'existing' : 'new' }}">

                    <div class="row mb-9">
                        <div class="col-lg-7 pr-lg-4 mb-4">
                            <h3 class="title billing-title text-uppercase ls-10 pt-1 pb-3 mb-0">
                                Billing & Shipping Details
                            </h3>

                            @if (count($addresses) > 0)
                                <div class="address-selection-section">
                                    <h4>Select an Address</h4>
                                    <hr />
                                    <div class="row gutter-sm">
                                        @foreach ($addresses as $addressList)
                                            <div class="col-sm-6 mb-3">
                                                <div class="ecommerce-address billing-address pr-lg-8 border p-3 rounded">
                                                    <address class="mb-2">
                                                        <label class="d-block mb-3">
                                                            @if ($addressList->is_default == '1')
                                                                <input type="radio" name="address_id"
                                                                    value="{{ $addressList->id }}" required 
                                                                    class="address-radio" 
                                                                    data-address-type="existing"
                                                                    checked>
                                                                <strong>Default Address</strong>
                                                            @else
                                                                <input type="radio" name="address_id"
                                                                    value="{{ $addressList->id }}" required
                                                                    class="address-radio"
                                                                    data-address-type="existing">
                                                                Select this address
                                                            @endif
                                                        </label>
                                                        <table class="address-table w-100">
                                                            <tbody>
                                                                <tr>
                                                                    <th width="30%">Name:</th>
                                                                    <td>{{ $addressList->first_name ?? '' }} {{ $addressList->last_name ?? '' }}</td>
                                                                </tr>
                                                                @if($addressList->company)
                                                                <tr>
                                                                    <th>Company:</th>
                                                                    <td>{{ $addressList->company }}</td>
                                                                </tr>
                                                                @endif
                                                                <tr>
                                                                    <th>Address:</th>
                                                                    <td>{{ $addressList->address_1 }} 
                                                                        @if($addressList->address_2)
                                                                        , {{ $addressList->address_2 }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>City:</th>
                                                                    <td>{{ $addressList->city ?? '' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Country:</th>
                                                                    <td>{{ $addressList->country ?? '' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>State:</th>
                                                                    <td>{{ $addressList->state ?? '' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Postcode:</th>
                                                                    <td>{{ $addressList->zipcode ?? '' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Phone:</th>
                                                                    <td>{{ $addressList->phone ?? '' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </address>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="col-12 mt-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="use_new" id="use_new_address"
                                                       data-address-type="new">
                                                <label class="form-check-label" for="use_new_address">
                                                    <strong>Add new address</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mt-4">
                                </div>
                            @endif

                            <div id="new-address-form" class="{{ count($addresses) > 0 ? 'd-none' : '' }}">
                                <div class="row gutter-sm">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>First name *</label>
                                            <input type="text" class="form-control form-control-md new-address-field"
                                                id="billing_first_name" name="billing_first_name"
                                                placeholder="First name"
                                                value="{{ auth()->user()->name ?? old('billing_first_name') }}"
                                                {{ count($addresses) > 0 ? '' : 'required' }}>
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
                                            <input type="text" class="form-control form-control-md new-address-field" id="inp-name"
                                                name="billing_last_name" placeholder="Last name"
                                                value="{{ auth()->user()->last_name ?? old('billing_last_name') }}"
                                                {{ count($addresses) > 0 ? '' : 'required' }}>
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
                                    <input type="text" class="form-control form-control-md new-address-field" name="billing_company_name"
                                        placeholder="Company name" value="{{ old('billing_company_name') }}">
                                    @error('billing_company_name')
                                        <span class="invalid-feedback" role="alert" style="color:red;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Country / Region *</label>
                                    <div class="select-box">
                                        <select name="billing_country" id="billing_country"
                                            onchange="getStateList(this.value)" class="form-control form-control-md new-address-field"
                                            {{ count($addresses) > 0 ? '' : 'required' }}>
                                            <option value="">-Select-</option>
                                            @if (!empty($country))
                                                @foreach ($country as $countryList)
                                                    @if ($countryList->name == $defaultSetting->country)
                                                        <option value="{{ $countryList->name }}" selected>
                                                            {{ $countryList->name }}</option>
                                                    @else
                                                        <option value="{{ $countryList->name }}">{{ $countryList->name }}
                                                        </option>
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
                                    <input type="text" placeholder="House number and street name"
                                        class="form-control form-control-md mb-2 new-address-field"
                                        value="{{ old('billing_address_1') }}" name="billing_address_1" 
                                        {{ count($addresses) > 0 ? '' : 'required' }}>
                                    @error('billing_address_1')
                                        <span class="invalid-feedback" role="alert" style="color:red;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <input type="text" placeholder="Apartment, suite, unit, etc. (optional)"
                                        class="form-control form-control-md new-address-field" value="{{ old('billing_address_2') }}"
                                        name="billing_address_2">
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
                                            <input type="text" class="form-control form-control-md new-address-field" name="billing_city"
                                                value="{{ old('billing_city') }}" 
                                                {{ count($addresses) > 0 ? '' : 'required' }}>
                                            @error('billing_city')
                                                <span class="invalid-feedback" role="alert" style="color:red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Zipcode </label>
                                            <input type="text" class="form-control form-control-md new-address-field" name="billing_zip"
                                                value="{{ old('billing_zip') }}">
                                            @error('billing_zip')
                                                <span class="invalid-feedback" role="alert" style="color:red;">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>State *</label>
                                            <div class="select-box">
                                                <select name="billing_state" id="billing_state"
                                                    class="form-control form-control-md new-address-field" 
                                                    {{ count($addresses) > 0 ? '' : 'required' }}>
                                                    <option value="">-Select-</option>
                                                </select>
                                                @error('billing_state')
                                                    <span class="invalid-feedback" role="alert" style="color:red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone *</label>
                                            <input type="text" class="form-control form-control-md new-address-field" name="billing_phone"
                                                value="{{ auth()->user()->phone ?? old('billing_phone') }}" 
                                                {{ count($addresses) > 0 ? '' : 'required' }}>
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
                                    <input type="email" class="form-control form-control-md new-address-field" name="billing_email"
                                        value="{{ auth()->user()->email ?? old('billing_email') }}" 
                                        {{ count($addresses) > 0 ? '' : 'required' }}>
                                    @error('billing_email')
                                        <span class="invalid-feedback" role="alert" style="color:red;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group pb-2">
                                    <input type="checkbox" class="custom-checkbox" id="toggle-shipping-address"
                                        name="shipping_toggle_value" value="1">
                                    <label for="toggle-shipping-address">Ship to a different address?</label>
                                </div>

                                <div class="shipping-address-section" style="display: none;">
                                    <h5 class="mb-3">Shipping Address</h5>
                                    <div class="row gutter-sm">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label>First name *</label>
                                                <input type="text" class="form-control form-control-md new-address-field"
                                                    value="{{ auth()->user()->name ?? old('shipping_first_name') }}"
                                                    name="shipping_first_name">
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
                                                <input type="text" class="form-control form-control-md new-address-field"
                                                    name="shipping_last_name"
                                                    value="{{ auth()->user()->last_name ?? old('shipping_last_name') }}">
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
                                        <input type="text" class="form-control form-control-md new-address-field"
                                            name="shipping_company_name">
                                        @error('shipping_company_name')
                                            <span class="invalid-feedback" role="alert" style="color:red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Country / Region *</label>
                                        <div class="select-box">
                                            <select name="shipping_country" onchange="getShippingStateList(this.value)"
                                                class="form-control form-control-md new-address-field">
                                                <option value="">-select-</option>
                                                @if (!empty($country))
                                                    @foreach ($country as $countryList)
                                                        @if ($countryList->name == $defaultSetting->country)
                                                            <option value="{{ $countryList->name }}" selected>
                                                                {{ $countryList->name }}</option>
                                                        @else
                                                            <option value="{{ $countryList->name }}">
                                                                {{ $countryList->name }}</option>
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
                                        <input type="text" placeholder="House number and street name"
                                            class="form-control form-control-md mb-2 new-address-field" name="shipping_street_address_1">
                                        @error('shipping_street_address_1')
                                            <span class="invalid-feedback" role="alert" style="color:red;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <input type="text" placeholder="Apartment, suite, unit, etc. (optional)"
                                            class="form-control form-control-md new-address-field" name="shipping_street_address_2">
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
                                                <input type="text" class="form-control form-control-md new-address-field"
                                                    name="shipping_city">
                                                @error('shipping_city')
                                                    <span class="invalid-feedback" role="alert" style="color:red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Zipcode </label>
                                                <input type="text" class="form-control form-control-md new-address-field"
                                                    name="shipping_zipcode">
                                                @error('shipping_zipcode')
                                                    <span class="invalid-feedback" role="alert" style="color:red;">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>State</label>
                                                <select name="shipping_state" id="shipping_state"
                                                    class="form-control form-control-md new-address-field">
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
                                    <textarea class="form-control mb-0" id="order-notes" name="order_notes" cols="30" rows="4"
                                        placeholder="Notes about your order, e.g special notes for delivery"></textarea>
                                    @error('order_notes')
                                        <span class="invalid-feedback" role="alert" style="color:red;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary Section -->
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
                                            <tr class="bb-no">
                                                <td class="product-name">
                                                    {{ $product->name }} <i class="fas fa-times"></i>
                                                    <span class="product-quantity">{{ $quantity }}</span>
                                                </td>
                                                <td class="product-total">{{ formatCurrency($total) }}</td>
                                            </tr>
                                            <tr class="cart-subtotal bb-no">
                                                <td><b>Subtotal</b></td>
                                                <td><b>{{ formatCurrency($total) }}</b></td>
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
                                                                <input type="radio" id="free-shipping"
                                                                    class="custom-control-input" name="shipping" checked>
                                                                <label for="free-shipping"
                                                                    class="custom-control-label color-dark">Free
                                                                    Shipping</label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr class="order-total">
                                                <th><b>Total</b></th>
                                                <td><b>{{ formatCurrency($total) }}</b></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="payment-methods" id="payment_method">
                                        <h4 class="title font-weight-bold ls-25 pb-0 mb-1">Payment Methods</h4><br />
                                        <div class="accordion payment-accordion">
                                            @foreach ($paymentGatewayList as $paymentList)
                                                @php
                                                    $input_id = 'payment_' . strtolower($paymentList->name);
                                                    $container_id = 'section_' . strtolower($paymentList->name);
                                                @endphp
                                                <div class="card">
                                                    <input type="radio" id="{{ $input_id }}" name="payment_method"
                                                        value="{{ strtolower($paymentList->name) }}"
                                                        @if ($paymentList->is_default) checked @endif>
                                                    <label for="{{ $input_id }}">{{ $paymentList->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div id="payment-sections">
                                        @foreach ($paymentGatewayList as $paymentList)
                                            @php $container_id = 'section_'.strtolower($paymentList->name); @endphp
                                            <div id="{{ $container_id }}" class="payment-section d-none">
                                                @if (strtolower($paymentList->name) === 'cod')
                                                    <button type="submit" class="btn btn-dark btn-block btn-rounded">Place
                                                        Order</button>
                                                @elseif(strtolower($paymentList->name) === 'paypal')
                                                    <button type="submit" class="btn btn-primary btn-block btn-rounded">Pay
                                                        with PayPal</button>
                                                @elseif(strtolower($paymentList->name) === 'stripe')
                                                    <button type="submit" class="btn btn-success btn-block btn-rounded">Pay
                                                        with Stripe</button>
                                                @elseif(strtolower($paymentList->name) === 'wallet')
                                                    <button type="submit" class="btn btn-info btn-block btn-rounded">Pay with
                                                        Wallet</button>
                                                @else
                                                    <button type="submit"
                                                        class="btn btn-secondary btn-block btn-rounded">{{ $paymentList->name }}</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End of PageContent -->
    </main>
    <!-- End of Main -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                input.addEventListener("change", function() {
                    toggleSections(this.value);
                });
            });

            const defaultInput = document.querySelector("input[name='payment_method']:checked");
            if (defaultInput) {
                toggleSections(defaultInput.value);
            }
        });

        // Address Type Management
        document.addEventListener('DOMContentLoaded', function() {
            const newAddressCheckbox = document.getElementById('use_new_address');
            const newAddressForm = document.getElementById('new-address-form');
            const addressRadios = document.querySelectorAll('.address-radio');
            const newAddressFields = document.querySelectorAll('.new-address-field');
            const selectedAddressType = document.getElementById('selected_address_type');
            const checkoutForm = document.getElementById('checkoutForm');

            // Function to toggle required attributes
            function toggleNewAddressFields(required) {
                newAddressFields.forEach(field => {
                    if (field.type !== 'checkbox' && field.tagName !== 'SELECT' || field.hasAttribute('required')) {
                        if (required) {
                            field.setAttribute('required', 'required');
                        } else {
                            field.removeAttribute('required');
                        }
                    }
                });
            }

            // Function to handle address type selection
            function handleAddressTypeSelection(type) {
                selectedAddressType.value = type;
                
                if (type === 'new') {
                    newAddressForm.classList.remove('d-none');
                    toggleNewAddressFields(true);
                    // Uncheck all address radios
                    addressRadios.forEach(radio => radio.checked = false);
                } else {
                    newAddressForm.classList.add('d-none');
                    toggleNewAddressFields(false);
                    if (newAddressCheckbox) {
                        newAddressCheckbox.checked = false;
                    }
                }
            }

            // Handle new address checkbox
            if (newAddressCheckbox) {
                newAddressCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        handleAddressTypeSelection('new');
                    } else {
                        handleAddressTypeSelection('existing');
                        // Re-check the first address radio if available
                        if (addressRadios.length > 0) {
                            const defaultRadio = document.querySelector('.address-radio[checked]') || addressRadios[0];
                            defaultRadio.checked = true;
                        }
                    }
                });
            }

            // Handle address radio selection
            addressRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        handleAddressTypeSelection('existing');
                        if (newAddressCheckbox) {
                            newAddressCheckbox.checked = false;
                        }
                    }
                });
            });

            // Form submission validation
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const addressType = selectedAddressType.value;
                    
                    if (addressType === 'existing') {
                        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
                        if (!selectedAddress) {
                            e.preventDefault();
                            alert('Please select an address or choose to add a new address.');
                            return false;
                        }
                    } else if (addressType === 'new') {
                        // New address fields will be validated by HTML5 required attributes
                        // Additional custom validation can be added here if needed
                    }
                });
            }

            // Initialize based on current state
            if ({{ count($addresses) }} > 0) {
                // If addresses exist, start with existing address selected
                handleAddressTypeSelection('existing');
            } else {
                // If no addresses, show new address form and make fields required
                handleAddressTypeSelection('new');
                if (newAddressCheckbox) {
                    newAddressCheckbox.checked = true;
                }
            }
        });

        // Shipping address toggle
        document.addEventListener('DOMContentLoaded', function() {
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

                    if (billingInput && shippingInput && !shippingInput.value) {
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

            getStateList('{{ $defaultSetting->country }}');
        });

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
                            $('#billing_state').append('<option value="' + value.name + '">' + value
                                .name + '</option>');
                            $('#shipping_state').append('<option value="' + value.name + '">' + value
                                .name + '</option>');
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
                            $('#shipping_state').append('<option value="' + value.name + '">' + value
                                .name + '</option>');
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

        document.addEventListener('DOMContentLoaded', function() {
            $('#ajax-login-form').submit(function(e) {
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
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                const input = form.find(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<span class="invalid-feedback d-block" role="alert"><strong style="color: red;">${messages[0]}</strong></span>`
                                );
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