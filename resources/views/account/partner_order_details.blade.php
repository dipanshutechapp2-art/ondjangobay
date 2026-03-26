@extends('layouts.app_inner')
@section('title', 'Campaign Order details')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Campaign Order details</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Campaign Order details</li>
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
                                
                                @if(!empty($orderInfo))
						
									<!-- End of Order Success -->

									<ul class="order-view list-style-none">
										<li>
											<label>Order number</label>
											<strong>{{$orderInfo->order_number}}</strong>
										</li>
										<li>
											<label>Status</label>
											<strong>{{ucfirst($orderInfo->status)}}</strong>
										</li>
										<li>
											<label>Date</label>
											<strong>{{ date('M d, Y',strtotime($orderInfo->created_at))}}</strong>
										</li>
										<li>
											<label>Total</label>
											<strong>{{$orderInfo->currency}}{{ number_format($orderInfo->amount,2)}}</strong>
										</li>
										<li>
											<label>Payment method</label>
											<strong>{{$orderInfo->payment_method}}</strong>
										</li>
									</ul>
									
									
									
									<!-- End of Order View -->

									<div class="order-details-wrapper mb-5">
										<h4 class="title text-uppercase ls-25 mb-5">Order Details</h4>
										<p><strong>Ordered at:</strong> {{ $orderInfo->created_at->format('Y, M, d H:i:s') }}</p>
										<p><strong>Shipped at:</strong> {{ $orderInfo->shipped_at ? date('Y, M, d H:i:s',strtotime($orderInfo->shipped_at)) : '' }}</p>
										<p><strong>Tracking:</strong> {{ $orderInfo->tracking_number ?? '' }} ({{ $orderInfo->shipping_provider ?? 'N/A' }})</p>
										<table class="order-table">
											<thead>
												<tr>
													<th class="text-dark">Product</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>
														{{ $orderInfo->product->name ?? "N/A" }}
														&nbsp;<strong>x {{ $orderInfo->quantity }}</strong><br>
													</td>
													<td>
														{{$orderInfo->currency}}{{number_format($orderInfo->amount,2)}}
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<th>Total:</th>
													<td>{{$orderInfo->currency}}{{(number_format($orderInfo->amount,2))}}</td>
												</tr>	
											</tfoot>
										</table>
									</div>
									<!-- End of Order Details -->

									<div id="account-addresses">
										<div class="row">
											<div class="col-sm-6 mb-8">
												<div class="ecommerce-address billing-address">
													<h4 class="title title-underline ls-25 font-weight-bold">Billing Address</h4>
													<address class="mb-4">
														<table class="address-table">
															<tbody>
																<tr>
																	<td>{{$orderInfo->billing_first_name}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_last_name}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_company}},{{$orderInfo->billing_address_1}},<br/> {{$orderInfo->billing_address_2}}, {{$orderInfo->billing_city}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_state}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_country}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_zipcode}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->billing_phone}}</td>
																</tr>
																<tr class="email">
																	<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f7999e94928098859cc6c5c2b7909a969e9bd994989a">[{{$orderInfo->billing_email}}]</a></td>
																</tr>
															</tbody>
														</table>
													</address>
												</div>
											</div>
											<div class="col-sm-6 mb-8">
												<div class="ecommerce-address shipping-address">
													<h4 class="title title-underline ls-25 font-weight-bold">Shipping Address</h4>
													<address class="mb-4">
														<table class="address-table">
															<tbody>
																<tr>
																	<td>{{$orderInfo->shipping_first_name}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->shipping_last_name}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->shipping_company}},{{$orderInfo->shipping_address_1}},<br/> {{$orderInfo->shipping_address_2}}, {{$orderInfo->shipping_city}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->shipping_state}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->shipping_country}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->shipping_zipcode}}</td>
																</tr>
																<tr>
																	<td>{{$orderInfo->phone}}</td>
																</tr>
																<tr class="email">
																	<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f7999e94928098859cc6c5c2b7909a969e9bd994989a">[{{$orderInfo->billing_email}}]</a></td>
																</tr>
															</tbody>
														</table>
													</address>
												</div>
											</div>
										</div>
									</div>
									<!-- End of Account Address -->
								@else
									<p>No order.</p>
								@endif

								<a href="{{url('/account/partner-orders')}}" class="btn btn-dark btn-rounded btn-icon-left btn-back mt-6"><i class="w-icon-long-arrow-left"></i>Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
@endsection
        