@extends('layouts.app_inner')
@section('title', 'Order Success')
@section('content')
<!-- Start of Main -->
        <main class="main order">
           <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb shop-breadcrumb bb-no">
                    <li class="passed"><a href="{{ url('/my-community-deal') }}">My Community Deal</a></li>
                    <li>Checkout</li>
                    <li class="active">Order Complete</li>
                </ul>
            </div>
        </nav>
        <hr />
            <!-- Start of PageContent -->
            <div class="page-content mb-10 pb-2">
                <div class="container">
                    @if(!empty($orderInfo))
						<div class="order-success text-center font-weight-bolder text-dark">
							<i class="fas fa-check"></i>
							Thank you. Your order has been received.
						</div>
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
											{{ $orderInfo->product->name ?? 'N/A' }}
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
										<td>{{$orderInfo->currency}}{{number_format($orderInfo->amount,2)}}</td>
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

                    <a href="{{url('/my-account')}}" class="btn btn-dark btn-rounded btn-icon-left btn-back mt-6"><i class="w-icon-long-arrow-left"></i>My Account</a>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->

@endsection
