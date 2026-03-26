<!-- Start of Quick View -->
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
						<!--<figure class="brand">
							<img src="{{asset('frontend/assets/images/products/brand/brand-1-1.jpg')}}" alt="Brand" width="102" height="48">
						</figure>-->
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
							<div class="product-sku">
								Brand: <span>{{$product->brand->title}}</span>
							</div>
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

					<div class="product-short-desc">
						{!! $product->short_description !!}
					</div>

					<hr class="product-divider">
				
			<!--  Varient Product Start -->
					@php
						$hasVariants = $product->productAttributes->isNotEmpty();
					@endphp
					
					@foreach($product->productAttributes as $attributeGroup)
						@php
							$attributeName = strtolower($attributeGroup->attribute->name);
						@endphp
						<input type="hidden" name="variants[{{ $attributeName }}]" id="variant-{{ $attributeName }}">
						<div class="product-form product-variation-form product-{{ $attributeName }}-swatch mb-3">
							<label class="mb-1">{{ ucfirst($attributeName) }}:</label>
							<div class="flex-wrap d-flex align-items-center product-variations varient-{{$attributeName}}">
								@foreach($attributeGroup->variants->unique('value') as $variant)
									@php
										$value = $variant->attributeValue->value ?? '';
										$colorStyle = ($attributeName === 'color') ? 'style=background-color:' . $value : '';
										$price = $variant->price ?? 0;
										$image = $variant->image ? asset('uploads/variant_images/' . $variant->image) : '';
										$sku  = $variant->sku ?? '';
									@endphp
									<a href="javascript:void(0)"
									   class="variant-option {{ $attributeName }}"
									   data-variant-type="{{ $attributeName }}"
									   data-attribute="{{ $attributeGroup->attribute->name }}"
									   data-value-id="{{ $variant->attributeValue->id }}"
									   data-price="{{ $price }}"
									   data-image="{{ $image }}"
									   data-sku="{{ $sku }}"
									   
									   {!! $colorStyle !!}>
									   @if($attributeName !== 'color') {{ $value }} @endif
									</a>
								@endforeach
							</div>
						</div>
					@endforeach
				<!--  Varient Product End -->
				
					<div class="fix-bottom product-sticky-content sticky-content-test">
						@if($hasVariants)
						   <a href="{{ url('/product/') }}/{{$product->slug}}" style="font-size:14px;">Clear All</a>	<br/>	<br/>
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
							<button  data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="1"   title="Add to cart" class="btn btn-primary btn-check-product-test">
									<i class="w-icon-cart"></i>
									<span>Add to Cart</span>
								</button>
								
							@else
								<button  data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="0"   title="Add to cart" class="btn btn-primary btn-check-product">
									<i class="w-icon-cart"></i>
									<span>Add to Cart</span>
								</button>
							@endif
						</div>
					</div>

					<div class="social-links-wrapper">
						<div class="social-links">
							<div class="social-icons social-no-color border-thin">
								<a href="#" class="social-icon social-facebook w-icon-facebook"></a>
								<a href="#" class="social-icon social-twitter w-icon-twitter"></a>
								<a href="#" class="social-icon social-pinterest fab fa-pinterest-p"></a>
								<a href="#" class="social-icon social-whatsapp fab fa-whatsapp"></a>
								<a href="#" class="social-icon social-youtube fab fa-linkedin-in"></a>
							</div>
						</div>
						 <!--<span class="divider d-xs-show"></span>
					   <div class="product-link-wrapper d-flex">
							<a href="#" class="btn-product-icon btn-wishlist w-icon-heart"><span></span></a>
							<a href="#" class="btn-product-icon btn-compare btn-icon-left w-icon-compare"><span></span></a>
						</div>-->
					</div>
				</div>
			</div>
		</div>