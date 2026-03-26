@extends('admin/layouts.backend')
@section('title', 'Vendor Commission')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ isset($vendor) ? 'Update Vendor Commission' : 'Add Vendor Commission' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">{{ isset($vendor) ? 'Update Vendor Commission' : 'Add Vendor Commission' }}</li>
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
										   <h3 class="card-title"> {{ isset($vendor) ? 'Update Vendor Commission' : 'Add Vendor Commission' }}</h3>
											<a href="{{ url('/admin/vendor-commissions') }}" class="btn btn-danger btn float-right">
												<i class="fas fa-arrow-left"></i> Back
											</a>
										</div>
										<div class="card-body">
											<form method="POST" action="{{ isset($vendor) ? route('admin.vendor_commissions.update', $vendor->id) : route('admin.vendor_commissions.store') }}">
												@csrf
												@if(isset($vendor))
													@method('PUT')
												@endif
												
												<div class="mb-3">
													<label class="form-label">Vendor</label>
													<input type="hidden" name="name" id="vendorName" class="form-control" value="{{ $vendor->name ?? old('name') }}" required>
													<select name="vendor_id" id="vendorSelect" class="form-control" required>
													    <option value="">Select</option>
													    @if(!empty($vendorsList))
															@foreach($vendorsList as $vendorData)
														        @if(!empty($vendor->vendor_id))
																	@if($vendorData->id==$vendor->vendor_id)
																	  <option value="{{$vendorData->id}}" selected>{{$vendorData->name ?? ""}}</option>
																	@else
																	  <option value="{{$vendorData->id}}">{{$vendorData->name ?? ""}}</option>
																	@endif
																@else
																	 <option value="{{$vendorData->id}}">{{$vendorData->name ?? ""}}</option>
																@endif
															@endforeach
													    @endif
													</select>
												</div>
												<div class="mb-3">
													<label class="form-label">Category</label>
													<select name="category_code" class="form-select form-control" required>
														<option value="internal" {{ old('category_code', $vendor->category_code ?? '') == 'internal' ? 'selected' : '' }}>Internal</option>
														<option value="external" {{ old('category_code', $vendor->category_code ?? '') == 'external' ? 'selected' : '' }}>External</option>
													</select>
												</div>

												<div class="mb-3">
													<label class="form-label">Commission Type</label>
													<select name="commission_type" class="form-select form-control" required>
														<option value="global" {{ old('commission_type', $vendor->commission_type ?? '') == 'global' ? 'selected' : '' }}>Use Global</option>
														<option value="custom" {{ old('commission_type', $vendor->commission_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
													</select>
												</div>

												<div class="mb-3">
													<label class="form-label">Commission Value (%)</label>
													<input type="number" name="commission_value" step="0.01" min="0" max="100"
														value="{{ old('commission_value', $vendor->commission_value ?? '') }}" class="form-control">
												</div>

												<!--<div class="mb-3">
													<label class="form-label">Bank Account</label>
													<input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $vendor->bank_account ?? '') }}">
												</div>-->

												<button type="submit" class="btn btn-success">Save</button>
												<a href="{{ route('admin.vendor_commissions.index') }}" class="btn btn-secondary">Cancel</a>
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
	<script>
document.addEventListener('DOMContentLoaded', function() {
    const vendorSelect = document.getElementById('vendorSelect');
    const vendorNameInput = document.getElementById('vendorName');
	
    vendorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
		if(selectedOption.text.trim()!=='Select'){
           vendorNameInput.value = selectedOption.text.trim();
		}else{
			vendorNameInput.value = '';
		}
    });
});
</script>
@endsection
