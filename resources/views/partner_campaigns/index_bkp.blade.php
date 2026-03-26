@extends('layouts.app_inner')
@section('title', 'My Community deal Sócia')
@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb mb-6">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>My Community deal Sócia</li>
            </ul>
        </div>
    </nav>

    <div class="page-content mb-10 pb-2">
        <div class="container">

            @forelse($campaigns as $campaign)
                <div class="campaign-section mb-5 p-3 border rounded shadow-sm">
                    <!-- Campaign Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h2 class="category-section-title">{{ $campaign->name }}</h2> 
                            <p>{{ $campaign->description }}</p>
                            <div>
                                <small>
                                    Starts: {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }} |
                                    Ends: {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}
                                </small>
                            </div>
                            <div class="mt-2">
                                <strong>Status: </strong>
                                <span class="campaign-timer" 
                                      data-start="{{ \Carbon\Carbon::parse($campaign->start_date)->timestamp }}" 
                                      data-end="{{ \Carbon\Carbon::parse($campaign->end_date)->timestamp }}">
                                    Loading...
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="row product-listing-row">
                        @php
                            $products = $campaign->products->where('status', 'approved');
                        @endphp

                        @if($products && $products->count() > 0)
                            @foreach($products as $product)
                                <div class="product-cols col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                                    <div class="product text-center market-place-product-grid border rounded p-2 h-100">
                                        <figure class="product-media mb-2">
                                            <img src="{{ $product->image ? asset($product->image) : asset('assets/images/default-campaign.jpg') }}" 
                                                     alt="{{ $product->name }}" class="img-fluid">
                                            
                                            @if($product->max_quantity <= 0)
                                                <h4 class="out-of-stock mt-2">Out of stock</h4>
                                            @endif
                                        </figure>

                                        <div class="product-details">
                                            <h4 class="product-name">{{ $product->name }}</h4>

                                            <div class="product-price mb-2">
                                                <ins class="new-price">{{ formatCurrency($product->new_price ?? 0) }}</ins>
                                                @if($product->old_price > 0)
                                                    <del class="old-price">{{ formatCurrency($product->old_price) }}</del>
                                                @endif
                                            </div>

                                            <!-- Buy Now Form -->
                                            @if($product->max_quantity > 0)
												<form method="GET" action="{{ route('partner.checkout', $product->id) }}">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <input type="number" name="quantity" value="1" min="{{ $product->min_quantity }}" 
                                                               max="{{ $product->max_quantity ?? 10 }}" class="form-control text-center">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100">Buy Now</button>
                                                </form>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center">
                                <p>No approved products in this campaign yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p>No active campaigns found.</p>
            @endforelse

        </div>
    </div>
</main>

<script>
function formatTime(seconds) {
    const d = Math.floor(seconds / (3600*24));
    const h = Math.floor((seconds % (3600*24)) / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return `${d}d ${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
}

function updateTimers() {
    const timers = document.querySelectorAll('.campaign-timer');
    const now = Math.floor(Date.now() / 1000);

    timers.forEach(timer => {
        const start = parseInt(timer.dataset.start);
        const end = parseInt(timer.dataset.end);

        if(now < start){
            timer.textContent = 'Starts in: ' + formatTime(start - now);
        } else if(now >= start && now <= end){
            timer.textContent = 'Ends in: ' + formatTime(end - now);
        } else {
            timer.textContent = 'Campaign ended';
        }
    });
}

setInterval(updateTimers, 1000);
updateTimers();
</script>
@endsection
