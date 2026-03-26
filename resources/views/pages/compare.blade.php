@extends('layouts.app_inner')
@section('title', 'Product Comparison')
@section('content')
<!-- Start of Main -->

<main class="main">
    <!-- Start of Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">Compare Products</h1>
        </div>
    </div>
    <!-- End of Page Header -->

    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav mb-2">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{url('/')}}">Home</a></li>
                <li>Compare</li>
            </ul>
        </div>
    </nav>
    <!-- End of Breadcrumb -->

    <!-- Start of Page Content -->
    <div class="page-content mb-10 pb-2">
        <div class="container">
            @if($products->isEmpty())
            <div class="text-center py-12">
                <div class="empty-compare">
                    <i class="w-icon-heart" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                    <p class="text-gray-500 text-lg mb-4">No products to compare</p>
                    <a href="{{ url('/shop') }}" class="btn btn-primary btn-rounded">
                        Browse Products
                    </a>
                </div>
            </div>
            @else
            <div class="compare-header mb-4 d-flex justify-content-between align-items-center flex-wrap">
                <p class="text-gray-600 mb-0">
                    {{ $products->count() }} of 4 products
                </p>

                <button onclick="clearComparison()" class="btn btn-danger btn-sm btn-rounded">
                    <i class="w-icon-times-solid"></i> Clear All
                </button>
            </div>


            <div class="compare-table">
                <!-- Compare Products Row -->
                <div class="compare-row cols-xl-5 cols-lg-4 cols-md-3 cols-2 compare-products">
                    <div class="compare-col compare-field">Products</div>
                    @foreach($products as $product)
                    <div class="compare-col compare-product" id="product-{{ $product->id }}">
                        <button onclick="removeFromComparison({{ $product->id }})" class="btn remove-product">
                            <i class="w-icon-times-solid"></i>
                        </button>
                        <div class="product text-center">
                            <figure class="product-media">
                                <a href="{{ url('/product/' . $product->slug) }}">
                                    <img src="{{ isset($product->image) ? asset('/uploads/products/' . $product->image) : asset('frontend/assets/no-image.webp') }}"
                                        alt="{{ $product->name }}" width="228" height="257"
                                        onerror="this.src='{{ asset('frontend/assets/no-image.webp') }}'">
                                </a>
                                <div class="product-action-vertical">
                                    @php
                                    $hasVariants = $product->productAttributes->isNotEmpty();
                                    @endphp

                                    <a style="cursor:pointer;" class="btn-check-product btn-product-icon w-icon-cart"
                                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-slug="{{ $product->slug }}" data-has-variant="{{ $hasVariants ? 1 : 0 }}"
                                        title="Add to cart">
                                    </a>

                                    <a style="cursor:pointer;" class="btn-wishlist btn-product-icon w-icon-heart"
                                        title="Wishlist" data-id="{{ $product->id }}">
                                    </a>
                                </div>

                            </figure>
                            <div class="product-details">
                                <h3 class="product-name">
                                    <a href="{{ url('/product/' . $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                <div class="product-price">
                                    <span class="new-price">${{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_price)
                                    <span class="old-price">${{ number_format($product->compare_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Empty columns for remaining slots -->
                    @for($i = $products->count(); $i < 4; $i++) <div class="compare-col compare-product empty-slot">
                        <div class="product text-center">
                            <a href="{{ url('/shop') }}">
                                <figure class="product-media">
                                    <div class="empty-product bg-gray-100 flex items-center justify-center"
                                        style="width: 228px; height: 257px;">
                                        <i class="w-icon-plus" style="font-size: 48px; color: #ccc;"></i>
                                    </div>
                                </figure>
                                <div class="product-details">
                                    <h3 class="product-name">Add Product</h3>
                                </div>
                            </a>
                        </div>
                </div>
                @endfor
            </div>
            <!-- End of Compare Products -->

            <!-- Price Row -->
            <div class="compare-row cols-xl-5 cols-lg-4 cols-md-3 cols-2 compare-price">
                <div class="compare-col compare-field">Price</div>
                @foreach($products as $product)
                <div class="compare-col compare-value">
                    <div class="product-price">
                        <span class="new-price">{{ formatCurrency($product->price) }}</span>
                        @if($product->compare_price)
                        <span class="old-price">{{ formatCurrency($product->compare_price) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
                @for($i = $products->count(); $i < 4; $i++) <div class="compare-col compare-value">-
            </div>
            @endfor
        </div>
        <!-- End of Compare Price -->

        <div class="compare-row cols-xl-5 cols-lg-4 cols-md-3 cols-2 compare-meta">
            <div class="compare-col compare-field">SKU</div>
            @foreach($products as $product)
            <div class="compare-col compare-value">{{ $product->sku ?? 'N/A' }}</div>
            @endforeach
            @for($i = $products->count(); $i < 4; $i++) <div class="compare-col compare-value">-
        </div>
        @endfor
    </div>

    <div class="compare-row cols-xl-5 cols-lg-4 cols-md-3 cols-2 compare-availability">
        <div class="compare-col compare-field">Availability</div>
        @foreach($products as $product)
        <div class="compare-col compare-value">
            <span class="badge {{ $product->quantity ? 'badge-success' : 'badge-danger' }}">
                {{ $product->quantity ? 'In Stock' : 'Out of Stock' }}
            </span>
        </div>
        @endforeach
        @for($i = $products->count(); $i < 4; $i++) <div class="compare-col compare-value">-
    </div>
    @endfor
    </div>

    @foreach($allSpecifications as $specKey)
    <div class="compare-row cols-xl-5 cols-lg-4 cols-md-3 cols-2 compare-spec">
        <div class="compare-col compare-field">{{ ucfirst(str_replace('_', ' ', $specKey)) }}</div>
        @foreach($products as $product)
        <div class="compare-col compare-value">
            {{ $product->specifications[$specKey] ?? '-' }}
        </div>
        @endforeach
        @for($i = $products->count(); $i < 4; $i++) <div class="compare-col compare-value">-
    </div>
    @endfor
    </div>
    @endforeach


    </div>
    @endif
    </div>
    <!-- End of Compare Table -->
    </div>
    <!-- End of Page Content -->
</main>
<!-- End of Main -->
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function removeFromComparison(productId) {
    if (!confirm('Are you sure you want to remove this product from comparison?')) {
        return;
    }

    fetch(`{{ route('compare.index') }}/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                const productElement = document.getElementById(`product-${productId}`);
                if (productElement) {
                    productElement.remove();
                }
                showToast(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function clearComparison() {
    if (!confirm('Are you sure you want to clear all comparisons?')) {
        return;
    }
    fetch(`{{ route('compare.clear') }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endsection