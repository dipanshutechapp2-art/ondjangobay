	<div class="sidebar-overlay"></div>
	<a class="sidebar-close" href="#"><i class="close-icon"></i></a>
	<div class="sidebar-content scrollable">
		<div class="sticky-sidebar">
			<div class="filter-actions mb-4">
				<label>Filter :</label>
				<a href="{{ url('/shop') }}" class="btn btn-dark btn-link" style="padding-bottom: 0;text-transform: capitalize;font-weight: 400;background-color: transparent;color: #333;border-color: #333;">Clean All</a>
			</div>

			<!-- Categories -->
			<!--<div class="widget widget-collapsible">
			  <h3 class="widget-title"><span>All Categories</span></h3>
			  <ul class="widget-body filter-items search-ul list-unstyled">
				@foreach ($categories as $category)
					@include('products.category-tree', [
						'category' => $category,
						'level' => 0,
						'selectedCategoryId' => $selectedCategoryId ?? null,
						'expandedCategoryIds' => $expandedCategoryIds ?? []
					])
				@endforeach
			  </ul>
			</div>-->

	


			<!-- Price Range Links -->
			<div class="widget widget-collapsible">
				<h3 class="widget-title"><span>Price</span></h3>
				<div class="widget-body">
					<ul class="filter-items search-ul mb-3">
						@php
							$priceRanges = [
								[0, 100],
								[100, 200],
								[200, 300],
								[300, 500],
							];
						@endphp

						@foreach ($priceRanges as $range)
							<li>
								<a href="{{ url('/shop') . '?' . http_build_query(array_merge(request()->except('page'), ['min_price' => $range[0], 'max_price' => $range[1]])) }}"
								   class="{{ request('min_price') == $range[0] && request('max_price') == $range[1] ? 'active' : '' }}">
									   {{getDefaultSelectedCurrency()}}{{ $range[0] }} - ${{ $range[1] }}
								</a>
							</li>
						@endforeach

						<li>
							<a href="{{ url('/shop') . '?' . http_build_query(array_merge(request()->except('page'), ['min_price' => 500])) }}"
							   class="{{ request('min_price') == 500 ? 'active' : '' }}">
								{{getDefaultSelectedCurrency()}}500+
							</a>
						</li>
					</ul>

					<!-- Custom Price Range Form -->
					<form method="GET" action="{{ url('/shop') }}" class="price-range d-flex align-items-center">
						<input type="number" name="min_price" class="min_price text-center me-2" placeholder="{{getDefaultSelectedCurrency()}}min" value="{{ request('min_price') }}">
						<span class="delimiter me-2">-</span>
						<input type="number" name="max_price" class="max_price text-center me-3" placeholder="{{getDefaultSelectedCurrency()}}max" value="{{ request('max_price') }}">

						<!-- Retain other filters -->
						<input type="hidden" name="category" value="{{ request('category') }}">
						<input type="hidden" name="brand" value="{{ request('brand') }}">
						<input type="hidden" name="sort" value="{{ request('sort') }}">

						<button type="submit" class="btn btn-primary btn-rounded">Go</button>
					</form>
				</div>
			</div>

			<!-- Brands -->

				<form method="GET" action="{{ url('/shop') }}" id="filterForm">
				  <!-- Categories and price filters... -->

				  <div class="widget widget-collapsible">
					<h3 class="widget-title">Brand</h3>
					<div class="widget-body">
					  @foreach ($brands as $brand)
						<div class="form-check mb-1">
						  <input class="form-check-input"
								 type="checkbox"
								 name="brands[]"
								 id="brand{{ $brand->id }}"
								 value="{{ $brand->id }}"
							{{ (is_array(request('brands')) && in_array($brand->id, request('brands'))) ? 'checked' : '' }}>
						  <label class="form-check-label" for="brand{{ $brand->id }}">
							{{ $brand->title }}
						  </label>
						</div>
					  @endforeach
					</div>
				  </div>

				  <button type="submit" class="btn btn-primary btn-sm">
					Apply Filters
				  </button>
				</form>
		</div>
	</div>
	
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-category').forEach(toggleBtn => {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const categoryItem = toggleBtn.closest('.category-item');
            const subTree = categoryItem?.querySelector('.subcategoryFilterTree');

            if (categoryItem && subTree) {
                categoryItem.classList.toggle('expanded');
                subTree.classList.toggle('show');

                const icon = toggleBtn.querySelector('.toggle-icon');
                if (icon) {
                    icon.classList.toggle('rotated');
                }
            }
        });
    });
});

</script>
<style>
.subcategoryFilterTree {
    display: none;
}
.subcategoryFilterTree.show {
    display: block;
}

.subcategoryFilterTree.toggle-icon {
    transition: transform 0.3s ease;
}
subcategoryFilterTree.toggle-icon.rotated {
    transform: rotate(90deg);
}


.category-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.toggle-category {
    flex-shrink: 0;
    padding-left: 8px;
}

.toggle-icon {
    transition: transform 0.3s ease;
}

.toggle-icon.rotated {
    transform: rotate(90deg);
}


</style>