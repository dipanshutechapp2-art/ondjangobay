@extends('layouts.app_inner')
@section('title', 'Update Address')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Update Address</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Update Address</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content pt-2">
                <div class="container">
                    <div class="tab tab-vertical row gutter-lg">
                       @include('account.sidebar')
                        <div class="tab-content mb-6">
                            <div class="tab-pane active in">
                                @if (session('success'))
									<div class="alert alert-success alert-dismissible fade show" role="alert">
										<strong>Success!</strong> {{ session('success') }}
									</div><br/>
								@endif

								@if (session('error'))
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Error!</strong> {{ session('error') }}
									</div><br/>
								@endif

								@if ($errors->any())
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Please fix the following errors:</strong>
										<ul class="mt-2 mb-0">
											@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div><br/>
								@endif
								@if(!empty($addressInfo))
									<div class="icon-box icon-box-side icon-box-light">
										<span class="icon-box-icon icon-map-marker">
											<i class="w-icon-map-marker"></i>
										</span>
										<div class="icon-box-content">
											<h4 class="icon-box-title mb-0 ls-normal">Address</h4>
										</div>
									</div><br/>
									
									<div class="row">
									
										<div class="col-sm-12 mb-6">
											<div class="ecommerce-address billing-address pr-lg-8">
												<h4 class="title title-underline ls-25 font-weight-bold">Update Address</h4>
												
												<form class="form account-details-form" action="{{ url('/account/address/update_address_action')}}" method="post">
												   @CSRF
                                                   <input type="hidden" name="address_id" value="{{$addressInfo->id}}"/>												   
												    <div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="firstname">First name *</label>
																<input type="text" id="firstname" name="firstname" placeholder="First name" class="form-control form-control-md" value="{{$addressInfo->first_name}}" required>
																											</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Last name *</label>
																<input type="text" id="lastname" name="lastname" placeholder="Last name" class="form-control form-control-md" value="{{$addressInfo->last_name}}" required>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Company </label>
																<input type="text" id="company" name="company" placeholder="Company name"value="{{$addressInfo->company}}" class="form-control form-control-md">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>Country</label>
																<div class="select-box">
																	<select name="country" class="form-control form-control-md">
																		<option value="">-Select-</option>
																		@if(!empty($country))
																			@foreach($country as $countryList)
																				@if($countryList->name==$addressInfo->country)
																					<option value="{{$countryList->name}}" selected>{{$countryList->name}}</option>
																				@else
																					<option value="{{$countryList->name}}">{{$countryList->name}}</option>
																				@endif
																			@endforeach
																		@endif
																	</select>
																	@error('billing_country')
																		<span class="invalid-feedback" role="alert">
																			<strong>{{ $message }}</strong>
																		</span>
																	@enderror
																	
																</div>
															</div>
														</div>
														
														<div class="col-md-6">
															<div class="form-group">
																<label>State *</label>
																<div class="select-box">
																	<select name="state" class="form-control form-control-md" required>
																		 <option value="">-Select-</option>
																		 @if(!empty($state))
																			@foreach($state as $stateList)
																		        @if($stateList->name==$addressInfo->state)
																					<option value="{{$stateList->name}}" selected>{{$stateList->name}}</option>
																				@else
																			       <option value="{{$stateList->name}}">{{$stateList->name}}</option>
																			    @endif
																			@endforeach
																		@endif
																	</select>
																	@error('billing_state_id')
																		<span class="invalid-feedback" role="alert">
																			<strong>{{ $message }}</strong>
																		</span>
																	@enderror
																</div>
															</div>
														</div>
														
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Address </label>
																<input type="text" id="address_1" name="address_1" placeholder="Address 1" class="form-control form-control-md" value="{{$addressInfo->address_1}}" required>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Address 2</label>
																<input type="text" id="address_2" name="address_2" placeholder="Address 2" value="{{$addressInfo->address_2}}" class="form-control form-control-md">
															</div>
														</div>
														
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">City</label>
																<input type="text" id="city" name="city" placeholder="City" class="form-control form-control-md" value="{{$addressInfo->city}}">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Zipcode</label>
																<input type="text" id="zipcode" name="zipcode" placeholder="Zipcode" value="{{$addressInfo->zipcode}}" class="form-control form-control-md">
															</div>
														</div>
														
														<div class="col-md-6">
															<div class="form-group">
																<label for="lastname">Phone</label>
																<input type="number" id="zipcode" name="phone" placeholder="Phone" class="form-control form-control-md" value="{{$addressInfo->phone}}" required>
															</div>
														</div>
													</div>
													<button type="submit" class="btn btn-dark btn-rounded btn-sm mb-4">Save Changes</button>
												</form>
											</div>
										</div>
									
									</div>
								@else
									<p>No any address.</p><br/>
								@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
@endsection
        