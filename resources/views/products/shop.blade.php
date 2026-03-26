@extends('layouts.app_inner')
@section('title', 'Shop')
@section('content')
<!-- Start of Main -->
<main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb bb-no">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/shop') }}">Shop</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-content">
        <div class="container max-container">
            <div class="shop-content row gutter-lg mb-10">

                <!-- Sidebar -->
                <aside class="sidebar shop-sidebar sticky-sidebar-wrapper sidebar-fixed">
                    @include('products.filter_sidebar')
                </aside>

                <!-- Main Content -->
                <div class="main-content">
                    <!-- Sorting Toolbar -->
                    <nav class="toolbox sticky-toolbox sticky-content fix-top d-flex justify-content-between align-items-center mb-4">
                        <a href="#" class="btn btn-primary btn-outline btn-rounded left-sidebar-toggle btn-icon-left d-block d-lg-none">
                            <i class="w-icon-category"></i><span>Filters</span>
                        </a>

                        <!-- Sort Form -->
                        <form method="GET" action="{{ url('/shop') }}" id="sortForm" class="d-flex align-items-center">
                            <label for="sort" class="me-2 mb-0">Sort By :</label>
                            <select name="sort" id="sort" class="form-control me-4" onchange="this.form.submit()">
                                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Recommended</option>
                                <option value="most-popular" {{ request('sort') == 'most-popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="new-arrivals" {{ request('sort') == 'new-arrivals' ? 'selected' : '' }}>New Arrivals</option>
                                <option value="top-rated" {{ request('sort') == 'top-rated' ? 'selected' : '' }}>Top Rated</option>
                                <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price Low to Hight</option>
                                <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price Hight to Low</option>
                            </select>

                            <!-- Preserve filters -->
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">

                            @if(request()->has('brands') && is_array(request('brands')))
                            @foreach(request('brands') as $brand)
                            <input type="hidden" name="brands[]" value="{{ $brand }}">
                            @endforeach
                            @endif

                            @if(request()->has('availability') && is_array(request('availability')))
                            @foreach(request('availability') as $availability)
                            <input type="hidden" name="availability[]" value="{{ $availability }}">
                            @endforeach
                            @endif

                            @if(request()->has('types') && is_array(request('types')))
                            @foreach(request('types') as $type)
                            <input type="hidden" name="types[]" value="{{ $type }}">
                            @endforeach
                            @endif

                            @if(request()->has('min_rating'))
                            <input type="hidden" name="min_rating" value="{{ request('min_rating') }}">
                            @endif

                        </form>
                    </nav>

                    <!-- Product Grid -->
                    <div class="product-wrapper row cols-lg-4 cols-md-3 cols-sm-2 cols-2 shop-product-wrapper">
                        @forelse ($products as $product)
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

                                    <div class="product-cat-wrapper">

                                        <div class="product-cat" onclick="toggleCat(this)">
                                            @foreach ($product->categories as $category)
                                            {{ $category->name }}{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        </div>

                                        <div class="category-tooltip">
                                            @foreach ($product->categories as $category)
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
                                        $reviewCount = $product->reviews->count();
                                        $averageRating = $reviewCount > 0 ? number_format($product->reviews->avg('rating'), 1) : 0;
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
                        @empty
                        <p>No products found.</p>
                        @endforelse
                    </div>
                    @if($products->hasPages())
                    {{ $products->links('pagination.custom') }}
                    @endif
                </div>
                <!-- End Main Content -->

            </div>
        </div>
    </div>
</main>
<script>
    document.querySelectorAll('.brand-link').forEach(el => {
        el.addEventListener('click', () => {
            let list = new Set((document.querySelector('input[name="brand"]').value || '').split(',').filter(Boolean));
            if (list.has(el.dataset.id)) list.delete(el.dataset.id);
            else list.add(el.dataset.id);
            document.querySelector('input[name="brand"]').value = Array.from(list).join(',');
            document.getElementById('filters').submit();
        });
    });
</script>
<script>
    function toggleCat(el) {

        if (window.innerWidth <= 768) {

            let all = document.querySelectorAll(".product-cat-wrapper");

            // sab close karo
            all.forEach(function(item) {
                item.classList.remove("active");
            });

            // current open karo
            el.parentElement.classList.add("active");

        }

    }


    // outside click close

    document.addEventListener("click", function(e) {

        if (!e.target.closest(".product-cat-wrapper")) {

            document.querySelectorAll(".product-cat-wrapper").forEach(function(el) {
                el.classList.remove("active");
            });

        }

    });

    function toggleName(el) {

        if (window.innerWidth <= 768) {

            document.querySelectorAll(".product-name-wrapper").forEach(function(item) {
                item.classList.remove("active");
            });

            el.parentElement.classList.add("active");

        }

    }
</script>
@endsection