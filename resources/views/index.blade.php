@extends('layouts.app')
@section('title', 'Escx')
@section('content')
	<!-- Start of Main -->
	<main class="main">
		 
			<div class="intro-wrapper mt-4">
                <div class="container">
				<div class="row">
					<div class="intro-slide-wrapper col-md-8 mb-4">
						<div class="swiper-container swiper-theme pg-inner animation-slider" data-swiper-options="{
							'nav': false,
							'dots': true,
							'slidesPerView': 1,
							'autoplay': {
								'delay': 8000,
								'disableOnInteraction': false
							}}">

							<div class="swiper-wrapper row gutter-no cols-1">
	@if($sliders)
    @foreach($sliders as $slider)
        <div class="swiper-slide banner banner-fixed intro-slide intro-slide1"
            style="background-image: url('{{ asset('uploads/sliders/' . $slider->desktop_image) }}'); background-color: #E7ECF0;">
            
            {{-- Mobile background image using picture tag for responsive loading --}}
            <picture>
                <source media="(max-width: 767px)" srcset="{{ asset('uploads/sliders/' . $slider->mobile_image) }}">
                <img src="{{ asset('uploads/sliders/' . $slider->desktop_image) }}" alt="Slider Image" class="w-100 d-none">
            </picture>

            <div class="banner-content y-50">
                <div class="slide-animate" data-animation-options="{ 'name': 'zoomIn', 'duration': '1s' }">
                    <h3 class="banner-title text-primary text-uppercase font-weight-normal ls-normal">
                        {{ $slider->title }}
                    </h3>
                    <h4 class="banner-subtitle text-dark text-capitalize font-weight-bolder ls-25">
                        {{ $slider->description }}
                    </h4>
                    <a href="{{ $slider->url ?? url('/shop') }}" class="btn btn-dark btn-outline btn-rounded btn-icon-right">
                        {{'Shop Now'}}
                        <i class="w-icon-long-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@endif

								
							</div>
							<div class="swiper-pagination"></div>
						</div>
					</div>
					<div class="intro-banner-wrapper col-md-4">
						<div class="row">
							<div class="intro-banner banner col-md-12 col-sm-6 mb-4 banner-fixed overlay-dark">
								<figure class="banner-media">
									<img src="{{ asset('uploads/homepage/hero/' . $posts->desktop_image1) }}" alt="Banner" width="347" height="245" style="background-color: #E9E1F1;">
								</figure>
								<div class="banner-content">
									<h5 class="banner-subtitle text-default font-weight-normal ls-normal mb-1">{{$posts->title1}}</h5>
									<h3 class="banner-title text-capitalize ls-25">{{$posts->description1}}</h3>
									<a href="{{ $posts->url1 ?? url('/shop') }}" class="btn btn-dark btn-link btn-underline btn-icon-right">
										Shop Now
										<i class="w-icon-long-arrow-right"></i>
									</a>
								</div>
							</div>
							<!-- End of Intro Banner -->
							<div class="intro-banner banner col-md-12 col-sm-6 mb-4 banner-fixed overlay-dark">
								<figure class="banner-media">
									<img src="{{ asset('uploads/homepage/hero/' . $posts->desktop_image2) }}" alt="Banner" width="347" height="245" style="background-color: #E9E9EB;">
								</figure>
								<div class="banner-content">
									<h5 class="banner-subtitle text-light font-weight-bold ls-25 mb-1">{{$posts->title2}}</h5>
									<h3 class="banner-title text-capitalize text-white ls-25">{{$posts->description2}}</h3>
									<a href="{{ $posts->url2 ?? url('/shop') }}" class="btn btn-white btn-link btn-underline btn-icon-right">
										Shop Now
										<i class="w-icon-long-arrow-right"></i>
									</a>
								</div>
							</div>
							<!-- End of Intro Banner -->
						</div>
					</div>
				</div>
			</div>
			</div>
			<!-- End of Intro-wrapper -->




			<div class="icon-wrapper-section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="swiper-container swiper-theme icon-box-wrapper appear-animate br-sm bg-white mt-2 mb-7" data-swiper-options="{
				'spaceBetween': 0,
				'slidesPerView': 1,
				'breakpoints': {
					'576': {
						'slidesPerView': 2
					},
					'992': {
						'slidesPerView': 3
					},
					'1200': {
						'slidesPerView': 4
					}
				}}">
				<div class="swiper-wrapper row cols-md-4 cols-sm-3 cols-1">
					<div class="swiper-slide icon-box icon-box-side text-dark">
						<span class="icon-box-icon icon-shipping">
							<i class="w-icon-truck"></i>
						</span>
						<div class="icon-box-content">
							<h4 class="icon-box-title font-weight-bolder">Free Shipping & Returns</h4>
							<p class="text-default">For all orders over $99</p>
						</div>
					</div>
					<div class="swiper-slide icon-box icon-box-side text-dark">
						<span class="icon-box-icon icon-payment">
							<i class="w-icon-bag"></i>
						</span>
						<div class="icon-box-content">
							<h4 class="icon-box-title font-weight-bolder">Secure Payment</h4>
							<p class="text-default">We ensure secure payment</p>
						</div>
					</div>
					<div class="swiper-slide icon-box icon-box-side text-dark icon-box-money">
						<span class="icon-box-icon icon-money">
							<i class="w-icon-money"></i>
						</span>
						<div class="icon-box-content">
							<h4 class="icon-box-title font-weight-bolder">Money Back Guarantee</h4>
							<p class="text-default">Any back within 30 days</p>
						</div>
					</div>
					<div class="swiper-slide icon-box icon-box-side text-dark icon-box-chat">
						<span class="icon-box-icon icon-chat">
							<i class="w-icon-chat"></i>
						</span>
						<div class="icon-box-content">
							<h4 class="icon-box-title font-weight-bolder">Customer Support</h4>
							<p class="text-default">Call or email us 24/7</p>
						</div>
					</div>
				</div>
			</div>
                        </div>
                    </div>
                </div>
            </div>
			<!-- End of Icon Box Wrapper -->
			
		<!-- multiple category  -->
		 <section class="multiple-category gray-bg">
			<div class="container">
				<div class="row  multiple-category-row">
					@if($parentCategoriesAndChiled->isNotEmpty())
						@foreach($parentCategoriesAndChiled as $parent)
							<div class="col-lg-3 col-md-6">
								<!-- category card  -->
								 <div class="multiple-category-card">
									<h4 class="category-main-title">{{ $parent->name ?? "" }}</h4>
										 <div class="row inner-cat-row">
											@foreach($parent->children as $keys=>$child)
												@if($keys+1<=4) 
													<div class="col-6">
														<a href="{{url('/shop')}}?category={{$child->id}}" class="inner-category-wrapper">
															<div class="inner-category-image">
															   @if(!empty($child->desktop_image))
																   <img src="{{asset('/uploads/categories/')}}/{{$child->desktop_image}}" alt="">
																@else
																   <!--<img src="{{asset('/frontend/assets/no-image.webp')}}" alt="">-->
																   <img src="https://marketplace.dipanshutech.co.in/uploads/products/1757095205_68bb25255da83.jpg" alt="">
																@endif
															</div>
															<h5 class="inner-category-title">{{ $child->name }}</h5>
														</a>
													</div>
												@endif
											@endforeach	 
										 </div>
										<!-- inner category listing end -->
										<!-- discover more btn  -->
										 <div class="discover-more"><a href="{{url('/shop')}}" class="discover-more-btn">Discover More</a></div>
										<!-- discover more btn end -->
								 </div>
								<!-- category card end -->
							</div>
						@endforeach	   
					@endif	   
				</div>
			</div>
		 </section>
		<!-- multiple category end -->
			
				@foreach($categoriesProduct as $level1)
					<section class="product-sections">
						<div class="container">
							<div class="row">
								<div class="col-md-12">
									<h2 class="category-section-title">{{ $level1->name }}</h2>
								</div>
							</div>
							@foreach($level1->children as $level2)
								@foreach($level2->children as $level3)
									@if($level3->products->isNotEmpty())
										<div class="row">
											<div class="col-md-12">
												<nav aria-label="breadcrumb">
													<ol class="breadcrumb bg-transparent px-0 py-2">
														<li class="breadcrumb-item">{{ $level2->name }}</li>
														<li class="breadcrumb-item active" aria-current="page">{{ $level3->name }}</li>
													</ol>
												</nav>
											</div>
										</div>

										<div class="row product-listing-row">
											@foreach($level3->products as $product)
												<div class="product-cols col-6 col-sm-4 col-md-3 col-lg-2">
													<div class="product text-center market-place-product-grid">
														<figure class="product-media">
															<a href="{{ url('/product/')}}/{{$product->slug}}">
																<img src="{{ asset('uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
																<img src="{{ asset('uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
															</a>
															<div class="product-action-horizontal">
																@php
																	$hasVariants = $product->productAttributes->isNotEmpty();	
																@endphp
																<a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="{{ $hasVariants ? 1 : 0 }}" title="Add to cart"></a>
																<a style="cursor:pointer;" class="btn-wishlist btn-product-icon w-icon-heart" title="Wishlist" data-id="{{ $product->id }}"></a>
																<!--<a style="cursor:pointer;" class="btn-product-icon btn-quickview w-icon-search" data-id="{{ $product->id }}" title="Quick View"></a>-->
																<!--<a style="cursor:pointer;" class="btn-product-icon w-icon-search" data-id="{{ $product->id }}" title="Quick View"></a>-->
																<a style="cursor:pointer;" onclick="toggleComparison({{ $product->id }})" class="btn-product-icon btn-compare-test w-icon-compare comparison-btn" title="Compare" id="comparison-btn-{{ $product->id }}"></a>

															</div>
															@if($product->quantity <= 0)
																<h4 class="out-of-stock">
																  Out of stock
																</h4>
															@endif
														</figure>
														<div class="product-details"> 
															

															<div class="product-name-wrapper">

<h4 class="product-name">
<a href="{{ url('/product/') }}/{{$product->slug}}">
{{$product->name}}
</a>
</h4>

<div class="name-tooltip">
{{$product->name}}
</div>

</div>

															<div class="ratings-container">
																@php 
																	$reviewCount     = $product->reviews->count();
																	$averageRating   = $reviewCount > 0 ? number_format($product->reviews->avg('rating'), 1) : 0;
																	$ratingPercent = $averageRating * 20; // 5 stars = 100%
																@endphp
																<div class="ratings-full">
																	<span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
																	<span class="tooltiptext tooltip-top">{{ $averageRating }} / 5</span>
																</div>
																<a href="{{ url('/product/')}}/{{$product->slug}}" class="rating-reviews">({{ $product->reviews->count() }} Reviews)</a>
															</div>
															<div class="product-price">
																<ins class="new-price">{{ formatCurrency($product->price)}}</ins><!--<del class="old-price">{{formatCurrency($product->price)}}</del>-->
															</div>
														</div>
													</div>
												</div>
											@endforeach
										</div>
									@endif
								@endforeach
							@endforeach
						</div>
					</section>
				@endforeach




        <!--product listing new design -->
		 <!-- <section class="product-sections ">
		      <div class="container">
		          <div class="row">
		              <div class="col-md-12">
		                  <h2 class="category-section-title">Deals Hot of The Day</h2>
		              </div>
		          </div>
		          <div class="row product-listing-row">
		              <div class="product-cols col-4 col-sm-4 col-md-4 col-lg-2">
		                  <div class="product text-center market-place-product-grid">
                                <figure class="product-media">
                                    <a href="product-default.html">
                                        <img src="https://marketplace.dipanshutech.co.in/uploads/products/1757017212_68b9f47c16312.jpg" alt="Product" width="300" height="337">
                                        <img src="https://marketplace.dipanshutech.co.in/uploads/products/1757017212_68b9f47c16312.jpg" alt="Product" width="300" height="337">
                                    </a>
                                    <div class="product-action-horizontal">
                                        <a href="#" class="btn-product-icon btn-cart w-icon-cart" title="Add to cart"></a>
                                        <a href="#" class="btn-product-icon btn-wishlist w-icon-heart" title="Wishlist"></a>
                                        <a href="#" class="btn-product-icon btn-compare w-icon-compare" title="Compare"></a>
                                        <a href="#" class="btn-product-icon btn-quickview w-icon-search" title="Quick View"></a>
                                    </div>
                                </figure>
                                <div class="product-details">
                                    <h4 class="product-name"><a href="product-default.html">Men's Black Watch</a></h4>
                                    <div class="ratings-container">
                                        <div class="ratings-full">
                                            <span class="ratings" style="width: 100%;"></span>
                                            <span class="tooltiptext tooltip-top"></span>
                                        </div>
                                        <a href="product-default.html" class="rating-reviews">(5 Reviews)</a>
                                    </div>
                                    <div class="product-price">
                                        <ins class="new-price">$75.00</ins><del class="old-price">$79.00</del>
                                    </div>
                                </div>
                            </div>
		              </div>
		          </div>
		      </div>
		  </section>-->
		  <!--product listing new design end -->
		  
			
			
			
			
			
			
			<div class="product-deal-section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-link-wrapper title-deals after-none appear-animate mb-5">
				<h2 class="title">Deals Hot of The Day</h2>
				<div class="product-countdown-container d-flex font-size-sm text-white bg-dark br-xs align-items-center mr-auto mt-1 mb-1">
					<label>Offer Ends in: </label>
					<div class="product-countdown countdown-compact ml-1 font-weight-bold" data-until="+10h" data-relative="true" data-compact="true" data-format="HMS">00:00:00</div>
				</div>
				<a href="{{url('/shop')}}" class="ml-0 mb-0 ls-normal">
					More Products
					<i class="w-icon-long-arrow-right"></i>
				</a>
			</div>
			<div class="swiper-container swiper-theme product-deals-wrapper appear-animate" 
     data-swiper-options="{
        'spaceBetween': 20,
        'slidesPerView': 2,
        'breakpoints': {
            '576': {
                'slidesPerView': 2
            },
            '768': {
                'slidesPerView': 4
            },
            '992': {
                'slidesPerView': 6
            }
        }
     }">

				<div class="swiper-wrapper row cols-lg-5 cols-md-4 cols-sm-3 cols-2">
					@if($products->count()>0)
						@foreach($products as $product)
							<div class="swiper-slide product-wrap">
								<div class="product text-center market-place-product-grid">
									<figure class="product-media">
										<a href="{{ url('/product/')}}/{{$product->slug}}">
											<img src="{{ asset('uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
											<img src="{{ asset('uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
										</a>
										<div class="product-action-horizontal">
											@php
												$hasVariants = $product->productAttributes->isNotEmpty();	
											@endphp
											<a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="{{ $hasVariants ? 1 : 0 }}" title="Add to cart"></a>
											<a style="cursor:pointer;" class="btn-wishlist btn-product-icon w-icon-heart" title="Wishlist" data-id="{{ $product->id }}"></a>
											<!--<a style="cursor:pointer;" class="btn-product-icon btn-quickview w-icon-search" data-id="{{ $product->id }}" title="Quick View"></a>-->
											<!--<a style="cursor:pointer;" class="btn-product-icon w-icon-search" data-id="{{ $product->id }}" title="Quick View"></a>-->
											<a style="cursor:pointer;" onclick="toggleComparison({{ $product->id }})" class="btn-product-icon btn-compare-test w-icon-compare comparison-btn" title="Compare" id="comparison-btn-{{ $product->id }}"></a>

										</div>
										  @if($product->quantity <= 0)
											<h4 class="out-of-stock">
											  Out of stock
											</h4>
										@endif
									</figure>
									<div class="product-details"> 
									  

										<div class="product-name-wrapper">

<h4 class="product-name">
<a href="{{ url('/product/') }}/{{$product->slug}}">
{{$product->name}}
</a>
</h4>

<div class="name-tooltip">
{{$product->name}}
</div>

</div>

										<div class="ratings-container">
										    @php 
												$reviewCount     = $product->reviews->count();
												$averageRating   = $reviewCount > 0 ? number_format($product->reviews->avg('rating'), 1) : 0;
												$ratingPercent = $averageRating * 20; // 5 stars = 100%
											@endphp
											<div class="ratings-full">
												<span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
												<span class="tooltiptext tooltip-top">{{ $averageRating }} / 5</span>
											</div>
											<a href="{{ url('/product/')}}/{{$product->slug}}" class="rating-reviews">({{ $product->reviews->count() }} Reviews)</a>
										</div>
										<div class="product-price">
											<ins class="new-price">{{ formatCurrency($product->price)}}</ins><!--<del class="old-price">{{formatCurrency($product->price)}}</del>-->
										</div>
									</div>
								</div>
							</div>	
						@endforeach
					@endif
				</div>
			</div>
                        </div>
                    </div>
                </div>
            </div>
			<!-- End of Prodcut Deals Wrapper -->







			<div class="banner-grid">
                <div class="container">
                    <div class="row grid banner-grid pt-1 appear-animate">
				<div class="grid-item grid-item1 banner banner-fixed overlay-dark  col-lg-8 height-x1">
					<figure class="banner-media">
						<img src="{{ asset('/frontend/assets/banner/banner-3.jpg')}}" alt="Category Banner" width="900" height="290" style="background-color: #373538;">
					</figure>
					<div class="banner-content y-50">
						<h4 class="banner-subtitle text-capitalize font-weight-normal ls-normal">Trending Collection</h4>
						<h3 class="banner-title text-white text-capitalize font-weight-bold ls-normal">Furniture Sale</h3>
						<h5 class="banner-price-info text-white font-weight-normal ls-25">
							Up to
							<span class="text-primary font-weight-bolder">25% OFF</span>
						</h5>
						<a href="{{url('/shop')}}" class="btn btn-white btn-link btn-underline btn-icon-right">
							Shop Now
							<i class="w-icon-long-arrow-right"></i>
						</a>
					</div>
				</div>
				<div class="grid-item grid-item3 banner banner-fixed overlay-dark col-lg-4 col-md-6 height-x2">
					<figure class="banner-media">
						<img src="{{ asset('/frontend/assets/banner/banner-4.jpg')}}" alt="Category Banner" width="440" height="290" style="background-color: #D3D3D5;">
					</figure>
					<div class="banner-content">
						<h4 class="banner-subtitle text-white text-capitalize font-weight-normal ls-normal">New Collection</h4>
						<h3 class="banner-title text-white text-capitalize font-weight-bold ls-25">Women Sale</h3>
						<a href="{{url('/shop')}}" class="btn btn-white btn-link btn-underline btn-icon-right">
							Shop Now
							<i class="w-icon-long-arrow-right"></i>
						</a>
					</div>
				</div>
				<div class="grid-item grid-item4 banner banner-fixed overlay-dark col-lg-4 col-md-6 height-x1">
					<figure class="banner-media">
						<img src="{{ asset('/frontend/assets/banner/banner-5.jpg')}}" alt="category" width="440" height="600" style="background-color: #ADB5BF;">
					</figure>
					<div class="banner-content">
						<h4 class="banner-subtitle text-white text-capitalize font-weight-normal ls-normal">New Arrivals</h4>
						<h3 class="banner-title text-white text-capitalize ls-25">Sports Shoes</h3>
						<a href="{{url('/shop')}}" class="btn btn-white btn-link btn-underline btn-icon-right">
							Shop Now
							<i class="w-icon-long-arrow-right"></i>
						</a>
					</div>
				</div>
				<div class="grid-item grid-item2 banner banner-fixed overlay-light col-lg-4 col-md-6 height-x1">
					<figure class="banner-media">
						<img src="{{ asset('/frontend/assets/banner/banner-6.jpg')}}" alt="category" width="440" height="290" style="background-color: #272624;">
					</figure>
					<div class="banner-content x-50 y-50 text-center">
						<h4 class="banner-subtitle text-light text-uppercase font-weight-bold ls-15">30% Off Our Entire Shop</h4>
						<h3 class="banner-title text-white text-capitalize font-secondary font-weight-bolder ls-normal mb-0">Black Friday</h3>
						<p class="text-uppercase text-center font-weight-normal">
							Use Code 
							<strong class="text-white">Blkfr123</strong> 
							at Checkout
						</p>
						<a href="{{url('/shop')}}" class="btn btn-primary btn-rounded text-white">
							Shop Now
						</a>
					</div>
				</div>
				<div class="grid-space col-1"></div>
			</div>
                </div>
            </div>
			<!-- End of Banner Grid -->


		 <!-- old design hidden for now  -->
		<div class="month-category pt-8" style="display:none;">
			<div class="container">
				<h2 class="title text-center d-block mt-3 mb-6 appear-animate fadeIn appear-animation-visible" style="animation-duration: 1.2s;">
					Latest Product Of The Month
				</h2>
				<div class="category-wrapper bg-white">
					<div class="icon-category-wrapper">
						<div class="nav-filters swiper-container swiper-theme appear-animate mb-4 pb-0" data-target="#products-1" data-swiper-options="{
							'spaceBetween': 0,
							'slidesPerView': 2,
							'breakpoints': {
								'576': {
									'slidesPerView': 3
								},
								'768': {
									'slidesPerView': 4
								},
								'992': {
									'slidesPerView': 5
								},
								'1200': {
									'slidesPerView': 8
								}
							}
							}">
							<!--<div class="swiper-wrapper row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 g-0">
								@foreach($categories as $key => $category)
									<div class="swiper-slide col text-center border-end">
										<a href="{{ url('/shop?category=').$category->id }}"
										   class="d-block py-3 @if($loop->first) border-bottom border-3 border-warning @endif">
											
											@if($category->desktop_image || $category->mobile_image)
												<img src="{{ asset('uploads/categories/' . $category->desktop_image) }}"
													 alt="{{ $category->name }}"
													 class="img-fluid mb-2"
													 style="width: 70px; height: 70px; object-fit: contain;margin:auto;">
											@else
												<i class="w-icon-tshirt fs-1 mb-2"></i>
											@endif

											<div class="fw-semibold text-dark small">{{ $category->name }}</div>
										</a>
									</div>
								@endforeach
							</div>-->
						</div>
						<!-- End of Icon Category Wrapper -->
					</div>


					
					
					 <div class="product-wrapper" >
						<div class="row grid cols-xl-5 cols-md-4 cols-sm-3 cols-2 appear-animate" id="products-1">
							
							<!-- End of Product Wrap -->
							@if($products->count()>0)
								@foreach($products as $product)
									<div class="grid-item product-wrap fashion furniture">
										<div class="product text-center market-place-product-grid">
											<figure class="product-media">
												<a href="{{ url('/product/')}}/{{$product->slug}}">
													<img src="{{ asset('/uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
													<img src="{{ asset('/uploads/products/')}}/{{$product->image}}" alt="Product" width="300" height="337">
												</a>
												
												<div class="product-action-horizontal">
													@php
														$hasVariants = $product->productAttributes->isNotEmpty();	
													@endphp
													<a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="{{ $hasVariants ? 1 : 0 }}" title="Add to cart">
												</a>
													<a style="cursor:pointer;" class="btn-wishlist btn-product-icon w-icon-heart" title="Wishlist" data-id="{{ $product->id }}"></a>
													<!--<a href="#" class="btn-product-icon w-icon-search" title="Quick View"></a>-->
													<a style="cursor:pointer;" onclick="toggleComparison({{ $product->id }})" class="btn-product-icon btn-compare-test w-icon-compare comparison-btn" title="Compare" id="comparison-btn-{{ $product->id }}"></a>
												</div>
													@if($product->quantity <= 0)
													<h4 class="out-of-stock">
													  Out of stock
													</h4>
												@endif
											</figure>
											<div class="product-details">
											
												<div class="product-name-wrapper">

<h4 class="product-name">
<a href="{{ url('/product/') }}/{{$product->slug}}">
{{$product->name}}
</a>
</h4>

<div class="name-tooltip">
{{$product->name}}
</div>

</div>

												<div class="ratings-container">
													@php 
														$reviewCount     = $product->reviews->count();
														$averageRating   = $reviewCount > 0 ? number_format($product->reviews->avg('rating'), 1) : 0;
														$ratingPercent = $averageRating * 20; // 5 stars = 100%
													@endphp
													<div class="ratings-full">
														<span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
														<span class="tooltiptext tooltip-top">{{ $averageRating }} / 5</span>
													</div>
													<a href="{{ url('/product/')}}/{{$product->slug}}" class="rating-reviews">({{ $product->reviews->count() }} Reviews)</a>
												</div>
												<div class="product-price">
													<ins class="new-price">{{ formatCurrency($product->price)}}</ins><!--<del class="old-price">{{formatCurrency($product->price)}}</del>-->
												</div>
											</div>
										</div>
									</div>
								@endforeach
							@endif
							
							<!-- End of Product Wrap -->
							<div class="grid-space col-xl-5col col-1"></div>
						</div>
					</div>  
					



					<!-- End of Product Wrapper -->



					





				</div>
			</div>
		</div>

<!-- old design end -->







		<!-- new latest product design  -->
					 <div class="new-latest-product">
					 <div class="container">
   <div class="row mb-5">
      <div class="col-lg-7 col-md-10">
        <div class="center-heading-wrapper">
			 <h2 class="category-section-title">Latest Product Of The Month</h2>
		 
		</div>
      </div>
   </div>
   

   <div class="row product-listing-row">
      @if($products->count() > 0)
         @foreach($products as $product)
            <div class="product-cols col-6 col-sm-4 col-md-3 col-lg-2">
               <div class="product text-center market-place-product-grid">
                  <figure class="product-media">
                     <a href="{{ url('/product/') }}/{{ $product->slug }}">
                        <img src="{{ asset('/uploads/products/') }}/{{ $product->image }}" alt="{{ $product->name }}" width="300" height="337">
                        <img src="{{ asset('/uploads/products/') }}/{{ $product->image }}" alt="{{ $product->name }}" width="300" height="337">
                     </a>

                     <div class="product-action-horizontal">
                        @php
                           $hasVariants = $product->productAttributes->isNotEmpty();
                        @endphp
                        <a style="cursor:pointer;" 
                           class="btn-check-product btn-product-icon w-icon-cart" 
                           data-id="{{ $product->id }}" 
                           data-name="{{ $product->name }}" 
                           data-slug="{{ $product->slug }}" 
                           data-has-variant="{{ $hasVariants ? 1 : 0 }}" 
                           title="Add to cart">
                        </a>

                        <a style="cursor:pointer;" 
                           class="btn-wishlist btn-product-icon w-icon-heart" 
                           title="Wishlist" 
                           data-id="{{ $product->id }}">
                        </a>

                        <!--<a style="cursor:pointer;" 
                           class="btn-product-icon w-icon-search" 
                           data-id="{{ $product->id }}" 
                           title="Quick View">
                        </a>-->
						<a style="cursor:pointer;" onclick="toggleComparison({{ $product->id }})" class="btn-product-icon btn-compare-test w-icon-compare comparison-btn" title="Compare" id="comparison-btn-{{ $product->id }}"></a>
                     </div>

                     @if($product->quantity <= 0)
                        <h4 class="out-of-stock">Out of stock</h4>
                     @endif
                  </figure>

                  <div class="product-details">

                    <div class="product-name-wrapper">

<h4 class="product-name">
<a href="{{ url('/product/') }}/{{ $product->slug }}">
{{ $product->name }}
</a>
</h4>

<div class="name-tooltip">
{{ $product->name }}
</div>

</div>

                     <div class="ratings-container">
                        @php 
                           $reviewCount   = $product->reviews->count();
                           $averageRating = $reviewCount > 0 ? number_format($product->reviews->avg('rating'), 1) : 0;
                           $ratingPercent = $averageRating * 20;
                        @endphp
                        <div class="ratings-full">
                           <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                           <span class="tooltiptext tooltip-top">{{ $averageRating }} / 5</span>
                        </div>
                        <a href="{{ url('/product/') }}/{{ $product->slug }}" class="rating-reviews">
                           ({{ $reviewCount }} Reviews)
                        </a>
                     </div>

                     <div class="product-price">
                        <ins class="new-price">{{ formatCurrency($product->price) }}</ins>
                        <!-- <del class="old-price">{{ formatCurrency($product->price) }}</del> -->
                     </div>
                  </div>
               </div>
            </div>
         @endforeach
      @else
         <div class="col-md-12 text-center">
            <p>No products available</p>
         </div>
      @endif
   </div>
</div>
</div>

					<!-- new latest product design end -->
		
		<div class="special-offer" >
			<div class="container">
				<h2 class="title mt-3 mb-5 appear-animate fadeIn appear-animation-visible" style="animation-duration: 1.2s;">
					Special Offers
				</h2>
				<div class="bg-white">
					<div class="banner-wrapper appear-animate row cols-md-2 mb-6">
						<div class="banner banner-fixed overlay-dark br-sm mt-4">
							<figure>
								<img src="{{ asset('/frontend/assets/banner/banner-7.jpg')}}" alt="Banner" width="680" height="180" style="background-color: #E5E6E8;">
							</figure>   
							<div class="banner-content y-50">
								<h2 class="banner-title text-dark font-weight-bolder ls-normal">
									Fitness Equipment<br>
									For Health
								</h2>
								<p class="text-dark">Only until the end of this week.</p>
								<a href="{{url('/shop')}}" class="btn btn-sm btn-outline btn-dark btn-rounded slide-animate">
									Shop Now
								</a>
							</div>
						</div>
						<div class="banner banner-fixed overlay-light br-sm mt-4">
							<figure>
								<img src="{{ asset('/frontend/assets/banner/banner-8.jpg')}}" alt="Banner" width="680" height="180" style="background-color: #565960;">
							</figure>   
							<div class="banner-content y-50">
								<h2 class="banner-title text-white font-weight-bold ls-normal mb-0">
									Electrolux Sale
								</h2>
								<h4 class="banner-price-info text-white font-weight-bold">
									<span class="text-primary font-weight-bolder">30%</span>
									Flat
								</h4>
								<p>Washing machine at low prices.</p>
								<a href="{{url('/shop')}}" class="btn btn-sm btn-white btn-outline btn-rounded slide-animate">
									Discover Now
								</a>
							</div>
						</div>                            
					</div>
					
					 
				</div>
			</div>
		</div>
		<div class="container mt-1">
			<h2 class="title title-brands text-left title-client pt-1 pb-1 mt-3 mb-4 appear-animate">Our Clients</h2>
			<div class="swiper-container swiper-theme brands-wrapper br-sm mb-10 appear-animate" data-swiper-options="{
				'loop': true,
				'spaceBetween': 0,
				'autoplay': {
					'delay': 4000,
					'disableOnInteraction': false
				},
				'breakpoints': {
					'0': {
						'slidesPerView': 2
					},
					'576': {
						'slidesPerView': 3
					},
					'768': {
						'slidesPerView': 4
					},
					'992': {
						'slidesPerView': 6
					},
					'1200': {
						'slidesPerView': 8
					}
				}
			}">
				<div class="swiper-wrapper row cols-xl-8 cols-lg-6 cols-md-4 cols-sm-3 cols-2 client-slider">
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
					<div class="swiper-slide">
						<figure>
							<img src="{{ asset('frontend/assets/footer-logo.png')}}" alt="Brand" width="290" height="100" class="client-logo">
						</figure>
					</div>
				</div>
			</div>
			
		</div>
	</main>
	<!-- Include compaign modules -->
	@include('partials.compaign_list');
	
<style>

.header-bottom {
 
    background: #f99e1c;}
    .header a:not(.btn):hover {
    color: #000;
}
.header-middle {
    
    background: #000;}.header-bottom .dropdown {
    margin-top: -0.0rem;
}

a.product-name:hover {
    
    color: red !important;
}
.page-wrapper .header .header-middle .header-right a:not(.btn):hover {
    color: #f99e1c !important;
}
img.client-logo {
  
    padding: 20px;
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
  
}
</style>

	<!-- End of Main -->
@endsection