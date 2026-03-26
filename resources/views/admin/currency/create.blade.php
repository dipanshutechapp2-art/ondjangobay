@extends('admin/layouts.backend')
@section('title', 'Add Currency')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Currency</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Currency</li>
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
											<h3 class="card-title"> Add Currency</h3>
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
											<form class="geniusform" action="{{ url('/admin/currency/add_currency_action') }}" method="POST" enctype="multipart/form-data">
											 @csrf 
                                                @method('POST')		
												<div class="form-group">
													<label for="inp-name">Code <span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="code" placeholder="Currency code" value="{{ old('code') }}" required>
													@error('code')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-name">Symbol<span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="symbol" placeholder="Currency Symbol" value="{{ old('symbol') }}" required>
													@error('symbol')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-name">Rate<span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="rate" placeholder="Currency Rate" value="{{ old('rate') }}" required>
													@error('rate')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-name">Display Order<span style="color:red;">*</span></label>
													<input type="number" class="form-control" id="inp-name" name="display_order" placeholder="0" value="{{ old('display_order',0) }}" required>
													@error('display_order')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label for="inp-name">Is Default<span style="color:red;">*</span></label><br/>
													Yes
													<input type="radio" id="inp-name" name="is_default" value="1">
													No
													<input type="radio" id="inp-name" name="is_default" checked value="0">
													
													@error('is_default')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
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
