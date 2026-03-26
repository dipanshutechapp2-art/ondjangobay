	<style>
	


	</style>
	
	<div class="footer-top">
		<div class="row">
			<div class="col-lg-4 col-sm-6">
				<div class="widget widget-about mt-0 mb-4">
					<a href="{{url('/')}}" class="logo-footer">
						<img src="{{ asset('uploads/setting') }}/{{ $setting->footer_logo }}" alt="logo-footer"
							width="230">
					</a>
					<div class="widget-body">
						<p class="widget-about-title">Got Question? Call us 24/7</p>
						<a href="tel:{{ $setting->primary_phone }}"
							class="widget-about-call">{{ $setting->primary_phone }}</a>
						<strong style="font-size:14px;"> <a href="mailto: info@ondjango.co.ao"> info@ondjango.co.ao</a></strong>
						<p class="widget-about-desc">
							Register now to get updates on pronot get up icons & coupons ster now toon.
						</p>
						<div class="social-icons social-icons-colored">
							<a href="{{url('https://www.facebook.com/profile.php?id=61581647353200')}}"
								class="social-icon social-facebook w-icon-facebook" target="_blank"></a>
							<a href="{{url('https://www.tiktok.com/@ondjango.bay?_r=1&_t=ZS-93G5iV7zdgN')}}"
								class="social-icon tiktok-icon" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
									<path
										d="M19.589 6.686a4.793 4.793 0 01-3.77-1.874V16.03a5.002 5.002 0 11-4.163-4.932v2.023a3 3 0 102.163 2.878V2h2.02a4.79 4.79 0 003.75 2.867z" />
								</svg>
							</a>
							<a href="{{url('https://www.instagram.com/ondjangobay/')}}"
								class="social-icon social-instagram w-icon-instagram" target="_blank"></a>
							<a href="{{url('/')}}" class="social-icon social-twitter w-icon-twitter"
								target="_blank"></a>
							<a href="{{url('/')}}" class="social-icon linkedin-icon" target="_blank">

								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
									<path d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 
										2.76 2.24 5 5 5h14c2.76 0 5-2.24 
										5-5v-14c0-2.76-2.24-5-5-5zm-11 
										19h-3v-10h3v10zm-1.5-11.27c-.97 
										0-1.75-.79-1.75-1.76s.78-1.76 
										1.75-1.76 1.75.79 1.75 1.76-.78 
										1.76-1.75 1.76zm13.5 
										11.27h-3v-5.6c0-1.34-.03-3.07-1.87-3.07-1.87 
										0-2.16 1.46-2.16 2.97v5.7h-3v-10h2.88v1.37h.04c.4-.75 
										1.38-1.54 2.84-1.54 3.04 0 3.6 2 3.6 4.59v5.58z" />
								</svg>

							</a>
							<!-- <a href="{{url('/')}}" class="social-icon social-youtube w-icon-youtube"></a> -->
							<a href="{{url('/')}}" class="social-icon social-pinterest w-icon-pinterest"></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6">
				<div class="widget">
					<h3 class="widget-title">Company</h3>
					<ul class="widget-body">
						<li><a href="#">Ondjango Guide</a></li>
						<li><a href="#">Selling on Ondjango </a></li>
						<li><a href="#">Sustainability & Supply Chain</a></li>
						<!--<li><a href="{{url('/affilate')}}">Affilate</a></li>-->
					</ul>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6">
				<div class="widget">
					<h4 class="widget-title">Customer Service</h4>
					<ul class="widget-body">
						<!--<li><a href="{{url('/track-order')}}">Track My Order</a></li>-->
						<li><a href="{{url('/contact-us')}}">Contact Us</a></li>
						<li><a href="{{url('/faq')}}">Faq</a></li>
						<li><a href="#">Shipping & Delivery Times</a></li>

						<li><a href="{{url('/user/login')}}">Returns & Refunds</a></li>
						<!--<li><a href="{{url('/help')}}">Help</a></li>-->
						<li><a href="{{url('/account/wishlist')}}">Coupons & Promotions </a></li>
						<li><a href="{{url('/cart')}}">View Cart</a></li>
					</ul>
				</div>
			</div>
			<div class="col-lg-3 col-sm-6">
				<div class="widget">
					<h4 class="widget-title">Payments and Security</h4>
					<ul class="widget-body">
						<li><a href="#">Payment Methods </a></li>
						<li><a href="#">Terms and Conditions </a></li>
						<li><a href="#">Security Notices </a></li>


						<!-- <li><a href="{{url('/privacy-policy')}}">Privacy Policy</a></li> -->

						<!-- <li><a href="{{url('/')}}">Payment Methods</a></li> -->
						<!--<li><a href="{{url('/')}}">Money-back guarantee!</a></li>-->
						<!-- <li><a href="{{url('/')}}">Product Returns</a></li> -->
						<!-- <li><a href="{{url('/support-center')}}">Support Center</a></li> -->
						<!-- <li><a href="{{url('/')}}">Shipping</a></li>-->
						<!-- <li><a href="{{url('/term-conditions')}}">Term and Conditions</a></li> -->
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="footer-left">
			<p class="copyright">{{ $setting->copyright }}</p>
		</div>
		<div class="footer-right">
			<span class="payment-label mr-lg-8">We're using safe payment for</span>
			<figure class="payment">
				<img src="{{asset('frontend/assets/payment.jpg')}}" alt="payment" width="220">
			</figure>
		</div>
	</div>

	<a href="https://wa.me/+351 910 256 808"
   class="wa-float"
   target="_blank"
   aria-label="Chat on WhatsApp">

    <svg xmlns="http://www.w3.org/2000/svg"
         width="28"
         height="28"
         viewBox="0 0 24 24"
         fill="white">
        <path d="M20.52 3.48A11.79 11.79 0 0012.01 0C5.38 0 .02 5.36.02 11.99c0 2.11.55 4.17 1.59 5.98L0 24l6.19-1.62a11.9 11.9 0 005.82 1.48h.01c6.63 0 11.99-5.36 11.99-11.99 0-3.2-1.25-6.21-3.49-8.39zM12.02 21.5c-1.78 0-3.53-.48-5.05-1.39l-.36-.21-3.67.96.98-3.58-.23-.37a9.47 9.47 0 01-1.45-5.02c0-5.24 4.27-9.51 9.52-9.51 2.54 0 4.92.99 6.72 2.79a9.45 9.45 0 012.79 6.72c0 5.25-4.27 9.51-9.51 9.51zm5.21-7.14c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.19.29-.76.95-.93 1.15-.17.19-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.44-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.44.13-.58.13-.13.29-.34.44-.51.15-.17.2-.29.29-.49.1-.19.05-.37-.02-.51-.07-.15-.66-1.6-.9-2.19-.24-.58-.48-.5-.66-.51-.17-.01-.37-.01-.56-.01-.19 0-.51.07-.78.37-.27.29-1.02 1-1.02 2.44 0 1.44 1.04 2.83 1.18 3.03.15.19 2.04 3.11 4.95 4.36.69.3 1.23.48 1.65.61.69.22 1.32.19 1.82.12.56-.08 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.12-.27-.19-.56-.34z"/>
    </svg>

</a>



