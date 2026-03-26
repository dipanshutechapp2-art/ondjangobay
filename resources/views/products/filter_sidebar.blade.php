<div class="sidebar-overlay"></div>
<a class="sidebar-close" href="#"><i class="close-icon"></i></a>
<div class="sidebar-content scrollable">
    <div class="sticky-sidebar">
        <div class="filter-actions filter-actions-title">
            <label>Filter :</label>
            <a href="{{ url('/shop') }}" class="btn btn-dark btn-link" style="padding-bottom: 0;text-transform: capitalize;font-weight: 400;background-color: transparent;color: #333;border-color: #333;">Clean All</a>
        </div>

        <!-- Categories -->
        <div class="widget widget-collapsible">
            <h3 class="widget-title toggle-widget">
                <span>All Categories</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
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
        </div>

        <!-- Price Range Links -->
        <div class="widget widget-collapsible">
            <h3 class="widget-title toggle-widget">
                <span>Price</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
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
                            {{getDefaultSelectedCurrency()}}{{ $range[0] }} - {{getDefaultSelectedCurrency()}}{{ $range[1] }}
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
                    @foreach(request()->except('page', 'min_price', 'max_price') as $key => $value)
                    @if(is_array($value))
                    @foreach($value as $val)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                    @endforeach
                    @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                    @endforeach

                    <!-- Preserve filters -->
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
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

                    <button type="submit" class="btn btn-primary btn-rounded">Go</button>
                </form>
            </div>
        </div>

        <!-- Brands -->
        <form method="GET" action="{{ url('/shop') }}" id="filterForm">
            <div class="widget widget-collapsible">
              <h3 class="widget-title toggle-widget">
                <span>Brand</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
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

            <!-- Availability Filter -->
            <div class="widget widget-collapsible">
                <h3 class="widget-title toggle-widget">
                <span>Availability</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
                <div class="widget-body">
                    <div class="form-check mb-1">
                        <input class="form-check-input"
                            type="checkbox"
                            name="availability[]"
                            id="in_stock"
                            value="in_stock"
                            {{ (is_array(request('availability')) && in_array('in_stock', request('availability'))) ? 'checked' : '' }}>
                        <label class="form-check-label" for="in_stock">
                            In Stock
                        </label>
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input"
                            type="checkbox"
                            name="availability[]"
                            id="out_of_stock"
                            value="out_of_stock"
                            {{ (is_array(request('availability')) && in_array('out_of_stock', request('availability'))) ? 'checked' : '' }}>
                        <label class="form-check-label" for="out_of_stock">
                            Out of Stock
                        </label>
                    </div>
                </div>
            </div>

            <!-- Product Type/Condition Filter -->
            <div class="widget widget-collapsible">
                <h3 class="widget-title toggle-widget">
                <span>Condition</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
                <div class="widget-body">
                    @php
                    $productTypes = [
                    'new' => 'New',
                    'used' => 'Used',
                    'refurbished' => 'Refurbished',
                    'local' => 'Local',
                    'imported' => 'Imported'
                    ];
                    @endphp

                    @foreach($productTypes as $value => $label)
                    <div class="form-check mb-1">
                        <input class="form-check-input"
                            type="checkbox"
                            name="types[]"
                            id="type_{{ $value }}"
                            value="{{ $value }}"
                            {{ (is_array(request('types')) && in_array($value, request('types'))) ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Rating Filter -->
            <div class="widget widget-collapsible rating-filter">
                <h3 class="widget-title toggle-widget">
                <span>Rating</span>
                <span class="widget-toggle-icon">+</span>
            </h3>
                <div class="widget-body">
                    @php
                    $ratings = [
                    5 => '5 Stars',
                    4 => '4 Stars & Up',
                    3 => '3 Stars & Up',
                    2 => '2 Stars & Up',
                    1 => '1 Star & Up'
                    ];
                    @endphp

                    @foreach($ratings as $value => $label)
                    <div class="form-check mb-1">
                        <input class="form-check-input"
                            type="radio"
                            name="min_rating"
                            id="rating{{ $value }}"
                            value="{{ $value }}"
                            {{ request('min_rating') == $value ? 'checked' : '' }}>
                        <label class="form-check-label" for="rating{{ $value }}">
                            <span class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $value ? ' text-warning' : ' text-light' }}"></i>
                                    @endfor
                            </span>
                            <span class="ms-2">{{ $label }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Preserve filters -->
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
            <input type="hidden" name="max_price" value="{{ request('max_price') }}">

            <button type="submit" class="btn btn-primary btn-sm">
                Apply Filters
            </button>
        </form>
    </div>
</div>


<script>
    // sub catagery js 
 document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-category').forEach(toggleBtn => {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();

                const categoryItem = toggleBtn.closest('.category-item');
                const subTree = categoryItem?.querySelector('.subcategoryFilterTree');
                const icon = toggleBtn.querySelector('.toggle-icon');

                if (!categoryItem || !subTree || !icon) return;

                const isOpen = categoryItem.classList.toggle('expanded');
                subTree.classList.toggle('show', isOpen);

                // PLUS / MINUS
                icon.textContent = isOpen ? '−' : '+';
            });
        });
    });


// catagerory js 
    document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.toggle-widget').forEach(title => {
        title.addEventListener('click', function () {

            const widget = this.closest('.widget-collapsible');
            const icon = this.querySelector('.widget-toggle-icon');

            widget.classList.toggle('open');

            icon.textContent =
                widget.classList.contains('open') ? '−' : '+';
        });
    });

});
</script>



