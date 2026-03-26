	<div class="header-top">
		<div class="container">
			<div class="header-left">
				<p class="welcome-msg">Welcome to Wolmart Store message or remove it!</p>
			</div>
			<div class="header-right">
				<div class="dropdown">
					<a href="#currency">USD</a>
					<div class="dropdown-box">
						<a href="#USD">USD</a>
						<a href="#EUR">EUR</a>
					</div>
				</div>
				<!-- End of DropDown Menu -->

				<div class="dropdown">
					<a href="#language"><img src="{{ asset('frontend/assets/images/flags/eng-1.png')}}" alt="ENG Flag" width="14" height="8" class="dropdown-image"> ENG
					</a>
					<div class="dropdown-box">
						<a href="#ENG">
							<img src="{{ asset('frontend/assets/images/flags/eng-1.png')}}" alt="ENG Flag" width="14" height="8" class="dropdown-image">
							ENG
						</a>
						<a href="#FRA">
							<img src="{{ asset('frontend/assets/images/flags/fra-1.png')}}" alt="FRA Flag" width="14" height="8" class="dropdown-image">
							FRA
						</a>
					</div>
				</div>
				<!-- End of Dropdown Menu -->
				<span class="divider d-lg-show"></span>
				<a href="{{url('/blog')}}" class="d-lg-show">Blog</a>
				<a href="{{url('/become-a-vendor')}}" class="d-lg-show">Become a Vendor</a>
				<a href="{{url('/about-us')}}" class="d-lg-show">About Us</a>
				<a href="{{url('/help')}}" class="d-lg-show">
					<i class="w-icon-exclamation-circle"></i>
					Need Help
				</a>
			</div>
		</div>
	</div>