@extends('admin/layouts.backend')
@section('title', 'Products')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</li>
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
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12">
									<div class="card mb-4">
										{{-- Success Message --}}
										@if(session('success'))
											<div class="alert alert-success">{{ session('success') }}</div>
										@endif

										{{-- Error Validation --}}
										@if($errors->any())
											<div class="alert alert-danger">
												<ul class="mb-0">
													@foreach($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<div class="card-header">
										   <h3 class="card-title"> {{ isset($product) ? 'Edit Product' : 'Add New Product' }}</h3>
											<a href="{{ route('admin.product_commissions.index') }}" class="btn btn-danger btn float-right">
												<i class="fas fa-arrow-left"></i> Back
											</a>
										</div>
										<div class="card-body">
											<form method="POST" action="{{ isset($product) ? route('admin.product_commissions.update', $product->id) : route('admin.product_commissions.store') }}">
												@csrf
												@if(isset($product)) @method('PUT') @endif

												<div class="mb-3">
													<label class="form-label">Vendor</label>
													<select name="vendor_id" id="vendor_id" class="form-select form-control" required>
														<option value="">Select Vendor</option>
														@foreach($vendors as $vendor)
															@if(!empty($product->vendor_id))
																@if($vendor->vendor_id==$product->vendor_id)
																	<option value="{{ $vendor->vendor_id }}" selected>
																	{{ $vendor->name }}
																	</option>
																@else
																	<option value="{{ $vendor->vendor_id }}" {{ old('vendor_id', $product->vendor_id ?? '') == $vendor->vendor_id ? 'selected' : '' }}>
																		{{ $vendor->name }}
																	</option>
																@endif
															@else
																<option value="{{ $vendor->vendor_id }}" {{ old('vendor_id', $product->vendor_id ?? '') == $vendor->vendor_id ? 'selected' : '' }}>
																		{{ $vendor->name }}
																	</option>
															@endif
														
														@endforeach
													</select>
												</div>
												
												<div class="mb-3">
													<label class="form-label">Product</label>
													<select name="product_id" id="product_id" class="form-select form-control" required>
														<option value="">Select Product</option>
													</select>
													<input type="hidden" name="name" id="product_name" value="{{ $product->name ?? old('name') }}" required>
												</div>
											
												<div class="mb-3">
													<label class="form-label">Commission Type</label>
													<select name="commission_type" class="form-select form-control">
														<option value="global" {{ old('commission_type', $product->commission_type ?? '') == 'global' ? 'selected' : '' }}>Use Global</option>
														<option value="custom" {{ old('commission_type', $product->commission_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
													</select>
												</div>

												<div class="mb-3">
													<label class="form-label">Commission Value (%)</label>
													<input type="number" step="0.01" name="commission_value" min="0" max="100"
														value="{{ old('commission_value', $product->commission_value ?? '') }}" class="form-control">
												</div>

												<!--<div class="mb-3">
													<label class="form-label">Origin</label>
													<input type="text" name="origin" value="{{ old('origin', $product->origin ?? '') }}" class="form-control">
												</div>

												<div class="mb-3">
													<label class="form-label">Shipping Condition</label>
													<input type="text" name="shipping_condition" value="{{ old('shipping_condition', $product->shipping_condition ?? '') }}" class="form-control">
												</div>-->

												<button type="submit" class="btn btn-success">Save Product</button>
												<a href="{{ route('admin.product_commissions.index') }}" class="btn btn-secondary">Cancel</a>
											</form>
										</div>
									</div> 
								</div>
							</div>
						</div>
					</div>
				</div>      
			</div>
		</section>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    function loadVendorProducts(vendorID, selectedProductID = null) {
        if(vendorID){
            $('#product_id').html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ url('admin/vendor-products') }}/" + vendorID,
                type: 'GET',
                success: function(data){
                    var options = '<option value="">Select Product</option>';
                    $.each(data, function(key, product){
                        options += '<option value="'+product.id+'" data-name="'+product.name+'"';
                        if(selectedProductID && selectedProductID == product.id){
                            options += ' selected';
                            $('#product_name').val(product.name);
                        }
                        options += '>'+product.name+'</option>';
                    });
                    $('#product_id').html(options);
                },
                error: function(){
                    alert('Unable to fetch products.');
                }
            });
        } else {
            $('#product_id').html('<option value="">Select Product</option>');
            $('#product_name').val('');
        }
    }

    // ✅ Load products dynamically when vendor changes
    $('#vendor_id').change(function(){
        var vendorID = $(this).val();
        $('#product_name').val('');
        loadVendorProducts(vendorID);
    });

    // ✅ Update hidden input when product changes
    $('#product_id').change(function(){
        var productName = $('#product_id option:selected').data('name') || '';
        $('#product_name').val(productName);
    });

    // ✅ Handle edit mode (preload vendor & product)
    @if(isset($product) && !empty($product->vendor_id))
        let vendorID = "{{ $product->vendor_id }}";
        let selectedProductID = "{{ $product->product_id }}";
        loadVendorProducts(vendorID, selectedProductID);
    @endif
});
</script>




@endsection
