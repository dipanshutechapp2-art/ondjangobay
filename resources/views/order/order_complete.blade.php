@extends('layouts.app_inner')
@section('title', 'Order Complete')
@section('content')
	<!-- Start of Main -->
        <main class="main order">
            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb shop-breadcrumb bb-no">
                        <li class="passed"><a href="{{url('/cart')}}">Shopping Cart</a></li>
                        <li class="passed"><a href="{{url('/checkout')}}">Checkout</a></li>
                        <li class="active"><a href="{{url('/order-complete')}}">Order Complete</a></li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

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
								<strong>{{ucfirst($orderInfo->order_status)}}</strong>
							</li>
							<li>
								<label>Date</label>
								<strong>{{ date('M d, Y',strtotime($orderInfo->created_at))}}</strong>
							</li>
							<li>
								<label>Total</label>
								<strong>{{$orderInfo->currency}}{{ number_format($orderInfo->total_amount,2)}}</strong>
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
									@foreach ($orderInfo->orderProduct as $orderProductList)
										<tr>
											<td>
												<a href="{{url('product/')}}/{{$orderProductList->product->slug}}">
													{{ $orderProductList->name }}
												</a>
												&nbsp;<strong>x {{ $orderProductList->quantity }}</strong><br>
												<!--Vendor: 
												<a href="#">
													test vendor
												</a>-->
												@if (!empty($orderProductList->variant_text))
													<div class="text-muted small" style="font-size: 11px;font-weight: bold;">{{$orderProductList->variant_text}}</div>
												@endif
												  <p class="text-muted small" style="font-size: 11px;"><b style="font-size: 11px;">Vendor:</b> {{$orderProductList->product->userInfo->name ?? "N/A"}} <br/><b style="font-size: 11px;">Orgin:</b> {{$orderProductList->product->type ?? "unknown"}}</p>
											</td>
											<td>
												{{$orderProductList->currency}}{{number_format($orderProductList->total,2)}}
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									@foreach ($orderInfo->orderTotal as $orderTotalList)
										<tr>
											<th>{{$orderTotalList->meta_key}}:</th>
											<td>{{$orderTotalList->currency}}{{(number_format($orderTotalList->meta_value,2))}}</td>
										</tr>
									@endforeach	
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
														<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f7999e94928098859cc6c5c2b7909a969e9bd994989a">[{{$orderInfo->email}}]</a></td>
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
														<td><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f7999e94928098859cc6c5c2b7909a969e9bd994989a">[{{$orderInfo->email}}]</a></td>
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
        