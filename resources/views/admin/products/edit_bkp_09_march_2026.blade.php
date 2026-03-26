@extends('admin/layouts.backend')
@section('title', 'Update Product')
@section('content')
<style>
  .attr-row {
    margin-bottom: 15px;
  }
	.attribute-group {
		border: 1px solid #ccc;
		padding: 15px;
		margin-bottom: 10px;
		position: relative;
	}
	.variant-row {
		border: 1px dashed #ccc;
		padding: 10px;
		margin-bottom: 5px;
		display: flex;
		align-items: flex-end;
		gap: 10px;
		flex-wrap: wrap;
	}
	select option:disabled {
		background-color: #e9ecef; /* light gray */
		color: #6c757d; /* optional muted text */
	}
	.is-invalid {
		border-color: red !important;
	}
	
	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
		background-color: #0d6efd; /* Bootstrap primary */
		border-color: #0d6efd;
		color: #fff;
	}

	/* Optional: Remove the "x" close icon background */
	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
		color: #ffffff;
		margin-right: 4px;
	}

	.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
		color: #000;
	}
	.select2-container--bootstrap4 .select2-selection--multiple {
		min-height: 38px; /* matches Bootstrap .form-control height */
		padding: 6px 12px;
		border-radius: 0.375rem;
	}
</style>
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Update Product</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Update Product</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                  <x-sweet-alert />
                <div class="row">
                    <!-- Add Menu Form -->
                    <div class="col-md-12">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card mb-4">
										  <div class="card-header">
											<h3 class="card-title"> Update Product</h3>
											<a href="{{ route('admin.products.index') }}" class="btn btn-danger btn float-right">
													<i class="fas fa-arrow-left"></i> Back
												</a>
										  </div>
											@if ($errors->any())
												<div class="alert alert-danger">
													<ul>
														@foreach ($errors->all() as $error)
															<li>{{ $error }}</li>
														@endforeach
													</ul>
												</div>
											@endif
										  <div class="card-body">
											<form class="geniusform" onsubmit="return validateBeforeSubmit()" action="{{ route('admin.products.update',$product->id) }}" method="POST" enctype="multipart/form-data">
											 @csrf 
                                                 @method('PUT')											 
                                                <input type="hidden" name="product_id" value="{{ $product->id }}" required />											 
												<div class="form-group">
													<label for="inp-name">Name <span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="product_name" placeholder="Product Name" value="{{ $product->name }}" required>
													@error('product_name')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">Meta title</label>
													<input type="text" class="form-control" id="inp-email" name="meta_title" placeholder="Meta title" value="{{ $product->meta_title }}"  >
													@error('meta_title')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">Meta keyword </label>
													<input type="text" class="form-control" id="inp-email" name="meta_keyword" placeholder="Meta keyword" value="{{ $product->meta_keyword }}"  >
													@error('meta_keyword')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												
												<div class="form-group">
													<label for="inp-email">Meta Descriptions </label>
													<textarea class="form-control" id="inp-email" name="meta_description" placeholder="Meta description">{{ $product->meta_description }}</textarea>
													@error('meta_description')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">SKU <span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-email" name="sku" placeholder="Enter sku" value="{{ $product->sku }}" required >
													@error('sku')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">Price <span style="color:red;">*</span></label>
													<input type="number" class="form-control" id="inp-email" name="price" placeholder="Price" value="{{ $product->price }}"  required>
													@error('price')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">Quantity <span style="color:red;">*</span></label>
													<input type="number" class="form-control" id="inp-email" name="quantity" placeholder="Quantity" value="{{ $product->quantity }}"  required>
													@error('quantity')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-email">Vendor<span style="color:red;">*</span></label>
													<select id="vendor-select" name="vendor_id" class="form-control" onchange="handleVendorChange(this.value)" required>
													   <option value="">-Select-</option>
													    @foreach ($vendors as $vendor)
														    @if($product->seller_id==$vendor->id)
																<option value="{{ $vendor->id }}" selected>{{ $vendor->name }}</option>
															@else
															    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
														    @endif
														@endforeach
													</select>
												</div>
												<!-- Is Attributes and Varient Start-->
												<hr/>
												<div class="form-group">
													<div id="attribute-blocks"></div>
													<button type="button" id="defaultDisabled" class="btn btn-secondary mb-3" onclick="addAttributeGroup()" disabled>+ Add Attribute</button>
												</div>
												
												<hr/>
												<!-- Is Attributes and Varient End-->
												<div class="form-group">
													<label for="inp-email">Store <span style="color:red;">*</span></label>
													<select name="store_ids[]" class="form-control select2Stores" multiple required disabled>
														<!-- Stores will be loaded dynamically based on vendor selection -->
													</select>
													@error('store_ids')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												 <div class="form-group">
													<label for="inp-email">Brand<span style="color:red;">*</span></label>

													<select name="brand_id" class="form-control" required>
													   <option value="">Select Brand</option>
														@if($brands)
															@foreach($brands as $brand)
																<option value="{{$brand->id}}" @if($product->brand_id==$brand->id) selected @endif>{{$brand->title}}</option>
															@endforeach
														@endif
													</select>
													@error('brand_id')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>

											 	<div class="form-group">
													<label for="inp-email">Type <span style="color:red;">*</span></label>
													<select name="type" class="form-control" required>
													   <option value="local" @if($product->type=='local') selected @endif>Local</option>
													   <option value="used" @if($product->type=='used') selected @endif>Used</option>
													   <option value="imported" @if($product->type=='imported') selected @endif>Imported</option>
													</select>
													@error('type')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>

												<div class="form-group">
													<div class="mb-3">
														<label for="category" class="form-label">Categories</label>
														<select name="category_ids[]" id="category" class="form-control select2" multiple>
															<option value="">-- Select Categories --</option>
															@php
																$selectedCategories = $selectedCategories ?? [];
																echo renderCategoryOptions($categories, null, 0, $selectedCategories);
															@endphp
														</select>
													</div>
												</div>
												
												<!-- SPECIFICATION-->
												<div class="form-group">
												    @if($errors->has('specifications.*.key') || $errors->has('specifications.*.value'))
														<div class="text-danger mb-2">
															Please fill out all specification fields.
														</div>
													@endif
													<label for="inp-email">Specifications <span style="color:red;">*</span></label>
													
													<div id="specifications-wrapper">
														@php $i = 0; @endphp
														@if(!empty($product->specifications))
															@foreach($product->specifications as $key => $value)
																<div class="spec-row">
																	<div class="row mb-2">
																		<div class="col-md-5">
																			<input type="text" class="form-control spec-key" name="specifications[{{ $i }}][key]" value="{{ $key }}" placeholder="Specification Key" required />
																		</div>
																		<div class="col-md-5">
																			<input type="text" class="form-control spec-value" name="specifications[{{ $i }}][value]" value="{{ $value }}" placeholder="Specification Value" required />
																		</div>
																		<div class="col-md-2">
																			<button type="button" class="remove-spec btn btn-danger btn-sm w-10">X</button>
																		</div>
																	</div>
																</div>
																@php $i++; @endphp
															@endforeach
														@endif
													</div>
													<button type="button" id="add-spec" class="btn btn-secondary mb-3 mt-2">Add More</button>
												</div>
												
												<div class="form-group">
													<label for="inp-email">Short Descriptions </label>
													<textarea class="form-control" id="summernote" name="short_description" placeholder="Short description">{{ $product->short_description }}</textarea>
													@error('short_description')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												
												<div class="form-group">
													<label for="inp-email">Descriptions </label>
													<textarea class="form-control" id="summernote2" name="description" placeholder="Description">{{ $product->description }}</textarea>
													@error('description')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>

												<div class="form-group" >
												    <div class="form-group text-center" >
														@if($product->image)
															<img src="{{ url('/public/uploads/products') }}/{{ $product->image }}"  alt="" width="150">
														@else
															<img src="{{ url('admin/images/no-image.png') }}" alt="" width="150">
														@endif
													</div>
													<label for="exampleInputEmail1">{{ __('Upload image') }}  </label>
													<input id="s" type="file" class="form-control @error('image') is-invalid @enderror" name="image" placeholder="Select file..">
													@error('image')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
										<div class="form-group">
											<label for="select_files">{{ __('Upload Gallery Images') }} <span style="color:red;">*</span></label>
											
											<input id="select_files" type="file" class="form-control @error('galler_image.*') is-invalid @enderror" name="galler_image[]" multiple accept="image/*">
											
											@error('galler_image.*')
												<span class="invalid-feedback" role="alert">
													<strong>{{ $message }}</strong>
												</span>
											@enderror

											<div class="mt-3" style="display: flex; flex-wrap: wrap; gap: 10px;">
												@foreach($product->galleryImages as $image)
													<div style="position: relative;">
														<img src="{{ asset('/uploads/product/gallery') }}/{{$image->image}}" style="width:100px; height:100px; object-fit:cover; border-radius:5px; border:1px solid #ccc;">
				
														 <a href="{{ route('gallery.delete', $image->id) }}" style="position:absolute; top:0; right:0; background:red; color:#fff; padding:2px 5px; font-size:12px;">X</a>
													</div>
												@endforeach
											</div>

											<!-- New image preview before upload -->
											<div id="image_preview" class="mt-3" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
										</div>
														
												<button type="submit" id="submit-btn" class="btn btn-primary  ">Submit</button>
											</form>
										  </div>
										</div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
				</section>
			</div>
		</div>
      </section>
<script>
    let specIndex = {{ count($product->specifications ?? []) }};

    document.getElementById('add-spec').addEventListener('click', function () {
        const wrapper = document.getElementById('specifications-wrapper');
        const row = document.createElement('div');
        row.classList.add('spec-row');
        row.innerHTML = `
            <div class="row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="specifications[${specIndex}][key]" placeholder="Specification Key" />
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="specifications[${specIndex}][value]" placeholder="Specification Value" />
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-spec btn btn-danger btn-sm w-10">X</button>
                </div>
            </div>
        `;
        wrapper.appendChild(row);
        specIndex++;
    });

    // Use event delegation to catch dynamically added elements
    document.getElementById('specifications-wrapper').addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-spec')) {
            // Remove the entire row
            e.target.closest('.spec-row').remove();
        }
    });
</script>
@php

  $attributeValuesArray = $attributes->mapWithKeys(function ($attribute) {
    return [
      $attribute->id => $attribute->values->map(function ($value) {
        return [
          'id'    => $value->id,
          'value' => $value->value,
        ];
      })->values()->toArray(),
    ];
  })->toArray();


   $existingAttributess = $product->attributes->map(function($attribute) {
		return [
			'attribute_id' => $attribute->attribute_id,
			'variants' => $attribute->variants->map(function($variant) {
				return [
					'value'    => $variant->value,
					'price'    => $variant->price,
					'sku'      => $variant->sku,
					'stock'    => $variant->stock,
					'image'    => $variant->image,
				];
			})
		];
	});
	
@endphp

<script>
document.getElementById('select_files').addEventListener('change', function (e) {
    const preview = document.getElementById('image_preview');
    preview.innerHTML = ''; // Clear previous previews

    const files = e.target.files;

    if (files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.border = '1px solid #ccc';
                    img.style.borderRadius = '5px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<script>
// Define global variables
const allStores = @json($stores);
const attributeValues = @json(
  $attributes->mapWithKeys(function ($attribute) {
    return [
      $attribute->id => $attribute->values->map(function ($value) {
        return [
          'id'        => $value->id,
          'value'     => $value->value,
          'vendor_id' => $value->vendor_id
        ];
      })
    ];
  })
);
const allAttributes = @json($attributes);
const existingAttributes = @json($existingAttributess);
const selectedStores = @json($selectedStores);

let selectedVendorId = null;
let attributeIndex = 0;
let variantCounters = {};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Set the initial vendor ID
  selectedVendorId = document.getElementById('vendor-select').value;
  
  // If a vendor is already selected, enable the attribute button and load stores
  if (selectedVendorId) {
    document.getElementById('defaultDisabled').removeAttribute('disabled');
    document.querySelector('.select2Stores').removeAttribute('disabled');
    
    // Load stores for the selected vendor
    loadVendorStores(selectedVendorId);
    
    // Load existing attributes if any
    if (existingAttributes.length) {
      loadExistingAttributes(existingAttributes);
    }
  }
});

// Handle vendor change
function handleVendorChange(vendorID) {
  // Clear existing attributes
  document.getElementById('attribute-blocks').innerHTML = '';
  attributeIndex = 0;
  variantCounters = {};
  
  const select = document.getElementById('vendor-select');
  selectedVendorId = select.value || null;
  
  const storeSelect = document.querySelector('.select2Stores');
  const button = document.getElementById('defaultDisabled');
  
  if (vendorID) {
    button.removeAttribute('disabled');
    storeSelect.removeAttribute('disabled');
  } else {
    button.setAttribute('disabled','');
    storeSelect.setAttribute('disabled','');
    storeSelect.innerHTML = '';
  }
  
  // Load stores for the selected vendor
  if (vendorID) {
    loadVendorStores(vendorID);
  }
}

// Load vendor stores via AJAX
function loadVendorStores(vendorID) {
  const storeSelect = document.querySelector('.select2Stores');
  
  // Show loading state
  const loadingOption = document.createElement('option');
  loadingOption.text = 'Loading...';
  loadingOption.disabled = true;
  loadingOption.selected = true;
  storeSelect.innerHTML = '';
  storeSelect.appendChild(loadingOption);
  
  const baseUrl = "{{ url('/admin/') }}";
  
  fetch(`${baseUrl}/vendor-wise-store/${vendorID}`)
    .then(response => response.json())
    .then(data => {
      storeSelect.innerHTML = '';
      
      if (data.length === 0) {
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = 'No stores for this vendor';
        storeSelect.appendChild(option);
        return;
      }
      
      // Add stores to the select
      data.forEach(store => {
        const option = document.createElement('option');
        option.value = store.id;
        option.textContent = store.store_name;
        
        // Check if this store was previously selected
        if (selectedStores.includes(store.id)) {
          option.selected = true;
        }
        
        storeSelect.appendChild(option);
      });
      
      // Initialize Select2 if it exists
      if (typeof $ !== 'undefined' && $(storeSelect).hasClass('select2')) {
        $(storeSelect).trigger('change.select2');
      }
    })
    .catch(error => {
      console.error('Error fetching stores:', error);
      storeSelect.innerHTML = '<option disabled>Error loading stores</option>';
    });
}

// Add attribute group
function addAttributeGroup() {
  const idx = attributeIndex++;
  variantCounters[idx] = 0;

  // Build attribute options based on selected vendor
  let attributeOptions = `<option value="" disabled selected>Select Attribute</option>`;

  allAttributes.forEach(attr => {
    if (!selectedVendorId || attr.vendor_id == selectedVendorId) {
      attributeOptions += `<option value="${attr.id}">${attr.name}</option>`;
    }
  });

  const html = `
    <div class="attribute-group border p-3 mb-3 position-relative" id="attribute-${idx}">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <select name="attributes[${idx}][attribute_id]" class="form-control me-2 attribute-select" data-index="${idx}" onchange="handleAttributeChange()" required>
          ${attributeOptions}
        </select>
        <button type="button" class="btn btn-danger" onclick="removeAttributeThis(${idx})">x</button>
      </div>
      
      <div class="variants" id="variants-${idx}">
        ${variantRowHTML(idx)}
      </div>

      <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addVariantInput(${idx})">+ Add Variant</button>
    </div>
  `;

  document.getElementById('attribute-blocks').insertAdjacentHTML('beforeend', html);
  handleAttributeChange();
}

// Create variant row HTML
function variantRowHTML(attrIdx) {
  const varIdx = variantCounters[attrIdx]++;
  const previewId = `preview-${attrIdx}-${varIdx}`;
  return `
    <div class="variant-row border p-2 mb-2 d-flex align-items-end gap-2 flex-wrap" id="variant-${attrIdx}-${varIdx}">
      <div class="flex-grow-1">
        <div class="row">
          <div class="col-md-3">
            <select name="attributes[${attrIdx}][variants][${varIdx}][value]" class="form-control mb-1 variant-value-select" required>
              <option value="">Select Value</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="number" step="0.01" name="attributes[${attrIdx}][variants][${varIdx}][price]" placeholder="price" class="form-control mb-1" required>
          </div>
          <div class="col-md-2">
            <input type="text" name="attributes[${attrIdx}][variants][${varIdx}][sku]"  oninput="checkDuplicateSKUs(this)" placeholder="sku" class="form-control mb-1 variant-sku" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="attributes[${attrIdx}][variants][${varIdx}][stock]" placeholder="stock" class="form-control mb-1" required>
          </div>
          <div class="col-md-2">
            <img id="${previewId}" src="#" class="img-thumbnail mb-1 d-none" width="50">
            <input type="file" name="attributes[${attrIdx}][variants][${varIdx}][image]" accept="image/*" class="form-control mb-1" onchange="previewVariantImage(event, ${attrIdx}, ${varIdx})">
          </div>
          <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-btn" onclick="removeVariant(${attrIdx}, ${varIdx})">x</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

// Add variant input
function addVariantInput(attrIdx) {
  const container = document.getElementById(`variants-${attrIdx}`);
  const newVariantHTML = variantRowHTML(attrIdx);
  container.insertAdjacentHTML('beforeend', newVariantHTML);

  const selectAttribute = document.querySelector(`select.attribute-select[data-index="${attrIdx}"]`);
  const attributeId = selectAttribute.value;

  const variantRows = container.querySelectorAll('.variant-row');
  const lastRow = variantRows[variantRows.length - 1];
  const selectBox = lastRow.querySelector('.variant-value-select');

  // Populate variant value dropdown
  if (attributeValues[attributeId]) {
    attributeValues[attributeId].forEach(val => {
      const option = document.createElement('option');
      option.value = val.id; // IMPORTANT: use value ID, not value label
      option.textContent = val.value;
      selectBox.appendChild(option);
    });
  }

  // Enforce unique values after selection
  selectBox.addEventListener('change', function () {
    const selectedVal = this.value;
    const isDisabled = this.options[this.selectedIndex]?.disabled;

    // Prevent selecting a disabled value
    if (isDisabled) {
      alert("This value is already selected in another variant.");
      this.value = "";
      return;
    }

    enforceUniqueVariantValues(attrIdx);
  });

  // Call after slight delay to ensure DOM is updated before disabling
  setTimeout(() => {
    enforceUniqueVariantValues(attrIdx);
  }, 50);
}

// Remove attribute group
function removeAttributeThis(idx) {
  const el = document.getElementById(`attribute-${idx}`);
  if (el) el.remove();
  handleAttributeChange(); // Refresh available dropdown options
}

// Remove variant row
function removeVariant(attrIdx, varIdx) {
  const el = document.getElementById(`variant-${attrIdx}-${varIdx}`);
  if (el) el.remove();
  enforceUniqueVariantValues(attrIdx);
}

// Handle attribute change
function handleAttributeChange() {
  const selects = document.querySelectorAll('.attribute-select');
  const selectedValues = [];

  selects.forEach(select => {
    const val = select.value;
    if (val !== "") {
      selectedValues.push(val);
    }
  });

  selects.forEach(select => {
    const currentVal = select.value;
    Array.from(select.options).forEach(opt => {
      if (opt.value !== "") {
        opt.disabled = false;
      }
    });

    selectedValues.forEach(val => {
      if (val !== currentVal) {
        const option = select.querySelector(`option[value="${val}"]`);
        if (option) option.disabled = true;
      }
    });
  });

  selects.forEach(select => {
    const attrIdx = select.dataset.index;
    const attributeId = select.value;

    const variantContainer = document.getElementById(`variants-${attrIdx}`);
    if (!variantContainer) return;

    const variantRows = variantContainer.querySelectorAll('.variant-row');

    variantRows.forEach(row => {
      const selectBox = row.querySelector('.variant-value-select');
      if (!selectBox) return;

      const previouslySelected = selectBox.value; // Save selected value

      selectBox.innerHTML = '<option value="">Select Value</option>';

      if (attributeValues[attributeId]) {
        attributeValues[attributeId].forEach(val => {
          const option = document.createElement('option');
          option.value = val.id;
          option.textContent = val.value;
          selectBox.appendChild(option);
        });
      }

      if (previouslySelected) {
        selectBox.value = previouslySelected;
      }
    });

    enforceUniqueVariantValues(attrIdx);
  });
}

// Enforce unique variant values
function enforceUniqueVariantValues(attrIdx) {
  const container = document.getElementById(`variants-${attrIdx}`);
  const selects = container.querySelectorAll('.variant-value-select');

  const selectedValues = [];

  // Collect selected values
  selects.forEach(select => {
    if (select.value) {
      selectedValues.push(select.value);
    }
  });

  // Re-enable all options first
  selects.forEach(select => {
    Array.from(select.options).forEach(opt => {
      if (opt.value !== "") {
        opt.disabled = false;
      }
    });
  });

  // Disable selected values in other selects
  selects.forEach(currentSelect => {
    const currentVal = currentSelect.value;
    selectedValues.forEach(val => {
      if (val !== currentVal) {
        const opt = currentSelect.querySelector(`option[value="${val}"]`);
        if (opt) opt.disabled = true;
      }
    });
  });
}

// Load existing attribute data
function loadExistingAttributes(existingAttributes) {
  existingAttributes.forEach(attr => {
    const idx = attributeIndex++;
    variantCounters[idx] = 0;

    // Build attribute options based on selected vendor
    let attributeOptions = `<option value="" disabled>Select Attribute</option>`;
    
    allAttributes.forEach(a => {
      if (!selectedVendorId || a.vendor_id == selectedVendorId) {
        attributeOptions += `<option value="${a.id}" ${a.id == attr.attribute_id ? 'selected' : ''}>${a.name}</option>`;
      }
    });

    document.getElementById('attribute-blocks').insertAdjacentHTML('beforeend', `
      <div class="attribute-group border p-3 mb-3 position-relative" id="attribute-${idx}">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <select name="attributes[${idx}][attribute_id]" class="form-control me-2 attribute-select" data-index="${idx}" onchange="handleAttributeChange()" required>
            ${attributeOptions}
          </select>
          <button type="button" class="btn btn-danger" onclick="removeAttributeThis(${idx})">x</button>
        </div>
        <div class="variants" id="variants-${idx}"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addVariantInput(${idx})">+ Add Variant</button>
      </div>`);

    attr.variants.forEach(v => {
      addVariantInput(idx);

      const container = document.querySelector(`#variants-${idx} .variant-row:last-child`);
      const selectRow = container.querySelector('.variant-value-select');
      
      // Populate variant value dropdown
      if (attributeValues[attr.attribute_id]) {
        attributeValues[attr.attribute_id].forEach(val => {
          const option = document.createElement('option');
          option.value = val.id;
          option.textContent = val.value;
          selectRow.appendChild(option);
        });
        
        // Set the selected value
        selectRow.value = v.value;
      }

      container.querySelector(`input[name*="[price]"]`).value = v.price;
      container.querySelector(`input[name*="[sku]"]`).value = v.sku;
      container.querySelector(`input[name*="[stock]"]`).value = v.stock;

      if (v.image) {
        const imgPreview = container.querySelector('img');
        imgPreview.src = "{{ asset('/public/uploads/variant_images') }}/" + v.image;
        imgPreview.classList.remove('d-none');

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = container.querySelector(`input[type="file"]`).name.replace('[image]', '[existing_image]');
        hiddenInput.value = v.image;
        container.appendChild(hiddenInput);
      }
    });
  });

  handleAttributeChange();
}

// Check for duplicate SKUs
function checkDuplicateSKUs(inputElement) {
  const allSkuInputs = document.querySelectorAll('.variant-sku');
  const skuValues = {};
  let hasDuplicate = false;

  allSkuInputs.forEach(input => {
    const val = input.value.trim();
    if (val) {
      if (skuValues[val]) {
        skuValues[val].push(input);
        hasDuplicate = true;
      } else {
        skuValues[val] = [input];
      }
    }
  });

  // Clear all previous error states
  allSkuInputs.forEach(input => {
    input.classList.remove('is-invalid');
    input.title = '';
  });

  // Highlight duplicates
  Object.keys(skuValues).forEach(sku => {
    if (skuValues[sku].length > 1) {
      skuValues[sku].forEach(input => {
        input.classList.add('is-invalid');
        input.title = 'Duplicate SKU';
      });
    }
  });
}

// Validate before form submission
function validateBeforeSubmit() {
  checkDuplicateSKUs();
  const invalidFields = document.querySelectorAll('.variant-sku.is-invalid');
  if (invalidFields.length > 0) {
    alert("Duplicate SKUs found. Please fix them before submitting.");
    return false;
  }
  return true;
}

// Preview variant image
function previewVariantImage(event, attrIdx, varIdx) {
  const file = event.target.files[0];
  const previewId = `preview-${attrIdx}-${varIdx}`;
  const previewImg = document.getElementById(previewId);

  if (file && previewImg) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImg.src = e.target.result;
      previewImg.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  }
}
</script>

<!-- Select2 CSS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Categories --",
            width: '100%',
            theme: 'bootstrap4'
        });
		$('.select2Stores').select2({
            placeholder: "-- Select Stores --",
            width: '100%',
            theme: 'bootstrap4'
        });
    });
</script>
@endsection