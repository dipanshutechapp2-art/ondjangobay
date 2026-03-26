@php
    $hasChildren = $category->children->isNotEmpty();
    $isSelected = isset($expandedCategoryIds) && in_array($category->id, $expandedCategoryIds);
    $isExpanded = $isSelected;
@endphp

<li class="category-item {{ $hasChildren ? 'has-children' : '' }} {{ $isExpanded ? 'expanded' : '' }}">
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ url('/shop') . '?' . http_build_query(array_merge(request()->except('page'), ['category' => $category->id])) }}"
           class="category-link d-block flex-grow-1 {{ $isSelected ? 'text-primary fw-bold' : '' }}">
            {!! str_repeat('&nbsp;&nbsp;', $level ?? 0) !!} {{ $category->name }}
        </a>

		{{-- @if($hasChildren)
            <a href="javascript:void(0);" class="toggle-category ms-2">
                <i class="w-icon-angle-right toggle-icon {{ $isExpanded ? 'rotated' : '' }}"></i>
            </a>
        @endif --}}
		@if($hasChildren)
			<a href="javascript:void(0);" class="toggle-category ms-2">
				<span class="toggle-icon">
					{{ $isExpanded ? '−' : '+' }}
				</span>
			</a>
		@endif
    </div>

    @if ($hasChildren)
        <ul class="subcategoryFilterTree ps-3 mt-1 {{ $isExpanded ? 'show' : '' }}">
            @foreach ($category->children as $child)
                @include('products.category-tree', [
                    'category' => $child,
                    'level' => ($level ?? 0) + 1,
                    'selectedCategoryId' => $selectedCategoryId ?? null,
                    'expandedCategoryIds' => $expandedCategoryIds ?? []
                ])
            @endforeach
        </ul>
    @endif
</li>

