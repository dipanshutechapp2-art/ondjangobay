@extends('vendor/layouts.backend')
@section('title', 'Update Store')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Update Store</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Update Store</li>
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
											<h3 class="card-title"> Update Store</h3>
											<a href="{{ route('admin.vendorstore.show') }}" class="btn btn-danger btn float-right">
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
											<form class="geniusform" action="{{ route('vendor.vendorstore.update',$store->slug) }}" method="POST" enctype="multipart/form-data">
											 @csrf 
                                                 @method('PUT')											 
                                                <input type="hidden" name="store_id" value="{{ $store->id }}" required /><div class="form-group" >


												    <div class="form-group text-center" >
														@if($store->logo)
															<img src="{{ url('/public/uploads/store') }}/{{ $store->logo }}"  alt="" width="150">
														@else
															<img src="{{ url('admin/images/no_store.png') }}" alt="" width="150">
														@endif
													</div>
													<label for="exampleInputEmail1">{{ __('Store logo') }}  </label>
													<input id="select_files" type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" placeholder="Select file..">
													@error('logo')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>			
												<div class="form-group">
													<label for="inp-name">Name <span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="store_name" placeholder="Store Name" value="{{ $store->store_name }}" required>
													@error('store_name')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-name">Description <span style="color:red;">*</span></label>
													<textarea class="form-control" id="inp-name" name="description" placeholder="Store Name" required>{{ $store->description }}</textarea>
													@error('description')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												
												<div class="form-group">
													<label for="title">Status</label>
													<select name="status" id="category_id" class="form-control" required>
														<option value="1" @if($store->status=='1') selected @endif>Active</option>
														<option value="0" @if($store->status=='0') selected @endif>Inactive</option>	
													</select>
													@error('status')
														<div class="text-danger">{{ $message }}</div>
													@enderror
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
@endsection
