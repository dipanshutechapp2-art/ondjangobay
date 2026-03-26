<div class="sticky-footer sticky-content fix-bottom">
        <a href="{{url('/')}}" class="sticky-link active">
            <i class="w-icon-home"></i>
            <p>Home</p>
        </a>
        <a href="{{url('/shop')}}" class="sticky-link">
            <i class="w-icon-category"></i>
            <p>Shop</p>
        </a>
        @if(!empty(Auth::user()->role) && Auth::user()->role=='admin')
			<a href="{{url('/admin/dashboard')}}" class="sticky-link">
				<i class="w-icon-account"></i>
				<p>Account</p>
			</a>
		@elseif(!empty(Auth::user()->role) && Auth::user()->role=='vendor')
			<a href="{{url('/vendor/dashboard')}}" class="sticky-link">
				<i class="w-icon-account"></i>
				<p>Account</p>
			</a>
		@else
		    <a href="{{url('/my-account')}}" class="sticky-link">
				<i class="w-icon-account"></i>
				<p>Account</p>
			</a>
		@endif
        <div class="cart-dropdown dir-up">
            <a href="{{url('/cart')}}" class="sticky-link">
                <i class="w-icon-cart"></i>
                <p>Cart</p>
            </a>
        </div>
		
        <div class="header-search hs-toggle dir-up">
            <a class="search-toggle sticky-link">
                <i class="w-icon-search"></i>
                <p>Search</p>
            </a>
            <form action="{{url('/search')}}" method="get" class="input-wrapper">
               <input type="text" class="form-control" name="search" autocomplete="off" placeholder="Search">
                <button class="btn btn-search" type="submit">
                    <i class="w-icon-search"></i>
                </button>
            </form>
        </div>
    </div>
    <!-- End of Sticky Footer -->

    <!-- Start of Scroll Top -->
    <a id="scroll-top" class="scroll-top" href="#top" title="Top" role="button"> <i class="w-icon-angle-up"></i> <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 70 70"> <circle id="progress-indicator" fill="transparent" stroke="#000000" stroke-miterlimit="10" cx="35" cy="35" r="34" style="stroke-dasharray: 16.4198, 400;"></circle> </svg> </a>
    <!-- End of Scroll Top -->

    <!-- Start of Mobile Menu -->
    <div class="mobile-menu-wrapper">
        <div class="mobile-menu-overlay"></div>
        <!-- End of .mobile-menu-overlay -->

        <a href="#" class="mobile-menu-close"><i class="close-icon"></i></a>
        <!-- End of .mobile-menu-close -->

        <div class="mobile-menu-container scrollable">
            <form action="{{url('/search')}}" method="get" class="input-wrapper">
                <input type="text" class="form-control" name="search" autocomplete="off" placeholder="Search">
                <button class="btn btn-search" type="submit">
                    <i class="w-icon-search"></i>
                </button>
            </form>
            <!-- End of Search Form -->
            <div class="tab">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="#main-menu" class="nav-link active">Main Menu</a>
                    </li>
                    <li class="nav-item">
                        <a href="#categories" class="nav-link">Categories</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="main-menu">
                    <ul class="mobile-menu">
					  <li class="{{ request()->is('/') ? 'active' : '' }}">
						<a href="{{ url('/') }}">Home</a>
					  </li>

					  <li class="{{ request()->is('shop') ? 'active' : '' }}">
						<a href="{{ url('/shop') }}">Shop</a>
					  </li>

					  <li class="{{ request()->is('stores') ? 'active' : '' }}">
						<a href="{{ url('/stores') }}">Stores</a>
					  </li>

					  <li class="{{ request()->is('my-community-deal') ? 'active' : '' }}">
						<a href="{{ url('/my-community-deal') }}">My Community deal</a>
					  </li>

					  {{-- Submenu for Commodities --}}
					  @php
						  $commodityActive = request()->is('agricultural-commodities', 'minerals-materials');
					  @endphp
					  <li class="has-submenu {{ $commodityActive ? 'open active' : '' }}">
						<a href="javascript:void(0);" onclick="toggleSubmenu(event)">
						  Commodities
						  <i class="w-icon-angle-right toggle-icon-menu mt-2"></i>
						</a>
						<ul class="submenu" style="{{ $commodityActive ? 'display: block;' : '' }}">
						  <li class="{{ request()->is('agricultural-commodities') ? 'active' : '' }}">
							<a href="{{ url('/agricultural-commodities') }}">Agricultural Commodities</a>
						  </li>
						  <li class="{{ request()->is('minerals-materials') ? 'active' : '' }}">
							<a href="{{ url('/minerals-materials') }}">Minerals & Materials</a>
						  </li>
						</ul>
					  </li>

					  {{-- Auth check --}}
					  @if(Auth::check())
						<li class="{{ request()->is('my-account') ? 'active' : '' }}">
						    @if(!empty(Auth::user()->role) && Auth::user()->role=='admin')
								<a href="{{ url('/admin/dashboard') }}">My Account</a>
						    @elseif(!empty(Auth::user()->role) && Auth::user()->role=='vendor')
							    <a href="{{ url('/vendor/dashboard') }}">My Account</a>
						    @else
						      <a href="{{ url('/my-account') }}">My Account</a>
					        @endif
						</li>
						<li>
						  <a style="cursor:pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
						  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						  </form>
						</li>
					  @else
						<li class="{{ request()->is('register') ? 'active' : '' }}">
						  <a href="{{ url('/register') }}">Register</a>
						</li>
						<li class="{{ request()->is('login') ? 'active' : '' }}">
						  <a href="{{ url('/login') }}">Login</a>
						</li>
					  @endif
					</ul>

                </div>
                <div class="tab-pane" id="categories">
					  @php
						use App\Models\Category; 
						$categories = Category::with('children')->where('status', 1)->whereNull('parent_id')->get();
						$selectedCategoryId = request('category');
					  @endphp

					  <ul class="mobile-menu">
						@foreach ($categories as $parent)
						  @php
							$hasChildren = $parent->children->count();
							$childIds = $parent->children->pluck('id')->toArray();
							$isChildSelected = in_array($selectedCategoryId, $childIds);
							$isParentSelected = $selectedCategoryId == $parent->id;
						  @endphp

						  <li class="category-item {{ $hasChildren ? 'has-children' : '' }} {{ $isChildSelected || $isParentSelected ? 'expanded' : '' }}">
							<a href="{{ $hasChildren ? 'javascript:void(0);' : url('/shop') . '?' . http_build_query(array_merge(request()->except('page'), ['category' => $parent->id])) }}"
							   class="category-toggle fw-bold d-block {{ $isChildSelected || $isParentSelected ? 'text-primary' : '' }}">
							  @if($hasChildren)
								<i class="w-icon-angle-right toggle-icon"></i>
							  @endif
							  {{ $parent->name }}
							</a>

							@if($hasChildren)
							  <ul class="subcategory" style="{{ $isChildSelected ? 'display: block;' : 'display: none;' }}">
								@foreach ($parent->children as $child)
								  <li>
									<a href="{{ url('/shop') . '?' . http_build_query(array_merge(request()->except('page'), ['category' => $child->id])) }}"
									   class="{{ $selectedCategoryId == $child->id ? 'text-primary fw-bold' : '' }}">
									  - {{ $child->name }}
									</a>
								  </li>
								@endforeach
							  </ul>
							@endif
						  </li>
						@endforeach
					  </ul>
					</div>

            </div>
        </div>
    </div>

    <a href="https://wa.me/917830304557"
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
    <!-- End of Mobile Menu -->

    <!-- Start of Newsletter popup -->
    <!--<div class="newsletter-popup mfp-hide">
        <div class="newsletter-content">
            <h4 class="text-uppercase font-weight-normal ls-25">Get Up to<span class="text-primary">25% Off</span></h4>
            <h2 class="ls-25">Sign up to Escx</h2>
            <p class="text-light ls-10">Subscribe to the Escx market newsletter to 
                receive updates on special offers.</p>
            <form action="{{url('/')}}" method="get" class="input-wrapper input-wrapper-inline input-wrapper-round">
                <input type="email" class="form-control email font-size-md" name="email" id="email2" placeholder="Your email address" required="">
                <button class="btn btn-dark" type="submit">SUBMIT</button>
            </form>
            <div class="form-checkbox d-flex align-items-center">
                <input type="checkbox" class="custom-checkbox" id="hide-newsletter-popup" name="hide-newsletter-popup" required="">
                <label for="hide-newsletter-popup" class="font-size-sm text-light">Don't show this popup again.</label>
            </div>
        </div>
    </div>-->
    <!-- End of Newsletter popup -->

    <!-- Start of Quick View -->
    <!--<div class="product product-single product-popup">
        <div class="row gutter-lg">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="product-gallery product-gallery-sticky">
                    <div class="swiper-container product-single-swiper swiper-theme nav-inner">
                        <div class="swiper-wrapper row cols-1 gutter-no">
                            <div class="swiper-slide">
                                <figure class="product-image">
                                    <img src="{{ asset('frontend/assets/images/products/popup/1-440x494-1.jpg')}}" data-zoom-image="{{ asset('frontend/assets/images/products/popup/1-800x900.jpg')}}" alt="Water Boil Black Utensil" width="800" height="900">
                                </figure>
                            </div>
                            <div class="swiper-slide">
                                <figure class="product-image">
                                    <img src="{{ asset('frontend/assets/images/products/popup/2-440x494-1.jpg')}}" data-zoom-image="{{ asset('frontend/assets/images/products/popup/2-800x900.jpg')}}" alt="Water Boil Black Utensil" width="800" height="900">
                                </figure>
                            </div>
                            <div class="swiper-slide">
                                <figure class="product-image">
                                    <img src="{{ asset('frontend/assets/images/products/popup/3-440x494-1.jpg')}}" data-zoom-image="{{ asset('frontend/assets/images/products/popup/3-800x900.jpg')}}" alt="Water Boil Black Utensil" width="800" height="900">
                                </figure>
                            </div>
                            <div class="swiper-slide">
                                <figure class="product-image">
                                    <img src="{{ asset('frontend/assets/images/products/popup/4-440x494-1.jpg')}}" data-zoom-image="{{ asset('frontend/assets/images/products/popup/4-800x900.jpg')}}" alt="Water Boil Black Utensil" width="800" height="900">
                                </figure>
                            </div>
                        </div>
                        <button class="swiper-button-next"></button>
                        <button class="swiper-button-prev"></button>
                    </div>
                    <div class="product-thumbs-wrap swiper-container" data-swiper-options="{
                        'navigation': {
                            'nextEl': '.swiper-button-next',
                            'prevEl': '.swiper-button-prev'
                        }
                    }">
                        <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">
                            <div class="product-thumb swiper-slide">
                                <img src="{{ asset('frontend/assets/images/products/popup/1-103x116-1.jpg')}}" alt="Product Thumb" width="103" height="116">
                            </div>
                            <div class="product-thumb swiper-slide">
                                <img src="{{ asset('frontend/assets/images/products/popup/2-103x116-1.jpg')}}" alt="Product Thumb" width="103" height="116">
                            </div>
                            <div class="product-thumb swiper-slide">
                                <img src="{{ asset('frontend/assets/images/products/popup/3-103x116-1.jpg')}}" alt="Product Thumb" width="103" height="116">
                            </div>
                            <div class="product-thumb swiper-slide">
                                <img src="{{ asset('frontend/assets/images/products/popup/4-103x116-1.jpg')}}" alt="Product Thumb" width="103" height="116">
                            </div>
                        </div>
                        <button class="swiper-button-next"></button>
                        <button class="swiper-button-prev"></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 overflow-hidden p-relative">
                <div class="product-details scrollable pl-0">
                    <h2 class="product-title">Electronics Black Wrist Watch</h2>
                    <div class="product-bm-wrapper">
                        <figure class="brand">
                            <img src="{{ asset('frontend/assets/images/products/brand/brand-1-1.jpg')}}" alt="Brand" width="102" height="48">
                        </figure>
                        <div class="product-meta">
                            <div class="product-categories">
                                Category:
                                <span class="product-category"><a href="#">Electronics</a></span>
                            </div>
                            <div class="product-sku">
                                SKU: <span>MS46891340</span>
                            </div>
                        </div>
                    </div>

                    <hr class="product-divider">

                    <div class="product-price">$40.00</div>

                    <div class="ratings-container">
                        <div class="ratings-full">
                            <span class="ratings" style="width: 80%;"></span>
                            <span class="tooltiptext tooltip-top"></span>
                        </div>
                        <a href="{{url('/')}}" class="rating-reviews">(3 Reviews)</a>
                    </div>

                    <div class="product-short-desc">
                        <ul class="list-type-check list-style-none">
                            <li>Ultrices eros in cursus turpis massa cursus mattis.</li>
                            <li>Volutpat ac tincidunt vitae semper quis lectus.</li>
                            <li>Aliquam id diam maecenas ultricies mi eget mauris.</li>
                        </ul>
                    </div>

                    <hr class="product-divider">

                    <div class="product-form product-variation-form product-color-swatch">
                        <label>Color:</label>
                        <div class="d-flex align-items-center product-variations">
                            <a href="{{url('/')}}" class="color" style="background-color: #ffcc01"></a>
                            <a href="{{url('/')}}" class="color" style="background-color: #ca6d00;"></a>
                            <a href="{{url('/')}}" class="color" style="background-color: #1c93cb;"></a>
                            <a href="{{url('/')}}" class="color" style="background-color: #ccc;"></a>
                            <a href="{{url('/')}}" class="color" style="background-color: #333;"></a>
                        </div>
                    </div>
                    <div class="product-form product-variation-form product-size-swatch">
                        <label class="mb-1">Size:</label>
                        <div class="flex-wrap d-flex align-items-center product-variations">
                            <a href="{{url('/')}}" class="size">Small</a>
                            <a href="{{url('/')}}" class="size">Medium</a>
                            <a href="{{url('/')}}" class="size">Large</a>
                            <a href="{{url('/')}}" class="size">Extra Large</a>
                        </div>
                        <a href="{{url('/')}}" class="product-variation-clean">Clean All</a>
                    </div>

                    <div class="product-variation-price">
                        <span></span>
                    </div>

                    <div class="product-form">
                        <div class="product-qty-form">
                            <div class="input-group">
                                <input class="quantity form-control" type="number" min="1" max="10000000">
                                <button class="quantity-plus w-icon-plus"></button>
                                <button class="quantity-minus w-icon-minus"></button>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-cart">
                            <i class="w-icon-cart"></i>
                            <span>Add to Cart</span>
                        </button>
                    </div>

                    <div class="social-links-wrapper">
                        <div class="social-links">
                            <div class="social-icons social-no-color border-thin">
                                <a href="{{url('/')}}" class="social-icon social-facebook w-icon-facebook"></a>
                                <a href="{{url('/')}}" class="social-icon social-twitter w-icon-twitter"></a>
                                <a href="{{url('/')}}" class="social-icon social-pinterest fab fa-pinterest-p"></a>
                                <a href="{{url('/')}}" class="social-icon social-whatsapp fab fa-whatsapp"></a>
                                <a href="{{url('/')}}" class="social-icon social-youtube fab fa-linkedin-in"></a>
                            </div>
                        </div>
                        <span class="divider d-xs-show"></span>
                        <div class="product-link-wrapper d-flex">
                            <a href="{{url('/')}}" class="btn-product-icon btn-wishlist w-icon-heart"><span></span></a>
                            <a href="{{url('/')}}" class="btn-product-icon btn-compare btn-icon-left w-icon-compare"><span></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
<script>
  function toggleSubmenu(event) {
    event.preventDefault();
    const parentLi = event.target.closest('li');
    const submenu = parentLi.querySelector('.submenu');
    if (submenu) {
      const isOpen = parentLi.classList.contains('open');
      submenu.style.display = isOpen ? 'none' : 'block';
      parentLi.classList.toggle('open');
    }
  }
</script>