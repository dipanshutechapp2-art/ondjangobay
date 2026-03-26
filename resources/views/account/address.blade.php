@extends('layouts.app_inner')
@section('title', 'Addresses')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Address</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Address</li>
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
								
								@if($address->isNotEmpty())
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
									<div class="icon-box icon-box-side icon-box-light">
										<span class="icon-box-icon icon-map-marker">
											<i class="w-icon-map-marker"></i>
										</span>
										<div class="icon-box-content">
											<h4 class="icon-box-title mb-0 ls-normal">Addresses</h4>
										</div>
									</div><br/>
									
									<p>The following addresses will be used on the checkout page
                                    by default.</p>
									<div class="row">
									@foreach($address as $addressList)	
										<div class="col-sm-6 mb-6">
											<div class="ecommerce-address billing-address pr-lg-8">
												<h4 class="title title-underline ls-25 font-weight-bold">Address</h4>
												<address class="mb-4">
													<table class="address-table">
														<tbody>
															<tr>
																<th>Name:</th>
																<td>{{$addressList->first_name}} {{$addressList->last_name}}</td>
															</tr>
															<tr>
																<th>Company:</th>
																<td>{{$addressList->company ?? "N/A"}}</td>
															</tr>
															<tr>
																<th>Address:</th>
																<td>{{$addressList->address_1}} {{$addressList->address_2 }}</td>
															</tr>
															<tr>
																<th>City:</th>
																<td>{{$addressList->city ?? "N/A"}}</td>
															</tr>
															<tr>
																<th>Country:</th>
																<td>{{$addressList->country ?? "N/A"}}</td>
															</tr>
															<tr>
																<th>Postcode:</th>
																<td>{{$addressList->zipcode ?? "N/A"}}</td>
															</tr>
															<tr>
																<th>Phone:</th>
																<td>{{$addressList->phone ?? "N/A"}}</td>
															</tr>
														</tbody>
													</table>
												</address>
												<a href="{{url('/account/edit/address/')}}/{{$addressList->id}}" class="btn btn-link btn-underline btn-icon-right text-primary">Edit<i class="w-icon-long-arrow-right"></i></a>
												&nbsp;&nbsp;
												<a href="{{url('/account/delete/address/')}}/{{$addressList->id}}" class="btn btn-link btn-underline btn-icon-right text-primary" style="color:red!important;" onclick="return validateDelete(this);">Delete<i class="w-icon-long-arrow-right"></i></a>
												
												<br/>
												@if ($addressList->is_default)
													<span class="badge bg-success">Default</span>
												@endif
												@if ($addressList->is_shipping)
													<span class="badge bg-info">Shipping</span>
												@endif
												@if ($addressList->is_billing)
													<span class="badge bg-warning">Billing</span>
												@endif
												
												<!-- Set Default / Billing / Shipping -->
												<form action="{{ url('/account/address/update_action') }}" method="POST">
													@csrf
													<div class="row">
															<div class="col-sm-6 mb-4">
																<div class="ecommerce-address billing-address pr-lg-8 border p-3">
																   <input type="hidden" name="address_id" value="{{$addressList->id}}" required />
																	<div class="form-check">
																		<input class="form-check-input" type="radio" name="is_default" value="1" id="default_{{ $addressList->id }}" {{ $addressList->is_default ? 'checked' : '' }}>
																		<label class="form-check-label" for="default_{{ $addressList->id }}">
																			Set as Default
																		</label>
																	</div>
																	{{-- <div class="form-check">
																		<input class="form-check-input" type="radio" name="is_shipping" value="1" id="shipping_{{ $addressList->id }}" {{ $addressList->is_shipping ? 'checked' : '' }}>
																		<label class="form-check-label" for="shipping_{{ $addressList->id }}">
																			Set as Shipping
																		</label>
																	</div>
																	<div class="form-check">
																		<input class="form-check-input" type="radio" name="is_billing" value="1" id="billing_{{ $addressList->id }}" {{ $addressList->is_billing ? 'checked' : '' }}>
																		<label class="form-check-label" for="billing_{{ $addressList->id }}">
																			Set as Billing
																		</label>
																	</div> --}}
																</div>
															</div>
													</div>

													<button type="submit" class="btn btn-primary mt-3">Save Preferences</button>
												</form>

												
											</div>
										</div>
									@endforeach
									</div>
								@else
									<p>No any address.</p><br/>
								@endif
								
								<a href="{{url('/account/add-new-address')}}" class="btn btn-dark btn-rounded btn-icon-right">Add New Address<i class="w-icon-long-arrow-right"></i></a>
								
								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
		
		<script>
	  function validateDelete(){ 
		var confirms = confirm('Do you want to delete ?');
		if(confirms==false){
			return false;
		}
	  }
	</script>
	<style>
	.badge {
		display: inline-block;
		padding: 0.35em 0.6em;
		font-size: 0.75em;
		font-weight: 600;
		color: #fff;
		border-radius: 0.25rem;
		line-height: 1;
		vertical-align: middle;
		white-space: nowrap;
	}

	.bg-success {
		background-color: #28a745;
	}

	.bg-info {
		background-color: #17a2b8;
	}

	.bg-warning {
		background-color: #ffc107;
	}
	</style>
@endsection
        