<div class="dropdown cart-dropdown cart-offcanvas mr-0 mr-lg-2">
		<div class="cart-overlay"></div>
		<a href="#" class="cart-toggle label-down link">
			<i class="w-icon-cart">
				<span class="cart-count">0</span>
			</i>
			<span class="cart-label">Cart</span>
		</a>
		<div class="dropdown-box">
			<div class="cart-header">
				<span>Shopping Cart</span>
				<a href="# class=" btn-close">Close<i class="w-icon-long-arrow-right"></i></a>
			</div>

			<div class="products">
				<div class="product product-cart">
					<div class="product-detail">
						<a href="{{url('/')}}" class="product-name">
							Beige knitted elas<br>
							tic runner shoes
						</a>
						<div class="price-box">
							<span class="product-quantity">1</span>
							<span class="product-price">$25.68</span>
						</div>
					</div>
					<figure class="product-media">
						<a href="{{url('/')}}">
							<img src="{{ asset('frontend/assets/images/cart/product-1-1.jpg')}}" alt="product" height="84" width="94">
						</a>
					</figure>
					<button class="btn btn-link btn-close">
						<i class="fas fa-times"></i>
					</button>
				</div>
				<div class="product product-cart">
				</div>
			</div>
			<div class="bottom-cart-">
				@if(session('coupon'))
				<div class="CouponDiscount">
					<label>Coupon Discount:</label>
					<span class="price">{{formatCurrency(session('coupon.discount'))}}</span>
					<a href="{{url('/cart/removeCoupon')}}" style="color:red;" onclick="return validateCouponDelete(this);">Remove</a>
				</div>
				@endif
				<div class="cart-total">
					<label>Sub Total:</label>
					<span class="price">{{getDefaultSelectedCurrency()}}0</span>
				</div>
				<div class="cart-action">
					<a href="{{url('/cart')}}" class="btn btn-dark btn-outline btn-rounded">
						View Cart
					</a>

					<a href="{{url('/checkout')}}"
						class="btn btn-primary btn-rounded px-4 py-2"
						style="white-space: normal; text-align: center;">
						Proceed to Purchase
					</a>
				</div>

			</div>
			
		</div>
		<!-- End of Dropdown Box -->
	</div>