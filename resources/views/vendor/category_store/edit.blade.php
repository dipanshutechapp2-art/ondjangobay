@extends('vendor/layouts.backend')
@section('title', 'Update Category Store')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Update Category Store</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Update Category Store</li>
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
											<h3 class="card-title"> Update Category Store</h3>
											<a href="{{ route('vendor.categorystore.show') }}" class="btn btn-danger btn float-right">
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
											<form class="geniusform" action="{{ route('vendor.categorystore.update',$categoryStore->id) }}" method="POST" enctype="multipart/form-data">
											 @csrf 
                                                 @method('PUT')											 
                                                <input type="hidden" name="category_store_id" value="{{ $categoryStore->id }}" required /><div class="form-group" >

													<div class="form-group">
														<label for="category_id">Select Categories</label>
														<select name="category_id[]" id="category_id" class="form-control select2Stores" multiple required>
															<option value="">-- Select Categories --</option>
															@if(!empty($categories))
																@php
																	// convert stored comma separated IDs into array
																	$selectedCategories = explode(',', $categoryStore->category_id ?? '');
																@endphp
																@foreach($categories as $category)
																	<option value="{{ $category->id }}"
																		{{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}>
																		{{ $category->name }}
																	</option>
																@endforeach
															@endif
														</select>
													</div>
												
													<div class="form-group">
														<label for="store_id">Select Store</label>
														<select name="store_id" id="store_id" class="form-control" required>
															<option value="">-- Select Store --</option>
															@foreach($stores as $store)
																@if($store->id==$categoryStore->store_id)
																	<option value="{{ $store->id }}" selected>{{ $store->store_name }}</option>	
																@else
																	<option value="{{ $store->id }}">{{ $store->store_name }}</option>
																@endif
															@endforeach
														</select>
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
