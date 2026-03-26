@extends('vendor/layouts.backend')
@section('title', 'Add Category Store')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Category Store</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Category Store</li>
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
											<h3 class="card-title"> Add Category Store</h3>
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
											@if(session()->has('success'))
												<div class="alert alert-success">
													<strong>Success!</strong> {{ session()->get('success') }}
												</div>
											@endif
											@if(session()->has('error'))
												<div class="alert alert-danger">
													<strong>Error!</strong> {{ session()->get('error') }}
												</div>
											@endif
											<form class="geniusform" action="{{ route('vendor.categorystore.create') }}" method="POST" enctype="multipart/form-data">
											 @csrf 
                                                @method('POST')		
                                                <div class="form-group">
													<label for="category_id">Select Category</label>
													<select name="category_id[]" id="category_id" class="form-control select2Stores" multiple  required>
														<option value="">-- Select Category --</option>
														@if(!empty($categories))
															@foreach($categories as $category)
																<option value="{{$category->id}}">{{$category->name}}</option>
															@endforeach
														@endif
													</select>
												</div>
												
												<!-- Store Multi-Select Dropdown -->
												<div class="form-group">
													<label for="store_id">Select Store</label>
													<select name="store_id" id="store_id" class="form-control" required>
														<option value="">-- Select Store --</option>
														@foreach($stores as $store)
															<option value="{{ $store->id }}">{{ $store->store_name }}</option>
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
