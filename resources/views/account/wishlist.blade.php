@extends('layouts.app_inner')
@section('title', 'Wishlist')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Wishlist</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Wishlist</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content pt-2">
                <div class="container">
                    <div class="tab tab-vertical row gutter-lg">
                       @include('account.sidebar')
                       
                        <div class="tab-content mb-6">
                            <h3> <i class="w-icon-heart"></i> My wishlist
</h3>
                            <div class="tab-pane active in">
                                @if($wishlist->isNotEmpty())
									<div class="product-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2">
									@foreach($wishlist as $wishListData)
										<div class="product-wrap">
										<button class="btn btn-link btn-close btn-remove-wishlist" style="color:red;float:right;"  data-id="{{ $wishListData->product->id }}">
                                            <i class="fas fa-times"></i>
                                        </button><br/>
											<div class="product text-center">
											   
												<figure class="product-media">
													<a href="{{ url('/product/')}}/{{$wishListData->product->slug}}">
														<img src="{{asset('/uploads/products/')}}/{{$wishListData->product->image}}" alt="Product" width="300" height="338">
													</a>
													<div class="product-action-horizontal">
														@php
															$hasVariants = $wishListData->product->productAttributes->isNotEmpty();	
														@endphp
														<a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart" 
														   data-id="{{ $wishListData->product->id }}" data-name="{{ $wishListData->product->name }}" data-slug="{{ $wishListData->product->slug }}"
														   data-has-variant="{{ $hasVariants ? 1 : 0 }}" 
														   title="Add to cart">
														</a>
														<a style="cursor:pointer;" class="btn-wishlist btn-product-icon w-icon-heart" title="Wishlist" data-id="{{ $wishListData->product->id }}"></a>
														<!--<a href="#" class="btn-product-icon btn-compare w-icon-compare" title="Compare"></a>-->
														<a style="cursor:pointer;" onclick="toggleComparison({{ $wishListData->product->id }})" class="btn-product-icon btn-compare-test w-icon-compare comparison-btn" title="Compare" id="comparison-btn-{{ $wishListData->product->id }}"></a>
															{{-- <a href="#" class="btn-product-icon w-icon-search" title="Quick View"></a> --}}
													</div>
												</figure>
												
												<div class="product-details">
													<div class="product-cat-wrapper">

<div class="product-cat">
@if($wishListData->product->categories->isNotEmpty())

@foreach ($wishListData->product->categories as $category)
<a href="#"><span>{{ $category->name }}</span></a>@if (!$loop->last) , @endif
@endforeach

@else
<span>No categories</span>
@endif
</div>

<div class="category-tooltip">
@if($wishListData->product->categories->isNotEmpty())

@foreach ($wishListData->product->categories as $category)
<span>{{ $category->name }}</span>
@endforeach

@else
<span>No categories</span>
@endif
</div>

</div>
													<div class="product-name-wrapper">

<h3 class="product-name">
<a href="{{ url('/product/') }}/{{$wishListData->product->slug}}">
{{$wishListData->product->name}}
</a>
</h3>

<div class="name-tooltip">
{{$wishListData->product->name}}
</div>

</div>
													<div class="ratings-container">
														<div class="ratings-full">
															<span class="ratings" style="width: 100%;"></span>
															<span class="tooltiptext tooltip-top"></span>
														</div>
														<a href="#" class="rating-reviews">(0 reviews)</a>
													</div>
													<div class="product-pa-wrapper">
														<div class="product-price">
															{{ formatCurrency($wishListData->product->price)}}
														</div>
													</div>
												</div>
											</div>
										</div>
										@endforeach
									</div>
								@else
									<p>No any wishlist.</p><br/>
								@endif
								<br/>
                                <a href="{{url('/shop')}}" class="btn btn-dark btn-rounded btn-icon-right">Go
                                    Shop<i class="w-icon-long-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
		 <script>

document.addEventListener("click", function(e){

if(window.innerWidth <= 768){

// Product Name Click
let nameWrapper = e.target.closest(".product-name-wrapper");
if(nameWrapper){

document.querySelectorAll(".product-name-wrapper").forEach(function(el){
el.classList.remove("active");
});

nameWrapper.classList.add("active");
return;

}

// Category Click
let catWrapper = e.target.closest(".product-cat-wrapper");
if(catWrapper){

document.querySelectorAll(".product-cat-wrapper").forEach(function(el){
el.classList.remove("active");
});

catWrapper.classList.add("active");
return;

}

// Outside Click Close
document.querySelectorAll(".product-name-wrapper, .product-cat-wrapper").forEach(function(el){
el.classList.remove("active");
});

}

});

</script>
@endsection
        