@extends('layouts.app_inner')
@section('title', 'Cart')
@section('content')
<main class="main cart">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb shop-breadcrumb bb-no">
                <li class="active"><a href="{{ url('/cart') }}">Shopping Cart</a></li>
                <li><a href="{{ url('/checkout') }}">Checkout</a></li>
                <li><a href="{{ url('/order-complete') }}">Order Complete</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="row gutter-lg mb-10">
                <div class="col-lg-8 pr-lg-4 mb-6">
				    @if(session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif
					@if(session('error'))
						<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
                    <table class="shop-table cart-table">
                        <thead>
                            <tr>
                                <th class="product-name"><span>Product</span></th>
                                <th></th>
                                <th class="product-price"><span>Price</span></th>
                                <th class="product-quantity"><span>Quantity</span></th>
                                <th class="product-subtotal"><span>Subtotal</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp

                            @forelse($cartItems as $item)
                                @php 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td class="product-thumbnail">
                                        <div class="p-relative">
                                            <a href="{{ url('/product/' . $item['slug']) }}">
                                                <figure>
                                                    <img src="{{ $item['image'] }}" alt="product" width="300" height="338">
                                                </figure>
                                            </a>
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                <button type="submit" class="btn btn-close"><i class="fas fa-times"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="product-name">
                                        <a href="{{ url('/product/' . $item['slug']) }}">{{ $item['name'] }}</a>
                                    </td>
                                    <td class="product-price">
                                        <span class="amount">${{ number_format($item['price'], 2) }}</span>
                                    </td>
                                    <td class="product-quantity">
                                        <form method="POST" action="{{ route('cart.update') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                            <div class="input-group">
                                                <input class="quantity form-control" type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="1000">
                                                <button type="submit" class="btn btn-sm btn-primary ml-2">Update</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="product-subtotal">
                                        <span class="amount">${{ number_format($subtotal, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Your cart is empty.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="cart-action mb-6 d-flex align-items-center justify-content-between">
                        <a href="{{ url('/shop') }}" class="btn btn-dark btn-rounded btn-icon-left btn-shopping"><i class="w-icon-long-arrow-left"></i>Continue Shopping</a>
                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-rounded btn-default btn-clear">Clear Cart</button>
                        </form>
                    </div>

                    <!--<form class="coupon">
                        <h5 class="title coupon-title font-weight-bold text-uppercase">Coupon Discount</h5>
                        <input type="text" class="form-control mb-4" placeholder="Enter coupon code here..." required>
                        <button class="btn btn-dark btn-outline btn-rounded">Apply Coupon</button>
                    </form>-->
                </div>

                <div class="col-lg-4 sticky-sidebar-wrapper">
                    <div class="sticky-sidebar">
                        <div class="cart-summary mb-4">
                            <h3 class="cart-title text-uppercase">Cart Totals</h3>
                            <div class="cart-subtotal d-flex align-items-center justify-content-between">
                                <label class="ls-25">Subtotal</label>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>

                             <!--<hr class="divider">

                           <ul class="shipping-methods mb-2">
                                <li><label class="shipping-title text-dark font-weight-bold">Shipping</label></li>
                                <li>
                                    <div class="custom-radio">
                                        <input type="radio" id="free-shipping" name="shipping" class="custom-control-input" checked>
                                        <label for="free-shipping" class="custom-control-label color-dark">Free Shipping</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-radio">
                                        <input type="radio" id="flat-rate" name="shipping" class="custom-control-input">
                                        <label for="flat-rate" class="custom-control-label color-dark">Flat rate: $5.00</label>
                                    </div>
                                </li>
                            </ul>-->

                            <hr class="divider mb-6">

                            <div class="order-total d-flex justify-content-between align-items-center">
                                <label>Total</label>
                                <span class="ls-50">${{ number_format($total, 2) }}</span>
                            </div>

                            <a href="{{ url('/checkout') }}" class="btn btn-block btn-dark btn-icon-right btn-rounded btn-checkout">
                                Proceed to checkout<i class="w-icon-long-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of PageContent -->
</main>
@endsection
