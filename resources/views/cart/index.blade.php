@extends('layouts.app_inner')
@section('title', 'Cart')
@section('content')
<style>
	@media (max-width: 425px){

    .cart-action-test{
        flex-direction: column;
        align-items: stretch !important;
        gap: 10px;
    }

    .cart-action-test form,
    .cart-action-test .btn-shopping,
    .cart-action-test .btn-clear{
        width: 100%;
    }

    .cart-action-test .btn{
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

</style>
<main class="main cart">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb shop-breadcrumb bb-no">
                <li class="active"><a href="{{url('/cart')}}">Shopping Cart</a></li>
                <li>Checkout</li>
                <li>Order Complete</li>
            </ul>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="row gutter-lg mb-10">
                <div class="col-lg-8 pr-lg-4 mb-6">
                    @if(session('success'))
                    <div class="alert alert-success alert-simple alert-inline show-code-action">{{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-error alert-simple alert-inline show-code-action">
                        {{ session('error') }}
                    </div>
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
                                            <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                            <button type="submit" class="btn btn-close"><i
                                                    class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                </td>
                                <td class="product-name">
                                    <a href="{{ url('/product/' . $item['slug']) }}">{{ $item['name'] }}</a>
                                    @if (!empty($item['variant_text']))
                                    <div class="text-muted small" style="font-size: 11px;font-weight: bold;">
                                        {{$item['variant_text']}}</div>
                                    @endif
                                    @if (!empty($item['vendor']))
                                    <p class="text-muted small" style="font-size: 11px;"><b>Vendor:</b>
                                        {{$item['vendor']}} <br /><b>Orgin:</b> {{$item['origin']}}</p>
                                    @endif

                                </td>
                                <td class="product-price">
                                    <span class="amount">{{formatCurrency($item['price'])}}</span>
                                </td>
                                <td class="product-quantity">
                                    <form method="POST" action="{{ route('cart.updateCart') }}"
                                        class="quantity-form d-flex align-items-center">
                                        @csrf
                                        <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                        <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">

                                        <div class="update-wrapper">
                                            <div class="input-group mb-1">
                                                <input class="form-control quantity-input" type="number" name="quantity"
                                                    value="{{ $item['quantity'] }}" min="1" max="100000">
                                                <button class="quantity-plus w-icon-plus"
                                                    onclick="increaseQty(this); return false;"></button>
                                                <button class="quantity-minus w-icon-minus"
                                                    onclick="decreaseQty(this); return false;"></button>
                                            </div>
                                            <button type="submit" class="btn  btn-primary update-btn">Update</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="product-subtotal">
                                    <span class="amount">{{formatCurrency($subtotal)}}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Your cart is empty.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <br />
                    <div class="cart-action-test mb-6 d-flex align-items-center justify-content-between">

                        <a href="{{ url('/shop') }}" class="btn btn-dark btn-rounded btn-icon-left btn-shopping">
                            <i class="w-icon-long-arrow-left"></i> Continue Shopping
                        </a>

                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-rounded btn-default btn-clear">
                                Clear Cart
                            </button>
                        </form>

                    </div>


                    @if(session('coupon'))
                    <p>Coupon Applied: <strong>({{ session('coupon.code') }})</strong> <a
                            href="{{url('/cart/removeCoupon')}}" style="color:red;"
                            onclick="return validateCouponDelete(this);">Remove</a></p>
                    <p>Discount: {{formatCurrency(session('coupon.discount'))}}</p>
                    @endif
                    <form action="{{ route('cart.applyCoupon') }}" class="coupon11" method="POST">
                        @csrf
                        <h5 class="title coupon-title font-weight-bold text-uppercase">Coupon Discount</h5>
                        <input type="text" name="code" class="form-control mb-4" placeholder="Enter coupon code here..."
                            required>
                        <button type="submit" class="btn btn-dark btn-outline btn-rounded">Apply Coupon</button>
                    </form>

                    @if($vendorCoupons->count())
                    <br />
                    <div class="available-offers mt-5">
                        <h5 class="title coupon-title font-weight-bold text-uppercase"><i
                                class="w-icon-gift mr-2 text-primary"></i>Available Offers</h5>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th style="width: 120px;">Coupon Code</th>
                                        <!--<th>Description</th>-->
                                        <!--<th style="width: 100px;">Type</th>-->
                                        <th style="width: 120px;">Value</th>
                                        <!--<th style="width: 150px;">Valid Till</th>-->
                                        <th style="width: 100px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendorCoupons as $coupon)
                                    <tr style="text-align:center;">
                                        <td><strong>{{ strtoupper($coupon->code) }}</strong></td>
                                        <!--<td>{{ $coupon->description ?? 'Use this offer for your next order!' }}</td>
												<td>{{ ucfirst($coupon->type) }}</td>-->
                                        <td>
                                            @if($coupon->type == 'percentage')
                                            {{ $coupon->value }}%
                                            @else
                                            {{ formatCurrency($coupon->value) }}
                                            @endif
                                        </td>
                                        <!--<td>
													@if($coupon->expires_at)
														{{ \Carbon\Carbon::parse($coupon->expires_at)->format('d M Y') }}
													@else
														<span class="text-muted">No expiry</span>
													@endif
												</td>-->
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-dark copy-btn rounded-pill"
                                                data-code="{{ $coupon->code }}">
                                                Copy Code
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    <script>
                    document.querySelectorAll('.copy-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const code = this.dataset.code;
                            navigator.clipboard.writeText(code);
                            this.textContent = "Copied!";
                            this.classList.remove('btn-outline-dark');
                            this.classList.add('btn-success');
                            setTimeout(() => {
                                this.textContent = "Copy Code";
                                this.classList.add('btn-outline-dark');
                                this.classList.remove('btn-success');
                            }, 1500);
                        });
                    });
                    </script>
                </div>

                <div class="col-lg-4 sticky-sidebar-wrapper">
                    <div class="sticky-sidebar">
                        <div class="cart-summary mb-4">
                            <h3 class="cart-title text-uppercase">Cart Totals</h3>
                            <div class="cart-subtotal d-flex align-items-center justify-content-between">
                                <label class="ls-25">Subtotal</label>
                                <span>{{formatCurrency($total)}}</span>
                            </div>

                            <hr class="divider">

                            <ul class="shipping-methods mb-2">
                                <li>
                                    <label class="shipping-title text-dark font-weight-bold">
                                        Shipping Options
                                    </label>
                                </li>

                                @foreach($shippingOptions as $shipping)
                                <li>
                                    <div class="custom-radio">
                                        <input type="radio" id="shipping_{{ $shipping->id }}" name="shipping"
                                            class="custom-control-input shipping-radio" value="{{ $shipping->id }}"
                                            {{ session('shipping_price_id') == $shipping->id ? 'checked' : '' }}>

                                        <label for="shipping_{{ $shipping->id }}"
                                            class="custom-control-label color-dark">
                                            {{ $shipping->option->title }}
                                            —
                                            {{ formatCurrency($shipping->price) }},
                                            {{ $shipping->eta_min }}–{{ $shipping->eta_max }} days,
                                            {{ $shipping->option->description }}
                                        </label>
                                    </div>
                                </li>
                                @endforeach

                            </ul>


                            @if(session('coupon') && $cartItems)
                            <hr class="divider">
                            <span><b>Coupon Applied:</b> ({{ session('coupon.code') }}) <a
                                    href="{{url('/cart/removeCoupon')}}" style="color:red;"
                                    onclick="return validateCouponDelete(this);">Remove</a></span>
                            @endif
                            <hr class="divider mb-6">
                            @if(session('coupon') && $cartItems)
                            <div class="order-total d-flex justify-content-between align-items-center">
                                <label>Coupon Discount</label>
                                <span class="ls-50">{{formatCurrency(session('coupon.discount'))}}</span>
                            </div>
                            <hr class="divider mb-6">
                            @endif

                            <div class="order-total d-flex justify-content-between align-items-center">
                                <label>Shipping Cost</label>
                                @if(!empty($cartItems))
                                <span class="ls-50">{{formatCurrency($shippingPrice)}}</span>
                                @else
                                <span class="ls-50">{{formatCurrency(0)}}</span>
                                @endif
                            </div>
                            <hr class="divider mb-6">
                            <div class="order-total d-flex justify-content-between align-items-center">
                                <label>Total</label>

                                @if(session('coupon') && $cartItems)
                                <span
                                    class="ls-50">{{formatCurrency(($total+$shippingPrice)-session('coupon.discount'))}}</span>
                                @else
                                @if(!empty($cartItems))
                                <span class="ls-50">{{formatCurrency($total+$shippingPrice)}}</span>
                                @else
                                <span class="ls-50">{{formatCurrency(0)}}</span>
                                @endif
                                @endif
                            </div>

                            <a href="{{ url('/checkout') }}"
                                class="btn btn-block btn-dark btn-icon-right btn-rounded btn-checkout">
                                Proceed to checkout<i class="w-icon-long-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End of PageContent -->
</main>

<form method="POST" action="{{ route('checkout.shipping.select') }}" id="shipping-form">
    @csrf
    <input type="hidden" name="shipping_price_id" id="shipping_price_id">
</form>

<script>
function increaseQty(btn) {
    let input = btn.parentElement.querySelector(".quantity-input");
    let value = parseInt(input.value) || 0;
    let max = parseInt(input.max) || 100000;
    if (value < max) input.value = value + 1;
}

function decreaseQty(btn) {
    let input = btn.parentElement.querySelector(".quantity-input");
    let value = parseInt(input.value) || 0;
    let min = parseInt(input.min) || 1;
    if (value > min) input.value = value - 1;
}

function validateCouponDelete(msg) {
    var confrm = confirm('Do you want to remove?.');
    if (confrm == false) {
        return false;
    }
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.shipping-radio');
    const form = document.getElementById('shipping-form');
    const hidden = document.getElementById('shipping_price_id');

    radios.forEach(radio => {
        radio.addEventListener('change', function(e) {
            e.stopPropagation(); // 🔒 IMPORTANT
            hidden.value = this.value;
            form.submit();
        });
    });
});
</script>

@endsection