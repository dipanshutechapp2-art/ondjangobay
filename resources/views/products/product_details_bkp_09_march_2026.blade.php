@extends('layouts.app_inner')
@section('title', $product->name)
@section('content')

	<!-- Start of Main -->
        <main class="main mb-10 pb-1">
            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav container">
                <ul class="breadcrumb bb-no">
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li>Product details</li>
                </ul>
            </nav>
            <!-- End of Breadcrumb -->
			@php 
			  if(!empty($product->image)) {
			      $mainPrductImg = $product->image;
			  }else{
				  $mainPrductImg = '';
			  }
			@endphp
            <!-- Start of Page Content -->
            <div class="page-content">
                <div class="container">
                    <div class="row gutter-lg">
                        <div class="main-content">
                            <div class="product product-single row">
                                <div class="col-md-6 mb-6">
									<div class="product-gallery product-gallery-sticky">
                                        <div class="swiper-container product-single-swiper swiper-theme nav-inner" data-swiper-options="{
                                            'navigation': {
                                                'nextEl': '.swiper-button-next',
                                                'prevEl': '.swiper-button-prev'
                                            }
                                        }">
                                            <div class="swiper-wrapper row cols-1 gutter-no">
                                                @if(!empty($product->image))
													
													<div class="swiper-slide">
														<figure class="product-image">
															<img src="{{asset('uploads/products/')}}/{{$product->image}}" data-zoom-image="{{asset('uploads/products/')}}/{{$product->image}}" alt="{{$product->name}}" width="800" height="900"/>
														</figure>
													</div>
													@foreach($product->galleryImages as $galleryImg)
														<div class="swiper-slide">
															<figure class="product-image">
																<img src="{{asset('uploads/product/gallery/')}}/{{$galleryImg->image}}" data-zoom-image="{{asset('uploads/product/gallery/')}}/{{$galleryImg->image}}" alt="Electronics Black Wrist Watch" width="800" height="900"/>
															</figure>
														</div>
													@endforeach
												@endif 
                                                
                                            </div>
                                            <button class="swiper-button-next"></button>
                                            <button class="swiper-button-prev"></button>
                                            <!--<a href="#" class="product-gallery-btn product-image-full"><i class="w-icon-zoom"></i></a>-->
                                        </div>
                                        <div class="product-thumbs-wrap swiper-container" data-swiper-options="{
                                            'navigation': {
                                                'nextEl': '.swiper-button-next',
                                                'prevEl': '.swiper-button-prev'
                                            }
                                        }">
                                            <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">
                                                @if(!empty($product->galleryImages))
													<div class="product-thumb swiper-slide">
														<img src="{{asset('uploads/products/')}}/{{$product->image}}" alt="Product Thumb" width="800" height="900">
													</div>
													@foreach($product->galleryImages as $galleryImg)
														<div class="product-thumb swiper-slide">
															<img src="{{asset('uploads/product/gallery/')}}/{{$galleryImg->image}}" alt="Product Thumb" width="800" height="900">
														</div>
													@endforeach
												@endif                                               
                                            </div>
                                            <button class="swiper-button-next"></button>
                                            <button class="swiper-button-prev"></button>
                                        </div>
                                    </div>
									
                                </div>
                                <div class="col-md-6 mb-4 mb-md-6">
                                    <div class="product-details" data-sticky-options="{'minWidth': 767}">
                                        @if($product->quantity <= 0)
											<h3 class="product-name" style="color: red;">
											  Out of stock
											</h3>
										@endif
										<h1 class="product-title">{{$product->name}}</h1>
                                        <div class="product-bm-wrapper">
                                            <div class="product-meta">
                                                <div class="product-categories">
                                                    Category:
                                                    <span class="product-category">
															@if($product->categories->isNotEmpty())
																@foreach ($product->categories as $category)
																	<a href="#"><span>{{ $category->name }}</span></a>@if (!$loop->last) , @endif
																@endforeach
															@else
																<span>No categories</span>
															@endif
													</a></span>
                                                </div>
                                                <div class="product-sku">
                                                    SKU: <span>{{$product->sku}}</span>
                                                </div><br/>
												@if(!empty($product->brand->title))
													<div class="product-sku">
														Brand: <span>{{$product->brand->title ?? ""}}</span>
													</div>
												@endif
                                            </div>
                                        </div>

                                        <hr class="product-divider">
										
										<div class="product-price">
											<ins class="new-price">
												<span id="product-price" data-base-price="{{ $product->price }}">
													{{ formatCurrency($product->price) }}
												</span>
											</ins>
											
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
                                            <a href="#product-tab-reviews" class="rating-reviews scroll-to">({{$reviewCount}})
                                                Reviews)</a>
                                        </div>

                                        <!--<div class="product-short-desc">
                                            {!! $product->short_description !!}
                                        </div> -->

                                        <hr class="product-divider">
									
								<!--  Variant Product Start - Updated with Select Dropdown -->
								        @php
											$hasVariants = $product->productAttributes->isNotEmpty();
										@endphp
										
                                        @foreach($product->productAttributes as $attributeGroup)
											@php
												$attributeName = strtolower($attributeGroup->attribute->name);
												$attributeId = $attributeGroup->attribute->id;
											@endphp
											<input type="hidden" name="variants[{{ $attributeName }}]" id="variant-{{ $attributeName }}">
											<div class="product-form product-variation-form mb-3">
												<label class="mb-1" for="variant-select-{{ $attributeId }}">{{ ucfirst($attributeName) }}:</label>
												<div class="d-flex align-items-center">
													<select 
														id="variant-select-{{ $attributeId }}"
														class="form-control variant-select"
														data-variant-type="{{ $attributeName }}"
														style="max-width: 300px;"
													>
														<option value="">Select {{ ucfirst($attributeName) }}</option>
														@foreach($attributeGroup->variants->unique('value') as $variant)
															@php
																$value = $variant->attributeValue->value ?? '';
																$price = $variant->price ?? 0;
																$image = $variant->image ? asset('uploads/variant_images/' . $variant->image) : '';
																$sku   = $variant->sku ?? '';
																$variantId = $variant->attributeValue->id ?? '';
																$stockStatus = '';
																
																// Check if variant is out of stock
																if(isset($variant->quantity) && $variant->quantity <= 0) {
																	$stockStatus = ' (Out of stock)';
																} elseif(isset($variant->is_active) && !$variant->is_active) {
																	$stockStatus = ' (Unavailable)';
																}
															@endphp
															<option 
																value="{{ $variantId }}"
																data-price="{{ $price }}"
																data-image="{{ $image }}"
																data-sku="{{ $sku }}"
																data-value-id="{{ $variantId }}"
																data-display-value="{{ $value }}"
																{{ (isset($variant->quantity) && $variant->quantity <= 0) ? 'disabled' : '' }}
															>
																{{ $value }}
																@if($price > 0)
																	(+{{ formatCurrency($price) }})
																@endif
																{{ $stockStatus }}
															</option>
														@endforeach
													</select>
													@if($attributeName === 'color')
														<div id="selected-color-preview-{{ $attributeId }}" 
															 class="ml-2 color-preview" 
															 style="width: 30px; height: 30px; border: 1px solid #ddd; border-radius: 4px; display: none;">
														</div>
													@endif
												</div>
											</div>
										@endforeach
									<!--  Variant Product End -->
									
                                        <div class="fix-bottom product-sticky-content sticky-content">
                                            @if($hasVariants)
											   <button type="button" id="clear-variants" style="font-size:14px; background: none; border: none; color: #666; text-decoration: underline; cursor: pointer;">Clear All</button><br/><br/>
										    @endif
											<div class="product-form container">
                                                
												<div class="product-qty-form">
													<div class="input-group">
														<input id="product-quantity" class="quantity form-control" type="number" min="1" max="10000000" value="1">
														<button class="quantity-plus w-icon-plus"></button>
														<button class="quantity-minus w-icon-minus"></button>
													</div>
												</div>
												@if($hasVariants)
												<button  data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="1"   title="Add to cart" class="btn btn-primary btn-check-product-test btn-cart-test">
														<i class="w-icon-cart"></i>
														<span>Add to Cart</span>
													</button>
													
												@else
													<button  data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="0"   title="Add to cart" class="btn btn-primary btn-check-product btn-cart-test">
														<i class="w-icon-cart"></i>
														<span>Add to Cart</span>
													</button>
												@endif
                                            </div>
                                        </div>

                                        <div class="social-links-wrapper">
                                            <!--<div class="social-links">
                                                <div class="social-icons social-no-color border-thin">
                                                    <a href="#" class="social-icon social-facebook w-icon-facebook"></a>
                                                    <a href="#" class="social-icon social-twitter w-icon-twitter"></a>
                                                    <a href="#" class="social-icon social-pinterest fab fa-pinterest-p"></a>
                                                    <a href="#" class="social-icon social-whatsapp fab fa-whatsapp"></a>
                                                    <a href="#" class="social-icon social-youtube fab fa-linkedin-in"></a>
                                                </div>
                                            </div>-->
                                             <!--<span class="divider d-xs-show"></span>
                                           <div class="product-link-wrapper d-flex">
                                                <a href="#" class="btn-product-icon btn-wishlist w-icon-heart"><span></span></a>
                                                <a href="#" class="btn-product-icon btn-compare btn-icon-left w-icon-compare"><span></span></a>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                 @if(session()->has('success'))
					<div class="alert alert-success">
					  <strong>Success!</strong> {{ session()->get('success') }}
					</div>
				@endif
				@if(session()->has('error'))
					<div class="alert alert-danger">
						<strong>Warning!</strong> {{ session()->get('error') }}
					</div>
				@endif
                            <div class="tab tab-nav-boxed tab-nav-underline product-tabs">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a href="#product-tab-description" class="nav-link active">Description</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#product-tab-specification" class="nav-link">Specification</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#product-tab-vendor" class="nav-link">Store Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#product-tab-reviews" class="nav-link">Customer Reviews ({{$product->reviews->count()}})</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="product-tab-description">
                                        <div class="row mb-4">
                                            <div class="col-md-12 mb-5">
                                                
                                                <div class="product-description-card">
                                                     {!! $product->description !!}
                                                </div>
                                                
											  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="product-tab-specification">
										@if($product->specifications)
											<ul class="list-none">
												@foreach($product->specifications as $key => $value)
													<li> 
													    <label>{{ $key }}:</label> 
														<p>{{ $value }}</p>
													</li>
												@endforeach
											</ul>
										@endif
                                    </div>
                                    <div class="tab-pane" id="product-tab-vendor">
                                        <div class="row mb-3">
											@if($product->stores->isNotEmpty())
												@foreach ($product->stores as $store)
													
													@php 
														$reviewCount   = $store->storeReviews->count();
														$averageRating = $reviewCount > 0 ? number_format($store->storeReviews->avg('rating'), 1) : 0;
														$ratingPercent = $averageRating * 20; // 5 stars = 100%
													@endphp
											
													<div class="col-md-12 pl-2 pl-md-12 mb-4" style="border: 1px solid #e5e3e3;padding: 10px 0px 10px 0px;">
														<div class="vendor-user">
															<figure class="vendor-logo mr-4">
																<a href="{{url('/store/product/')}}/{{$store->slug}}">
																	<img src="{{asset('uploads/store/')}}/{{$store->logo}}" alt="Vendor Logo" width="80" height="80">
																</a>
															</figure>
															<div>
																<div class="vendor-name"><a href="{{url('/store/product/')}}/{{$store->slug}}">{{$store->store_name}}</a></div>
																<div class="ratings-container">
																	<div class="ratings-full">
																		<span class="ratings" style="width: {{$ratingPercent}}%;"></span>
																		<span class="tooltiptext tooltip-top"></span>
																	</div>
																	<a href="{{url('/store/product/')}}/{{$store->slug}}" class="rating-reviews">({{$reviewCount}} Reviews)</a>
																</div>
															</div>
														</div>
														<a href="{{url('/store/product/')}}/{{$store->slug}}" class="btn btn-dark btn-link btn-underline btn-icon-right">Visit Store<i class="w-icon-long-arrow-right"></i></a>
													</div>
												@endforeach
											@else
												<div class="col-md-6 pl-2 pl-md-6 mb-4">
													<p>No vendor</p>
												</div>
											@endif
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="product-tab-reviews">
                                        <div class="row mb-4">
                                       
                                            <div class="col-xl-12 col-lg-12 mb-4">
                                                <div class="review-form-wrapper">
                                                    <h3 class="title tab-pane-title font-weight-bold mb-1">Submit Your
                                                        Review</h3>
                                                    <p class="mb-3">Your email address will not be published. Required
                                                        fields are marked *</p>
                                    <form action="{{ url('/submit-review') }}" method="POST" class="review-form">
                                        @csrf

                                        <input type="hidden" name="product_id" value="{{$product->id}}">
                                         <div class="rating-form">
                                            <label for="rating">Your Rating Of This Product :</label>
                                            <span class="rating-stars">
                                                <a class="star-1" href="#" data-value="1">1</a>
                                                <a class="star-2" href="#" data-value="2">2</a>
                                                <a class="star-3" href="#" data-value="3">3</a>
                                                <a class="star-4" href="#" data-value="4">4</a>
                                                <a class="star-5" href="#" data-value="5">5</a>
                                            </span>
                                            <select name="rating" id="rating" required style="display: none;">
                                                <option value="">Rate…</option>
                                                <option value="5">Perfect</option>
                                                <option value="4">Good</option>
                                                <option value="3">Average</option>
                                                <option value="2">Not that bad</option>
                                                <option value="1">Very poor</option>
                                            </select>
    
                                            </div>
                                            <textarea cols="30" rows="6" placeholder="Write Your Review Here..." class="form-control" name="review" required></textarea>
                                            <div class="row gutter-md">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" placeholder="Your Name" name="author" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="email" class="form-control" placeholder="Your Email" name="email" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-dark">Submit Review</button>
                                        </form>

                                                </div>
                                            </div>

                                            
											<div id="review-section">
											   <ul class="comments list-style-none">
												 @foreach($product->reviews as $review)
													<li class="comment">
														<div class="comment-body">
															<figure class="comment-avatar">
																<img src="{{ asset('uploads/default/no_user.jpg') }}" alt="Commenter Avatar" width="90" height="90">
															</figure>
															<div class="comment-content">
																<h4 class="comment-author">
																	<a>{{ $review->author }}</a>
																	<span class="comment-date">{{ $review->created_at->format('F d, Y \a\t h:i A') }}</span>
																</h4>
																<div class="ratings-container comment-rating">
																	<div class="ratings-full">
																		<span class="ratings" style="width: {{ $review->rating * 20 }}%;"></span>
																		<span class="tooltiptext tooltip-top">{{ $review->rating }}/5</span>
																	</div>
																</div>
																<p>{{ $review->review }}</p>
															</div>
														</div>
													</li>
												@endforeach
											</ul>
											@if($product->reviews->isEmpty())
												<p class="text-muted">No reviews yet for this product.</p>
											@endif
											</div>



											
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Main Content -->
                        <aside class="sidebar product-sidebar sidebar-fixed right-sidebar sticky-sidebar-wrapper">
                            <div class="sidebar-overlay"></div>
                            <a class="sidebar-close" href="#"><i class="close-icon"></i></a>
                            <a href="#" class="sidebar-toggle d-flex d-lg-none"><i class="fas fa-chevron-left"></i></a>
                            <div class="sidebar-content scrollable">
                          
                                
                                
                                <div class="sticky-sidebar">
                                    <div class="widget widget-icon-box mb-6">
                                        <div class="icon-box icon-box-side">
                                            <span class="icon-box-icon text-dark">
                                                <i class="w-icon-truck"></i>
                                            </span>
                                            <div class="icon-box-content">
                                                <h4 class="icon-box-title">Free Shipping & Returns</h4>
                                                <p>For all orders over $99</p>
                                            </div>
                                        </div>
                                        <div class="icon-box icon-box-side">
                                            <span class="icon-box-icon text-dark">
                                                <i class="w-icon-bag"></i>
                                            </span>
                                            <div class="icon-box-content">
                                                <h4 class="icon-box-title">Secure Payment</h4>
                                                <p>We ensure secure payment</p>
                                            </div>
                                        </div>
                                        <div class="icon-box icon-box-side">
                                            <span class="icon-box-icon text-dark">
                                                <i class="w-icon-money"></i>
                                            </span>
                                            <div class="icon-box-content">
                                                <h4 class="icon-box-title">Money Back Guarantee</h4>
                                                <p>Any back within 30 days</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                        <!-- End of Sidebar -->
                    </div>
                </div>
            </div>
            <!-- End of Page Content -->
        </main>
		
<script>
    document.querySelectorAll('.rating-stars a').forEach(function(star) {
        star.addEventListener('click', function(e) {
            e.preventDefault();
            const rating = this.getAttribute('data-value');
            document.getElementById('rating').value = rating;
            // Optional: Highlight selected stars
            document.querySelectorAll('.rating-stars a').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Clear All button functionality
    document.getElementById('clear-variants')?.addEventListener('click', function() {
        document.querySelectorAll('.variant-select').forEach(select => {
            select.value = '';
            
            // Clear hidden input
            const variantType = select.getAttribute('data-variant-type');
            const input = document.getElementById(`variant-${variantType}`);
            if (input) {
                input.value = '';
            }
            
            // Hide color preview
            if (variantType === 'color') {
                const attributeId = select.id.replace('variant-select-', '');
                const previewDiv = document.getElementById(`selected-color-preview-${attributeId}`);
                if (previewDiv) {
                    previewDiv.style.display = 'none';
                }
            }
        });
        
        // Reset price to base price
        updateProductPrice();
        updateProductImage();
    });
    
    // Update product image when variant is selected
    function updateProductImage() {
        let newImage = '';
        let defaultImage = "{{ asset('uploads/products/' . $mainPrductImg) }}";
        
        // Find first selected variant with an image
        document.querySelectorAll('.variant-select').forEach(select => {
            if (select.value && !newImage) {
                const selectedOption = select.options[select.selectedIndex];
                const variantImage = selectedOption.getAttribute('data-image')?.trim();
                if (variantImage && variantImage !== "") {
                    newImage = variantImage;
                }
            }
        });
		
        if (newImage && newImage !== "") {
            // Update main image and zoom image
            $('.product-gallery .swiper-slide:first-child img')
                .attr('src', newImage)
                .attr('data-zoom-image', newImage);

            // Update thumbnail
            $('.product-thumbs .swiper-slide:first-child img')
                .attr('src', newImage);
        } else {
            // Revert to default product image
            $('.product-gallery .swiper-slide:first-child img')
                .attr('src', defaultImage)
                .attr('data-zoom-image', defaultImage);

            $('.product-thumbs .swiper-slide:first-child img')
                .attr('src', defaultImage);
        }
    }
    
    // Color preview handling
    function updateColorPreview(selectElement) {
        const attributeId = selectElement.id.replace('variant-select-', '');
        const previewDiv = document.getElementById(`selected-color-preview-${attributeId}`);
        
        if (previewDiv && selectElement.value) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const colorValue = selectedOption.getAttribute('data-display-value');
            if (colorValue) {
                previewDiv.style.display = 'block';
                previewDiv.style.backgroundColor = colorValue;
            }
        } else if (previewDiv) {
            previewDiv.style.display = 'none';
        }
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const priceElement = document.getElementById("product-price");
    const basePrice = parseFloat(priceElement.dataset.basePrice);
    const variantSelects = document.querySelectorAll(".variant-select");
    const defaultCurrency = "{{ getDefaultSelectedCurrency() }}";
    const defaultImage = "{{ asset('uploads/products/' . $mainPrductImg) }}";

    const currency = window.currency;
    const currencyInfo = window.currencyInfo || { rate: 1, symbol: '$' };
    
    // Store selected variants and their prices
    let selectedVariants = {};
    
    // Function to update product price
    function updateProductPrice() {
        let totalVariantPrice = 0;
        let allSelected = true;
        
        // Reset selected variants
        selectedVariants = {};
        
        // Calculate total variant price
        variantSelects.forEach(select => {
            const variantType = select.getAttribute('data-variant-type');
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const variantPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const valueId = selectedOption.value;
                const displayValue = selectedOption.getAttribute('data-display-value');
                
                totalVariantPrice += variantPrice;
                selectedVariants[variantType] = {
                    id: valueId,
                    price: variantPrice,
                    name: displayValue
                };
            } else {
                allSelected = false;
            }
        });
        
        // Calculate total price
        let totalPrice = basePrice + totalVariantPrice;
        
        // Format and display price
        const basePriceConverted = basePrice * currencyInfo.rate;
        const variantPriceConverted = totalVariantPrice * currencyInfo.rate;
        const totalPriceConverted = totalPrice * currencyInfo.rate;
        
        // Update price display
        priceElement.innerText = currencyInfo.symbol + totalPriceConverted.toFixed(2);
        
        // Update price breakdown if all variants are selected
        const priceBreakdown = document.getElementById('price-breakdown');
        const basePriceText = document.getElementById('base-price-text');
        const variantPriceText = document.getElementById('variant-price-text');
        
        if (priceBreakdown && basePriceText && variantPriceText) {
            if (allSelected && totalVariantPrice > 0) {
                basePriceText.innerText = currencyInfo.symbol + basePriceConverted.toFixed(2);
                variantPriceText.innerText = currencyInfo.symbol + variantPriceConverted.toFixed(2);
                priceBreakdown.style.display = 'block';
            } else {
                priceBreakdown.style.display = 'none';
            }
        }
        
        return totalPrice;
    }
    
    // Function to update product image based on selected variants
    function updateProductImage() {
        let newImage = '';
        let defaultImage = "{{ asset('uploads/products/' . $mainPrductImg) }}";
        
        // Find first selected variant with an image
        variantSelects.forEach(select => {
            if (select.value && !newImage) {
                const selectedOption = select.options[select.selectedIndex];
                const variantImage = selectedOption.getAttribute('data-image')?.trim();
                if (variantImage && variantImage !== "") {
                    newImage = variantImage;
                }
            }
        });
        
        if (newImage && newImage !== "") {
            // Update main image and zoom image
            const mainImage = $('.product-gallery .swiper-slide:first-child img');
            if (mainImage.length) {
                mainImage.attr('src', newImage)
                        .attr('data-zoom-image', newImage);
            }
            
            // Update thumbnail
            const thumbImage = $('.product-thumbs .swiper-slide:first-child img');
            if (thumbImage.length) {
                thumbImage.attr('src', newImage);
            }
        } else {
            // Revert to default product image
            const mainImage = $('.product-gallery .swiper-slide:first-child img');
            if (mainImage.length) {
                mainImage.attr('src', defaultImage)
                        .attr('data-zoom-image', defaultImage);
            }
            
            const thumbImage = $('.product-thumbs .swiper-slide:first-child img');
            if (thumbImage.length) {
                thumbImage.attr('src', defaultImage);
            }
        }
    }
    
    // Function to update color preview
    function updateColorPreview(selectElement) {
        const attributeId = selectElement.id.replace('variant-select-', '');
        const previewDiv = document.getElementById(`selected-color-preview-${attributeId}`);
        
        if (previewDiv && selectElement.value) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const colorValue = selectedOption.getAttribute('data-display-value');
            if (colorValue) {
                previewDiv.style.display = 'block';
                previewDiv.style.backgroundColor = colorValue;
            }
        } else if (previewDiv) {
            previewDiv.style.display = 'none';
        }
    }
    
    // Add event listeners to all variant selects
    variantSelects.forEach(select => {
        select.addEventListener('change', function() {
            const variantType = this.getAttribute('data-variant-type');
            const selectedOption = this.options[this.selectedIndex];
            const variantInput = document.getElementById(`variant-${variantType}`);
            
            // Update hidden input
            if (variantInput) {
                variantInput.value = this.value;
            }
            
            // Update price
            updateProductPrice();
            
            // Update image
            updateProductImage();
            
            // Update color preview if applicable
            if (variantType === 'color') {
                updateColorPreview(this);
            }
        });
        
        // Initialize color preview for color selects
        const variantType = select.getAttribute('data-variant-type');
        if (variantType === 'color' && select.value) {
            updateColorPreview(select);
        }
    });
    
    // Initialize with base price
    const basePriceConverted = basePrice * currencyInfo.rate;
    priceElement.innerText = currencyInfo.symbol + basePriceConverted.toFixed(2);
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
	// Load cart function (if exists)
	if (typeof loadCart === 'function') {
		loadCart();
	}
	
    document.querySelectorAll('.btn-check-product-test').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;
			const name      = button.dataset.name;
			const quantity  = parseInt($('#product-quantity').val()) || 1;

            const hasVariant = this.dataset.hasVariant === '1';
			
            let variants = {};

            if (hasVariant) { 
                let valid = true;
                document.querySelectorAll('input[name^="variants"]').forEach(input => {
                    if (!input.value) {
                        valid = false;
                    } else {
                        const key = input.name.replace('variants[', '').replace(']', '');
                        variants[key] = input.value;
                    }
                });

                if (!valid) {
                    alert('Please select all product options.');
                    return;
                }
            }

            // Prepare payload
            const payload = {
                product_id: productId,
                variants: variants,
				quantity: quantity
            };

            const siteUrl = "{{ url('/') }}";
			
            fetch(`${siteUrl}/cart/add`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof renderNewCart === 'function') {
                        renderNewCart(data.cart_items, data.cart_total);
                    }
                    const cartDropdown = document.querySelector('.cart-dropdown');
                    const cartOverlay = document.querySelector('.cart-overlay');
                    if (cartDropdown) cartDropdown.classList.add('opened');
                    if (cartOverlay) cartOverlay.classList.add('active');
					
					if (typeof showToast === 'function') {
						showToast(name + " added to cart!");
					} else {
						alert(name + " added to cart!");
					}
                } else {
					if (typeof errorMsgShowToast === 'function') {
						errorMsgShowToast(data.message);
					} else {
						alert(data.message || 'Error adding to cart');
					}
                }
            });
        });
    });
});
</script>

<style>
.variant-select {
    max-width: 300px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    font-size: 14px;
}

.variant-select:focus {
    border-color: #336699;
    outline: none;
    box-shadow: 0 0 0 2px rgba(51, 102, 153, 0.2);
}

.color-preview {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-left: 10px;
    flex-shrink: 0;
}

.product-variation-form {
    margin-bottom: 15px;
}

.product-variation-form label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #333;
}

/* Style for disabled options (out of stock) */
.variant-select option:disabled {
    color: #999;
    background-color: #f5f5f5;
}

/* Style for options with additional price */
.variant-select option[data-price^="0."], 
.variant-select option[data-price^="-"] {
    color: #333;
}

#clear-variants {
    background: none;
    border: none;
    color: #666;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
}

#clear-variants:hover {
    color: #336699;
}

#price-breakdown {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
    line-height: 1.4;
}
</style>

@endsection