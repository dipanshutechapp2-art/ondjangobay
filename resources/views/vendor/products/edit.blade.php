@extends('vendor/layouts.backend')
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
											<a href="{{ route('products.index') }}" class="btn btn-danger btn float-right">
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
											<form class="geniusform" onsubmit="return validateBeforeSubmit()" action="{{ route('products.update',$product->id) }}" method="POST" enctype="multipart/form-data">
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
													<label for="inp-email">Meta title </label>
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
													<label for="inp-email">Price ({{getDefaultSelectedCurrency()}})<span style="color:red;">*</span></label>
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
												
												<!-- Is Attributes and Varient Start-->
												@if(empty($product->auto_ds_product_id))
													<hr/>
													<div class="form-group">
														<div id="attribute-blocks"></div>
														<button type="button" class="btn btn-secondary mb-3" onclick="addAttributeGroup()">+ Add Attribute</button>
													</div>
													
													<hr/>
												@endif
												<!-- Is Attributes and Varient End-->
												<div class="form-group">
													<label for="inp-email">Store <span style="color:red;">*</span></label>
													<select name="store_ids[]" class="form-control select2Stores" multiple required>
														@foreach($stores as $store)
															<option value="{{ $store->id }}" @if(in_array($store->id, $selectedStores)) selected @endif>
																{{ $store->store_name }}
															</option>
														@endforeach
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
												
												@if(empty($product->auto_ds_product_id))
													
													<div class="form-group" >
														<div class="form-group text-center" >
															@if($product->image)
																<img src="{{ url('/public/uploads/products') }}/{{ $product->image }}"  alt="" width="150">
															@else
																<img src="{{ url('admin/images/no-image.png') }}" alt="" width="150">
															@endif
														</div>
														<label for="exampleInputEmail1">{{ __('Upload image') }}  </label>
														<input id="select_files" type="file" class="form-control @error('image') is-invalid @enderror" name="image" placeholder="Select file..">
														@error('image')
															<span class="invalid-feedback" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror
													</div>
												
													<div class="form-group">
														<label for="select_files">{{ __('Upload Gallery Images') }} <span style="color:red;"></span></label>
														
														
														<input id="select_files" type="file" class="form-control @error('galler_image.*') is-invalid @enderror" name="galler_image[]" multiple accept="image/*">
														
														@error('galler_image.*')
															<span class="invalid-feedback" role="alert">
																<strong>{{ $message }}</strong>
															</span>
														@enderror

														<div class="mt-3" style="display: flex; flex-wrap: wrap; gap: 10px;">
															@foreach($product->galleryImages as $image)
																<div style="position: relative;">
																	@if(!empty($product->auto_ds_product_id))
																	   <img src="{{$image->image}}" style="width:100px; height:100px; object-fit:cover; border-radius:5px; border:1px solid #ccc;">
																	@else
																	   <img src="{{ asset('/uploads/product/gallery') }}/{{$image->image}}" style="width:100px; height:100px; object-fit:cover; border-radius:5px; border:1px solid #ccc;">
																	@endif
							
																	 <a href="{{ route('gallery.delete', $image->id) }}" style="position:absolute; top:0; right:0; background:red; color:#fff; padding:2px 5px; font-size:12px;">X</a>
																</div>
															@endforeach
														</div>

														<!-- New image preview before upload -->
														<div id="image_preview" class="mt-3" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
													</div>
												@endif	
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
					'auto_ds_variant_id'    => $variant->auto_ds_variant_id,
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
const existingAttributes = @json($existingAttributess);
const attributeValues = @json(
  $attributes->mapWithKeys(function ($attribute) {
    return [
      $attribute->id => $attribute->values->map(function ($value) {
        return [
          'id'    => $value->id,
          'value' => $value->value,
        ];
      })
    ];
  })
);

let attributeIndex = 0;
let variantCounters = {};

// Create a new attribute group
function addAttributeGroup() {
  const idx = attributeIndex++;
  variantCounters[idx] = 0;

  const html = `
			<div class="attribute-group border p-3 mb-3 position-relative" id="attribute-${idx}">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<select name="attributes[${idx}][attribute_id]" class="form-control me-2 attribute-select" data-index="${idx}" onchange="handleAttributeChange()" required>
						<option value="" disabled selected>Select Attribute</option>
						@foreach ($attributes as $attribute)
							<option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
						@endforeach
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

// Create a new variant row for a group
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
							<input type="text" name="attributes[${attrIdx}][variants][${varIdx}][sku]" oninput="checkDuplicateSKUs(this)" placeholder="sku" class="form-control mb-1 variant-sku" required>
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


	function populateVariantDropdown(attributeId, selectBox, selectedValue = '') {
		
		selectBox.innerHTML = '<option value="">Select Value</option>';
		if (attributeValues[attributeId]) {
			attributeValues[attributeId].forEach(val => {
				const option = new Option(val.value, val.id);
				selectBox.add(option);
			});
			selectBox.value = selectedValue; 
		}
	}


	function addVariantInput(attrIdx) {
		
	  const container = document.getElementById(`variants-${attrIdx}`);
	  container.insertAdjacentHTML('beforeend', variantRowHTML(attrIdx));

	  const selectAttribute = document.querySelector(`select.attribute-select[data-index="${attrIdx}"]`);
	  const attributeId = selectAttribute.value;
	  const selectBox = container.querySelector('.variant-row:last-child .variant-value-select');

	  populateVariantDropdown(attributeId, selectBox, '');

	  selectBox.addEventListener('change', () => {
		if (selectBox.options[selectBox.selectedIndex]?.disabled) {
		  alert("This value is already selected.");
		  selectBox.value = '';
		  return;
		}
		enforceUniqueVariantValues();
	  });

	  setTimeout(enforceUniqueVariantValues, 50);
	}


	function handleAttributeChange() {
		
		const selects = document.querySelectorAll('.attribute-select');
		const selectedValues = [];

		selects.forEach(select => {
			const val = select.value;
			if (val !== "") selectedValues.push(val);
		});

		selects.forEach(select => {
			const currentVal = select.value;
			Array.from(select.options).forEach(opt => {
				if (opt.value !== "") opt.disabled = false;
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
				populateVariantDropdown(attributeId, selectBox, selectBox.value);
			});
		});
	}

// Remove attribute group
function removeAttributeThis(idx) {
  document.getElementById(`attribute-${idx}`).remove();
  handleAttributeChange();
}

// Remove variant row
function removeVariant(attrIdx, varIdx) {
  document.getElementById(`variant-${attrIdx}-${varIdx}`).remove();
  enforceUniqueVariantValues();
}

// Prevent duplicate SKUs
function checkDuplicateSKUs(input) {
  const skus = {};
  document.querySelectorAll('.variant-sku').forEach(el => {
    const v = el.value.trim();
    if (v) (skus[v] ||= []).push(el);
  });
  document.querySelectorAll('.variant-sku').forEach(el => el.classList.remove('is-invalid'));
  Object.values(skus).forEach(list => {
    if (list.length > 1) list.forEach(el => el.classList.add('is-invalid'));
  });
}

// Ensure unique variant values globally
function enforceUniqueVariantValues() {
  const allSelects = document.querySelectorAll('.variant-value-select');
  const selected = Array.from(allSelects).map(s => s.value).filter(Boolean);

  allSelects.forEach(s => {
    Array.from(s.options).forEach(opt => opt.disabled = false);
  });
  allSelects.forEach(s => {
    selected.forEach(val => {
      if (s.value !== val) {
        const opt = s.querySelector(`option[value="${val}"]`);
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

    document.getElementById('attribute-blocks').insertAdjacentHTML('beforeend', `
      <div class="attribute-group" id="attribute-${idx}">
        <div class="d-flex justify-content-between mb-2">
          <select name="attributes[${idx}][attribute_id]" class="attribute-select form-control" data-index="${idx}" onchange="handleAttributeChange()" required>
            <option value="" disabled>Select Attribute</option>
            @foreach ($attributes as $attribute)
            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
            @endforeach
          </select>
          &nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger remove-btn" onclick="removeAttributeThis(${idx})">x</button>
        </div>
        <div class="variants" id="variants-${idx}"></div>
        <button type="button" class="addVariantInput btn btn-sm btn-outline-primary mt-2" onclick="addVariantInput(${idx})">+ Add Attribute</button>
      </div>`);

    document.querySelector(`#attribute-${idx} .attribute-select`).value = attr.attribute_id;

    attr.variants.forEach(v => {
      addVariantInput(idx);

      const container = document.querySelector(`#variants-${idx} .variant-row:last-child`);
      const selectRow = container.querySelector('.variant-value-select');
      populateVariantDropdown(attr.attribute_id, selectRow, v.value);

      container.querySelector(`input[name*="[price]"]`).value = v.price;
      container.querySelector(`input[name*="[sku]"]`).value = v.sku;
      container.querySelector(`input[name*="[stock]"]`).value = v.stock;

      if (v.image) {
        const imgPreview = container.querySelector('img');
        //imgPreview.src = `/public/uploads/variant_images/${v.image}`;

		if (v.auto_ds_variant_id && v.auto_ds_variant_id !== '') {
			imgPreview.src = v.image;
		} else {
			imgPreview.src = "{{ asset('/public/uploads/variant_images') }}/" + v.image;
		}
		
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
  enforceUniqueVariantValues();
}

document.addEventListener('DOMContentLoaded', () => {
  if (existingAttributes.length) loadExistingAttributes(existingAttributes);
});
</script>



<script>
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

function validateBeforeSubmit() {
    checkDuplicateSKUs();
    const invalidFields = document.querySelectorAll('.variant-sku.is-invalid');
    if (invalidFields.length > 0) {
        alert("Duplicate SKUs found. Please fix them before submitting.");
        return false;
    }
    return true;
}

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
