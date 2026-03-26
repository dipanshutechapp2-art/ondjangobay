@extends('layouts.app_inner')
@section('title', $storeInfo->store_name ?? 'Store products')
@section('content')

<!-- Start of Main -->
<main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb bb-no">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ request()->fullUrl() }}">{{$storeInfo->store_name ?? 'Store products'}}</a></li>
            </ul>
        </div>
    </nav>
	
    <!-- Page Content -->
    <div class="page-content">
        <div class="container">
            <div class="shop-content row-test gutter-lg mb-10">
                <!-- Main Content -->
                <div class="main-content">
					<div class="store-info-box mb-5 p-4 border rounded shadow-sm bg-light">
						<div class="row ">
							<div class="col-md-2 mb-3 mb-md-0">
								<img src="{{ asset('uploads/store/' . ($storeInfo->logo ?? 'default.png')) }}" alt="Store Logo" class="img-fluid rounded" style="max-height: 100px;">
							</div>
							
							@php
								$averageRating = $storeInfo->average_rating;
								$reviewCount   = $storeInfo->review_count;
								$ratingPercent = $averageRating * 20;
							@endphp
							
							<div class="col-md-10">
								<h4 class="mb-1">{{ $storeInfo->store_name }}</h4>
								<p class="mb-1"><i class="fa fa-user"></i> <b>Vendor name:</b> {{ $storeInfo->user->name ?? "N/A" }}</p>
								<div class="ratings-container">
									<div class="ratings-full">
										<span class="ratings" style="width: {{$ratingPercent}}%;"></span>
										<span class="tooltiptext tooltip-top"></span>
									</div>
									<a href="#" class="rating-reviews">({{$reviewCount}} Reviews)</a>
								</div>
								<p class="mb-1"><i class="w-icon-map-marker"></i> {{ $storeInfo->address ?? 'No address provided' }}</p>
								<p class="mb-0"><i class="w-icon-phone"></i> {{ $storeInfo->phone ?? 'No phone' }}</p>
								<p class="mb-0"> {{ $storeInfo->description ?? '' }}</p>
							</div>
						</div>
					</div>
					
				<!--menu wrapper -->
					
					<div class="vendor-menu-section mb-4">
						<div class="container">
							<div class="row">
								<div class="col-md-12">
									<div class="vendor-menu-wrapper vendor-menu-wrappers">
										<header class="vendor-header">
										    
											<!-- Hamburger / Cross -->
											<button id="vendor-menu-toggle" class="vendor-menu-toggle">&#9776;</button>

											<!-- Menu -->
											<nav id="vendor-nav" class="vendor-nav">
												@php 
												    
													$selectedCategorySlug = request()->query('category');
													
													function renderCategoriesStore($categories, $storeSlug, $selectedCategorySlug, $depth = 1) {
														$html = '<ul>';

														foreach ($categories as $category) {
															$isActive = $selectedCategorySlug === $category->slug ? 'active' : '';

															if ($category->children->count() && $depth < 2) {
																$html .= '<li class="vendor-has-submenu '.$isActive.'">';
																$html .= '<a href="' . url('store/product/'.$storeSlug) . '?category=' . $category->slug . '" class="vendor-parent-link">'
																	   . e($category->name)
																	   . ' <span class="vendor-caret"><i class="w-icon-angle-down"></i></span></a>';

																$html .= '<ul class="vendor-dropdown">';
																foreach ($category->children as $child) {
																	$childActive = $selectedCategorySlug === $child->slug ? 'active' : '';
																	$html .= '<li class="'.$childActive.'"><a href="' . url('store/product/'.$storeSlug) . '?category=' . $child->slug . '">'
																		   . e($child->name) . '</a></li>';
																}
																$html .= '</ul>';
																$html .= '</li>';
															} else {
																$html .= '<li class="'.$isActive.'"><a href="' . url('store/product/'.$storeSlug) . '?category=' . $category->slug . '">'
																	   . e($category->name) . '</a></li>';
															}
														}

														$html .= '</ul>';
														return $html;
													}
												@endphp

												{!! renderCategoriesStore($storeCategories, $storeInfo->slug, $selectedCategorySlug) !!}
											</nav>
											<a href="{{ url('store/product/'.$storeInfo->slug) }}" class="clear-vendor">Clear Filters</a>
										</header>
									</div>
								</div>
							</div>
						</div>
					</div>
				<div class="vendor-product-listing mb-4">
					<div class="container">
						<div class="row justify-content-center">
							@php 
								$selectedCategorySlug = $selectedCategory ?? request()->query('category');

								function findCategoryBySlug($categories, $slug) {
									foreach ($categories as $category) {
										if ($category->slug == $slug) return $category;
										if ($category->children->count()) {
											$found = findCategoryBySlug($category->children, $slug);
											if ($found) return $found;
										}
									}
									return null;
								}

								$selectedCategoryObj = findCategoryBySlug($storeCategories, $selectedCategorySlug);
								
								function renderThirdLevelCategoriesGrid($categories, $storeSlug, $selectedCategorySlug) {
									$html = '';
									if (!$selectedCategorySlug) return $html;

									$selectedCategory = findCategoryBySlug($categories, $selectedCategorySlug);

									if ($selectedCategory && $selectedCategory->children->count()) {
										foreach ($selectedCategory->children as $child) {

											$imageUrl = !empty($child->desktop_image) 
												? asset('/uploads/categories/' . $child->desktop_image)
												: asset('/frontend/assets/no-image.webp');

											$html .= '<div class="col-6 col-sm-4 col-md-3 col-lg-2">';
											$html .= '<div class="category-wrap-">';
											$html .= '<figure class="category-media mb-2">';
											$html .= '<a href="' . url("store/product/".$storeSlug) . '?category=' . $child->slug . '">';
											$html .= '<img src="' . $imageUrl . '" alt="' . e($child->name) . '" width="80" height="80">';
											$html .= '</a></figure>';
											$html .= '<div class="category-content">';
											$html .= '<h4 class="category-name mb-0">';
											$html .= '<a href="' . url("store/product/".$storeSlug) . '?category=' . $child->slug . '">'
												   . e($child->name) . '</a>';
											$html .= '</h4></div></div></div>';
										}
									}

									return $html;
								}
							@endphp

							{!! renderThirdLevelCategoriesGrid($storeCategories, $storeInfo->slug, $selectedCategorySlug) !!}
						</div>
					</div>
				</div>
				
				<!--Category Bredcum listing start -->
					@php
						$selectedCategorySlug = request()->query('category') ?? $selectedCategory;
					
						function findCategoryBySlugww($categories, $slug) {
							foreach ($categories as $category) {
								if ($category->slug == $slug) return $category;
								if ($category->children->count()) {
									$found = findCategoryBySlug($category->children, $slug);
									if ($found) return $found;
								}
							}
							return null;
						}

						function getCategoryBreadcrumb($category) {
							$breadcrumb = [];
							while ($category) {
								array_unshift($breadcrumb, $category);
								$category = $category->parent; 
							}
							return $breadcrumb;
						}

						$breadcrumbCategories = [];
						if ($selectedCategorySlug) {
							$selectedCategory = findCategoryBySlugww($storeCategories, $selectedCategorySlug);
							if ($selectedCategory) {
								$breadcrumbCategories = getCategoryBreadcrumb($selectedCategory);
							}
						}

					@endphp
					
				@if(!empty($breadcrumbCategories))
					
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
							<li class="breadcrumb-item"><a href="{{ url('store/product/'.$storeInfo->slug) }}">{{ $storeInfo->name }}</a></li>

							@foreach($breadcrumbCategories as $cat)
								<li class="breadcrumb-item @if($cat->slug == $selectedCategorySlug) active @endif" 
									@if($cat->slug == $selectedCategorySlug) aria-current="page" @endif>
									@if($cat->slug == $selectedCategorySlug)
										{{ $cat->name }}
									@else
										<a href="{{ url('store/product/'.$storeInfo->slug) }}?category={{ $cat->slug }}">{{ $cat->name }}</a>
									@endif
								</li>
							@endforeach
						</ol>
					</nav>
					<!--Category Bredcum listing start -->
				@endif
				
				<!--vendor product listing end -->
				<div class="vendor-multiple-product-list">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
										
						<h4 class=" mb-4">Products</h4>
						<!-- Product Grid -->
						<div class="product-wrapper row cols-lg-5 cols-md-3 cols-sm-2 cols-2">
							@if($products->isNotEmpty())
								@foreach($products as $product)
									<div class="product-wrap">
										<div class="product text-center">
											<figure class="product-media">
												<a href="{{ url('/product/' . $product->slug) }}">
													<img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" width="300" height="338">
												</a>
												<div class="product-action-horizontal">
														@php
															$hasVariants = $product->productAttributes->isNotEmpty();	
														@endphp
														<a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-has-variant="{{ $hasVariants ? 1 : 0 }}" title="Add to cart">
													</a>
													<a class="btn-wishlist btn-product-icon w-icon-heart" title="Wishlist" data-id="{{ $product->id }}"></a>
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
												
												<div class="product-cat-wrapper">

<div class="product-cat">
@foreach($product->categories as $category)
<a href="#">{{ $category->name }}</a>{{ !$loop->last ? ',' : '' }}
@endforeach
</div>

<div class="category-tooltip">
@foreach($product->categories as $category)
<span>{{ $category->name }}</span>
@endforeach
</div>

</div>
											   
												<div class="product-name-wrapper">

<h3 class="product-name" onclick="toggleName(this)">
<a href="{{ url('/product/' . $product->slug) }}">
{{ $product->name }}
</a>
</h3>

<div class="name-tooltip">
{{ $product->name }}
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
													<a href="#" class="rating-reviews">({{$reviewCount}} reviews)</a>
												</div>
												<div class="product-pa-wrapper">
													<div class="product-price">{{ formatCurrency($product->price) }}</div>
												</div>
											</div>
										</div>
									</div>
								@endforeach
							@else
								<p>No products found.</p>
							@endif
						</div>
						<!-- Pagination -->
						<div class="mt-5">
							@if($products->hasPages())
								{{ $products->appends(request()->query())->links('pagination.custom') }}
							@endif
						</div>
						
							</div>
						</div>
					</div>
				</div>
                    
                    
                    
                    
                    	<!-- START TABS-->
						@if(auth()->check())
							<div class="tab tab-nav-boxed tab-nav-underline product-tabs">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item">
										<a href="#product-tab-reviews" class="nav-link active">Reviews (0)</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="product-tab-reviews">
										<div class="row mb-4">
											<div class="col-xl-12 col-lg-12 mb-4">
												<div class="review-form-wrapper">
													@if(session()->has('success'))
														<div class="alert alert-success">
														  <strong>Success!</strong> {{ session()->get('success') }}
														</div><br/>
													@endif
													@if(session()->has('error'))
														<div class="alert alert-danger">
															<strong>Warning!</strong> {{ session()->get('error') }}
														</div><br/>
													@endif
													<form action="{{ url('/store/submit-review') }}" method="POST" class="review-form">
													@CSRF
													<input type="hidden" name="store_id" value="{{$storeInfo->id}}" required>
													 <div class="rating-form">
														<label for="rating">Your rating of this store :</label>
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
													 @foreach($storeReview as $review)
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
												@if($storeReview->isEmpty())
													<p class="text-muted">No reviews yet for this store.</p>
												@endif
											</div>

										</div>
									</div>
								</div>
							</div>
							<!-- END TABS-->
						@endif
						
                    
                    
                </div>
                <!-- End Main Content -->
            </div>
        </div>
    </div>
</main>




<script>
(function(){
  const vendorMenuToggle = document.getElementById('vendor-menu-toggle');
  const vendorNav = document.getElementById('vendor-nav');

  // Toggle sidebar
  vendorMenuToggle.addEventListener('click', () => {
    vendorNav.classList.toggle('vendor-active');
    vendorMenuToggle.innerHTML = vendorNav.classList.contains('vendor-active') ? '&times;' : '&#9776;';
  });

  // Dropdown toggle (desktop + mobile)
  document.querySelectorAll('.vendor-has-submenu > .vendor-parent-link').forEach(vendorLink => {
    vendorLink.addEventListener('click', e => {
      e.preventDefault();
      const vendorLi = vendorLink.parentElement;

      // close all other open dropdowns
      document.querySelectorAll('.vendor-has-submenu.vendor-open').forEach(openLi => {
        if (openLi !== vendorLi) {
          openLi.classList.remove('vendor-open');
        }
      });

      // toggle clicked one
      vendorLi.classList.toggle('vendor-open');
    });
  });

  // Close menu when clicking outside (mobile only)
  document.addEventListener('click', e => {
    if (window.innerWidth <= 768 && vendorNav.classList.contains('vendor-active')) {
      if (!vendorNav.contains(e.target) && e.target !== vendorMenuToggle) {
        vendorNav.classList.remove('vendor-active');
        vendorMenuToggle.innerHTML = '&#9776;';
      }
    }
  });
})();
</script>


 

<script>
(function () {
  document.addEventListener("DOMContentLoaded", function () {
    var navbars = document.querySelectorAll(".vendor-menu-wrapper");

    if (navbars.length > 0) {
      window.addEventListener("scroll", function () {
        navbars.forEach(function (nav) {
          if (window.scrollY > 350) {  // <-- scroll threshold
            nav.classList.add("scrolleds");
          } else {
            nav.classList.remove("scrolleds");
          }
        });
      });
    }
  });

  // independent function
  window.scrollTabSection = function () {
    var navbars = document.querySelectorAll(".vendor-menu-wrapper");
    navbars.forEach(function (nav) {
      nav.classList.remove("scrolleds");
    });
    return true;
  };
})();
</script>



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

function toggleName(el){

if(window.innerWidth <= 768){

document.querySelectorAll(".product-name-wrapper").forEach(function(item){
item.classList.remove("active");
});

el.parentElement.classList.add("active");

}

}

</script>
@endsection
        