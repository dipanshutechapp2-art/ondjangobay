@extends('vendor/layouts.backend')
@section('title', 'Orders')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <style>
        .normal {
            background: yellow;
        }

        .weak {
            background: #ff5d5d;
        }

        .strong {
            background: #0eaf0e;
            color: #fff;
        }
    </style>
    <div class="content-wrapper admin-dashboard-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Orders</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Orders</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                           <div class="card-header">
                                <h3 class="card-title">Orders </h3>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <x-sweet-alert />
                                <table id="example2" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
									<thead>
										<tr>
											<th>Sr.no</th>
											<th>Order Number</th>
											<!--<th>User Name</th>
											 <th>User Email</th>
											<th>Price</th> -->
											<th>Total Amount</th>
											<th>Is Guest Order ?</th>
											<th>Payment Method</th>
											<th width="200px;">Payment Status</th>
											<th width="200px;">Order Status</th>
											<th>Created At</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php $i = 1; ?>
										@if(isset($orders) && count($orders))
											@foreach($orders as $order)
												<tr>
													<td>{{ $i++ }}</td>
													<td>{{ $order->order_number }}</td>
													<!-- <td>{{ $order->user->name ?? 'N/A' }}</td>
													<td>{{ $order->user->email ?? 'N/A' }}</td>
													<td>Demo Product</td>
													<!-- <td>{{ number_format($order->price, 2) }}</td> -->
													<td>{{$order->currency}}{{ number_format($order->total_amount, 2) }}</td>
													<td>
														@if($order->is_guest=='1')
															<span class="badge bg-success">Yes</span>
														@else
															<span class="badge bg-warning">No</span>
														@endif
													</td>
													<td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
													<td>
														<select class="form-control payment-status-dropdown" data-order-id="{{ $order->id }}">
															@php
																$paymentStatuses = ['pending','paid','failed','refunded'];
															@endphp
															@foreach($paymentStatuses as $payStatus)
																<option value="{{ $payStatus }}" {{ $order->payment_status == $payStatus ? 'selected' : '' }}>
																	{{ ucfirst($payStatus) }}
																</option>
															@endforeach
														</select>
													</td>
													<!--<td>
														<span class="badge 
															@if($order->payment_status == 'paid') bg-success
															@elseif($order->payment_status == 'pending') bg-warning
															@elseif($order->payment_status == 'failed') bg-danger
															@else bg-secondary
															@endif">
															{{ ucfirst($order->payment_status) }}
														</span>
													</td>-->
													<td>
														<select class="form-control order-status-dropdown" data-order-id="{{ $order->id }}">
															@php
																$statuses = ['pending','confirmed','shipped','delivered','cancelled','returned'];
															@endphp
															@foreach($statuses as $status)
																<option value="{{ $status }}" {{ $order->order_status == $status ? 'selected' : '' }}>
																	{{ ucfirst($status) }}
																</option>
															@endforeach
														</select>
													</td>
										
													<td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-M-Y') }}</td>
													<td> <a href="{{url('/vendor/order/product-order-details')}}/{{$order->id}}" class="btn btn-success btn float-right">View</a><br/><br/>
													<a href="{{ route('vendor.order.invoice.download', $order->id) }}" class="btn btn-primary">Invoice</a></td>
												</tr>
											@endforeach
										@endif
									</tbody>

                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $(document).on('click', '.delete-enquiry', function() {
            var enquiryId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this enquiry",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit delete request via form or AJAX
                    $('#delete-form-' + enquiryId).submit();
                }
            });
        });
    </script>

            <script>
                $(document).off('change', '.order-status-dropdown').on('change', '.order-status-dropdown', function() {
                    var orderId = $(this).data('order-id');
                    var status = $(this).val();
                    var $dropdown = $(this);

                    $.ajax({
                        url: '{{ url("/vendor/product-orders/update-status") }}',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            order_status: status,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Success', 'Order status updated!', 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to update status.', 'error');
                            // Optionally revert dropdown to previous value
                            // $dropdown.val($dropdown.data('current-status'));
                        }
                    });
                });
                </script>
				
			<script>
                $(document).off('change', '.payment-status-dropdown').on('change', '.payment-status-dropdown', function() {
                    var orderId = $(this).data('order-id');
                    var payStatus = $(this).val();
                    var $dropdown = $(this);

                    $.ajax({
                        url: '{{ url("/vendor/product-orders/payment-status") }}',
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            payment_status: payStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Success', 'Payment status updated!', 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to update status.', 'error');
                            // Optionally revert dropdown to previous value
                            // $dropdown.val($dropdown.data('current-status'));
                        }
                    });
                });
                </script>

@endsection
