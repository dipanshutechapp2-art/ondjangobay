@extends('layouts.app_inner')
@section('title', 'Campaign Orders')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Campaign Orders</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Campaign Orders</li>
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
                                @if($orders->isNotEmpty())
                                
									<h3> <i class="w-icon-orders"></i> Campaign Orders</h3>
									@if (session('success'))
										<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
											{{ session('success') }}
										</div>
									@endif

									@if (session('error'))
										<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
											{{ session('error') }}
										</div>
									@endif
									<table class="shop-table account-orders-table mb-6">
										<thead>
											<tr>
												<th align="left" class="order-id">Order</th>
												<th align="left" class="order-date">Date</th>
												<th align="left" class="order-status">Status</th>
												<th align="left" class="order-total">Total</th>
												<th align="left" class="order-total">Payment Status</th>
												<th align="left" class="order-actions">Actions</th>
											</tr>
										</thead>
										<tbody>
											@if($orders->isNotEmpty())
												@foreach($orders as $order)
												<tr>
													<td class="order-id">{{$order->order_number}}</td>
													<td class="order-date">{{date('M d, Y',strtotime($order->created_at))}}</td>
													<td class="order-status">{{ucfirst($order->status)}}</td>
													<td class="order-total">
														<span class="order-price">{{$order->currency}}{{number_format($order->amount,2)}}</span>
													</td>
													<td class="order-total">{{ucfirst($order->payment_status)}}</td>
													<td class="order-action"> 
														<div class="order-buttons"> 
															<a href="{{url('/account/partner-order-details')}}/{{$order->order_number}}" class="btn btn-outline btn-default btn-block btn-sm btn-rounded" title="Order details"><i class="fas fa-eye"></i></a> 
															<a href="{{ route('partner.customer.order.invoice.download', $order->order_number) }}" class="btn btn-primary " title="Download invoice"><i class="fas fa-download"></i></a>
															<a href="{{ url('/cancel/partner-order') }}/{{ $order->order_number }}"
															   class="btn btn-danger btn-block btn-sm btn-rounded"
															   title="Cancel this order"
															   onclick="return CancelOrderAlert(this);">
																<i class="fas fa-times"></i> Cancel
															</a> 
														</div> 
													</td>
												</tr>
												@endforeach
											@endif
											
										</tbody>
									</table>
									@if($orders->hasPages())
										<div class="d-flex justify-content-center">
											{{ $orders->links('pagination::bootstrap-5') }}
										</div>
									@endif
									
								@else
									<p>No orders.</p><br/>
								@endif

								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
		<script> 
			function reorderAlert() {
				return confirm('Do you want to reorder?');
			}
			function CancelOrderAlert() {
				return confirm('Are you sure you want to cancel this order?');
			}
		</script>
		
@endsection
        