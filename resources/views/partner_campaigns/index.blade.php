{{-- resources/views/partner_campaigns/index.blade.php --}}
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

    <style>
        /* SAME BLACK BADGE LIKE STATUS */
        .timer-badge {
            background: #000;
            color: #fff !important;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            min-width: 120px;
            text-align: center;
        }

	/* GLOBAL CLEAN LOOK */
	.campaign-section {
		background: #fff;
		border-radius: 12px;
		padding: 25px;
		margin-bottom: 40px;
		box-shadow: 0 6px 20px rgba(0,0,0,0.06);
	}

	/* HEADER CLEAN TITLE */
	.campaign-section-title {
		font-size: 26px;
		font-weight: 700;
		margin-bottom: 8px;
	}

	/* DESCRIPTION */
	.campaign-section p {
		font-size: 15px;
		color: #444;
	}

	/* TIMESTAMP */
	.start-end-time small {
		font-size: 13px;
		color: #777;
	}

	/* CATEGORY BOX CLEAN UI */
	.category-cart-box {
		background: #fafafa;
		border-radius: 12px;
		border: 1px solid #eee !important;
		padding: 20px;
		position: relative;
	}

	/* CATEGORY HEADER BAR */
	.category-cart-box h3 {
		font-size: 20px;
		font-weight: 700;
		margin: 0;
	}

	.category-cart-box small {
		font-size: 13px;
		color: #666;
	}

	/* VENDOR TITLE */
	.vendor-box h4 {
		font-size: 17px;
		font-weight: 700;
		margin-bottom: 10px;
		color: #333;
	}

	/* PRODUCT CARD */
	.product-card {
		background: #fff;
		transition: 0.3s;
		border: 1px solid #ddd !important;
		border-radius: 10px;
		padding: 10px;
	}

	.product-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(0,0,0,0.1);
	}

	/* PRODUCT NAME */
	.product-name {
		font-size: 15px;
		font-weight: 600;
		min-height: 40px;
	}

	/* PRICE */
	.product-price ins {
		font-size: 16px;
		font-weight: 700;
	}

	.product-price del {
		font-size: 13px;
		color: #888;
	}

	/* BUY NOW BUTTON */
	.btn-buy {
		background: #000;
		border-radius: 6px;
		padding: 8px 0;
		font-weight: 600;
		border: none;
		color: #fff;
	}

	.btn-buy:hover { background: #333; }

	/* QTY INPUT */
	.qty-input { border-radius: 6px; border: 1px solid #bbb; }

	/* progress styles */
	.progress { height: 12px; background-color: #e6e6e6; border-radius: 10px; overflow: hidden; }
	.progress-bar-test { background-color: #000; height: 100%; width: 0%; transition: width .6s ease; }
	
	/* small stat row */
	.cart-stats { display:flex; gap:12px; align-items:center; font-size:13px; color:#444; margin-top:10px; }

	/* closed overlay */
	.cart-closed-overlay {
		position: absolute;
		inset: 0;
		background: rgba(255,255,255,0.8);
		display:flex;
		align-items:center;
		justify-content:center;
		font-weight:700;
		color:#333;
		border-radius: 12px;
		font-size:18px;
		z-index: 5;
	}

	/* badge */
	.cart-closed-badge {
		background: #dc3545;
		color: #fff;
		padding: 6px 10px;
		border-radius: 6px;
		font-weight:700;
	}
    </style>

    <div class="page-content mb-10 pb-2">
        <section class="campaign-section-shop">
            <div class="container">

                @forelse($campaigns as $campaign)
                    @php
                        $campaignStart = \Carbon\Carbon::parse($campaign->start_date)->timestamp;
                        $campaignEnd   = \Carbon\Carbon::parse($campaign->end_date)->timestamp;
                        // cart limits from campaign
                        $cartMaxVolume = (int) ($campaign->cart_max_volume ?? 0);
                        $goalQuantity  = (int) ($campaign->goal_quantity ?? 0);
                    @endphp

                    <div class="campaign-section" id="campaign-{{ $campaign->id }}">

                        <!-- Campaign Header -->
                        <div class="mb-3">
							<h2 class="campaign-section-title">{{ $campaign->name }}</h2>

							@if(!empty($campaign->description))
								<p>{{ $campaign->description }}</p>
							@endif

							<div class="start-end-time mb-2">
								<small>
									Starts: {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }} |
									Ends: {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}
								</small>
							</div>

							<div class="mt-2">
								<strong>Status:</strong>
								<span class="campaign-timer timer-badge"
									  data-start="{{ $campaignStart }}"
									  data-end="{{ $campaignEnd }}">
									Loading...
								</span>
							</div>
						</div>

                        {{-- Category + Vendor + Products --}}
                        <div class="row product-listing-row campaign-listing-row">

                            @php
                                $grouped = $campaign->groupedProducts ?? collect();
                                $defaultImage = asset('/mnt/data/451a7efb-56ae-4dd2-b3dc-eebcb71072fc.png');
                            @endphp

                            @if($grouped->count() > 0)
                                @foreach($grouped as $categoryId => $vendorGroups)
                                    @php
                                        $category = \App\Models\Category::find($categoryId);
                                        // compute sold qty for this campaign + this category from partner_orders
                                        // join partner_products to filter by category
                                        $categorySold = \App\Models\PartnerOrder::join('partner_products', 'partner_orders.partner_product_id', '=', 'partner_products.id')
                                            ->where('partner_orders.partner_campaign_id', $campaign->id)
                                            ->where('partner_products.category_id', $categoryId)
                                            ->sum('partner_orders.quantity');

                                        // product-level sold (map used for JS fallback) - not strictly required here
                                        // compute percent toward goal (use campaign goal_quantity if set, else use cart_max_volume fallback)
                                        $targetForProgress = $goalQuantity > 0 ? $goalQuantity : ($cartMaxVolume > 0 ? $cartMaxVolume : 1);
                                        $progressPercent = $targetForProgress > 0 ? min(100, round(($categorySold / $targetForProgress) * 100, 2)) : 0;

                                        // remaining volume if cart_max_volume is set
                                        $remainingVolume = $cartMaxVolume > 0 ? max(0, $cartMaxVolume - $categorySold) : null;

                                        // cart is closed server-side if time passed OR remainingVolume == 0
                                        $cartClosedServer = (time() > $campaignEnd) || ($remainingVolume !== null && $remainingVolume <= 0);
                                    @endphp

                                    <div class="col-12">
                                        <div class="category-cart-box border p-3 mb-4"
                                             id="cart-{{ $campaign->id }}-{{ $categoryId }}"
                                             data-campaign-id="{{ $campaign->id }}"
                                             data-category-id="{{ $categoryId }}"
                                             data-campaign-end="{{ $campaignEnd }}"
                                             data-cart-max-volume="{{ $cartMaxVolume }}"
                                             data-category-sold="{{ $categorySold }}"
                                             data-goal-quantity="{{ $goalQuantity }}"
                                             data-server-closed="{{ $cartClosedServer ? 1 : 0 }}"
                                        >

                                            <!-- Category Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h3 class="mb-0">
                                                    {{ $category ? $category->name : 'Uncategorized' }}
                                                    <small class="text-muted"> ({{ collect($vendorGroups)->flatten(1)->count() }} products)</small>
                                                </h3>

                                                <div class="text-right">
                                                    <div style="margin-bottom:6px;">
                                                        <strong>Cart Timer:</strong>
                                                        <span class="cart-timer timer-badge" id="cart-timer-{{ $campaign->id }}-{{ $categoryId }}">
                                                            Loading...
                                                        </span>
                                                    </div>

                                                    <div class="cart-stats">
                                                        @if($goalQuantity > 0)
                                                            <div>
                                                                <small>Goal:</small><br>
                                                                <strong>{{ $goalQuantity }}</strong>
                                                            </div>
                                                        @endif

                                                        @if($cartMaxVolume > 0)
                                                            <div>
                                                                <small>Max Vol:</small><br>
                                                                <strong id="remaining-{{ $campaign->id }}-{{ $categoryId }}">{{ $remainingVolume }}</strong>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <small>Sold:</small><br>
                                                            <strong id="sold-{{ $campaign->id }}-{{ $categoryId }}">{{ $categorySold }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Progress bar (toward goal) --}}
                                            <div class="mb-3">
                                                <div class="progress">
                                                    <div class="progress-bar-test"
                                                         id="progress-{{ $campaign->id }}-{{ $categoryId }}"
                                                         role="progressbar"
                                                         aria-valuenow="{{ $progressPercent }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100"
                                                         style="width: {{ $progressPercent }}%;">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small>{{ $progressPercent }}% of target</small>
                                                    <small>{{ $categorySold }} / {{ $targetForProgress }}</small>
                                                </div>
                                            </div>

                                            {{-- Vendor → Products --}}
                                            @foreach($vendorGroups as $vendorId => $products)
                                                @php $vendor = \App\Models\User::find($vendorId); @endphp

                                                <div class="vendor-box mb-3">
                                                    <h4 class="mb-2 vendor-title">
                                                        Vendor: {{ $vendor ? $vendor->name : 'Vendor #'.$vendorId }}
                                                    </h4>

                                                    <div class="row">
                                                        @foreach($products as $product)
                                                            @php
                                                                // product sold (sum of partner_orders for this product)
                                                                $productSold = \App\Models\PartnerOrder::where('partner_product_id', $product->id)->sum('quantity');
                                                            @endphp

                                                            <div class="col-sm-4 col-md-3 mb-4">
                                                                <div class="product-card product text-center"
                                                                     data-product-id="{{ $product->id }}"
                                                                     data-max-qty="{{ $product->max_quantity ?? 0 }}"
                                                                     data-product-sold="{{ $productSold }}">

                                                                    <figure class="product-media mb-2">
                                                                        <img src="{{ $product->image ? asset($product->image) : $defaultImage }}"
                                                                             alt="{{ $product->name }}"
                                                                             class="img-fluid"
                                                                             style="height:160px; object-fit:cover;">
                                                                    </figure>

                                                                    <div class="product-details">
                                                                        <h4 class="product-name">{{ $product->name }}</h4>

                                                                        <div class="product-price mb-2">
                                                                            <ins class="new-price">{{ formatCurrency($product->new_price) }}</ins>
                                                                            @if($product->old_price > 0)
                                                                                <del class="old-price">{{ formatCurrency($product->old_price) }}</del>
                                                                            @endif
                                                                        </div>

                                                                        @php
                                                                            $discount = 0;
                                                                            if($product->old_price > 0){
                                                                                $discount = round((($product->old_price - $product->new_price) / $product->old_price) * 100);
                                                                            }
                                                                        @endphp

                                                                        <small>
                                                                            Discount: {{ $discount }}% |
                                                                            Stock:
                                                                            <span class="prod-qty" id="prod-qty-{{ $product->id }}">
                                                                                {{ $product->max_quantity ?? 0 }}
                                                                            </span>
                                                                        </small>

                                                                        <div class="mt-2">
                                                                            @if(($product->max_quantity ?? 0) > 0)
                                                                                <form method="GET"
                                                                                      action="{{ route('partner.checkout', $product->id) }}"
                                                                                      class="buy-now-form">
                                                                                    @csrf
                                                                                    <div class="campaign-btn-wrap d-flex">
                                                                                        <div class="col-4 p-0">
                                                                                            <input type="number" name="quantity"
                                                                                                   value="1" min="1"
                                                                                                   max="{{ $product->max_quantity }}"
                                                                                                   class="form-control text-center qty-input" required>
                                                                                        </div>
                                                                                        <div class="col-8 p-0">
                                                                                            <button type="submit" class="btn btn-primary btn-buy w-100">
                                                                                                Buy Now
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            @else
                                                                                <span class="badge bg-danger">Out of Stock</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                            @endforeach

                                            {{-- closed overlay server-side if closed --}}
                                            @if($cartClosedServer)
                                                <div class="cart-closed-overlay">Cart Closed</div>
                                            @endif

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
        </section>
    </div>
</main>

{{-- ========== TIMER & REALTIME LOGIC ========== --}}
<script>
(function(){

    // Format: Xd HH:MM:SS
    function formatTime(seconds) {
        if (seconds <= 0) return "0d 00:00:00";
        const d = Math.floor(seconds / 86400);
        const h = Math.floor((seconds % 86400) / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        return `${d}d ${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    function disableCartUI(cartEl){
        if(!cartEl) return;
        cartEl.style.opacity = '0.5';
        cartEl.querySelectorAll('.btn-buy, .qty-input, .buy-now-form input, .buy-now-form button').forEach(el=>{
            el.disabled = true;
        });
        if(!cartEl.querySelector('.cart-closed-overlay')){
            const overlay = document.createElement('div');
            overlay.className = 'cart-closed-overlay';
            overlay.innerText = 'Cart Closed';
            cartEl.appendChild(overlay);
        }
    }

    function enableCartUI(cartEl){
        if(!cartEl) return;
        cartEl.style.opacity = '1';
        cartEl.querySelectorAll('.btn-buy, .qty-input, .buy-now-form input, .buy-now-form button').forEach(el=>{
            el.disabled = false;
        });
        const existing = cartEl.querySelector('.cart-closed-overlay');
        if(existing) existing.remove();
    }

    function updateAll(){
        const now = Math.floor(Date.now()/1000);

        // update campaign timers
        document.querySelectorAll('.campaign-timer').forEach(el=>{
            const start = parseInt(el.dataset.start) || 0;
            const end = parseInt(el.dataset.end) || 0;
            if(now < start){
                el.innerText = 'Starts in: ' + formatTime(start - now);
            } else if(now >= start && now <= end){
                el.innerText = 'Ends in: ' + formatTime(end - now);
            } else {
                el.innerText = 'Campaign Ended';
                // hide entire campaign
                const camp = el.closest('.campaign-section');
                if(camp) camp.style.display = 'none';
            }
        });

        // update each category/cart box
        document.querySelectorAll('.category-cart-box').forEach(box=>{
            const campaignEnd = parseInt(box.dataset.campaignEnd) || 0;
            const cartMaxVolume = parseInt(box.dataset.cartMaxVolume) || 0;
            let categorySold = parseInt(box.dataset.categorySold) || 0;
            const goalQuantity = parseInt(box.dataset.goalQuantity) || 0;
            const serverClosed = box.dataset.serverClosed === '1';

            // If you later implement a realtime endpoint to fetch updated sold values,
            // you can replace dataset.categorySold here via AJAX. For now dataset is server-sent.
            // We'll also update DOM elements referencing sold/remaining/progress.

            const timerEl = box.querySelector('.cart-timer');
            const idParts = box.id.split('-'); // cart-{campaignId}-{categoryId}
            const campaignId = idParts[1];
            const categoryId = idParts[2];

            // time left
            const timeLeft = campaignEnd - now;
            timerEl.innerText = formatTime(timeLeft);

            // sold number element & remaining
            const soldEl = document.getElementById(`sold-${campaignId}-${categoryId}`);
            const remainingEl = document.getElementById(`remaining-${campaignId}-${categoryId}`);

            // update progress (toward goalQuantity or cartMaxVolume fallback)
            const targetForProgress = goalQuantity > 0 ? goalQuantity : (cartMaxVolume > 0 ? cartMaxVolume : 1);
            const progressPercent = targetForProgress > 0 ? Math.min(100, Math.round((categorySold / targetForProgress) * 10000)/100) : 0;
            const progressBar = document.getElementById(`progress-${campaignId}-${categoryId}`);
            if(progressBar){
                progressBar.style.width = progressPercent + '%';
                progressBar.setAttribute('aria-valuenow', progressPercent);
            }

            if(soldEl) soldEl.innerText = categorySold;
            if(remainingEl){
                if(cartMaxVolume > 0){
                    let remaining = cartMaxVolume - categorySold;
                    if(remaining < 0) remaining = 0;
                    remainingEl.innerText = remaining;
                } else {
                    remainingEl.innerText = '-';
                }
            }

            // close conditions:
            const timeExpired = timeLeft <= 0;
            const volumeExceeded = (cartMaxVolume > 0) && (categorySold >= cartMaxVolume);

            if(serverClosed || timeExpired || volumeExceeded){
                disableCartUI(box);
            } else {
                enableCartUI(box);
            }

        });

        // product-level stock UI (in case product max_quantity zero)
        document.querySelectorAll('.product-card').forEach(pc=>{
            const maxQty = parseInt(pc.dataset.maxQty || pc.getAttribute('data-max-qty')) || 0;
            const buyBtn = pc.querySelector('.btn-buy');
            const qtySpan = pc.querySelector('.prod-qty');
            if(maxQty <= 0){
                if(buyBtn){ buyBtn.disabled = true; buyBtn.innerText = 'Out of Stock'; buyBtn.classList.remove('btn-primary'); buyBtn.classList.add('btn-secondary'); }
                if(qtySpan) qtySpan.innerText = 0;
            } else {
                if(qtySpan) qtySpan.innerText = maxQty;
            }
        });

    }

    // Initial run
    updateAll();

    // update every second
    setInterval(updateAll, 1000);

    // OPTIONAL: Poll server for updated sold counts (recommended for real-time)
    // Endpoint idea: /api/partner-campaigns/{campaignId}/category-sold?category_ids=1,2,3
    // If you want I can add the controller route+api implementation. For now we rely on server-rendered numbers.
    /*
    setInterval(function(){
        // gather all campaign+category combos
        const payload = [];
        document.querySelectorAll('.category-cart-box').forEach(box=>{
            const campaignId = box.dataset.campaignId;
            const categoryId = box.datasetCategoryId || box.dataset.categoryId || null;
            if(campaignId && categoryId) payload.push({ campaignId, categoryId });
        });
        if(payload.length===0) return;
        // call your API and update box.dataset.categorySold and call updateAll() after response
    }, 10000);
    */

})();
</script>

@endsection
