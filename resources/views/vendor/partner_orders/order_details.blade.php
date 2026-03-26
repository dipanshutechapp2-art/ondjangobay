@extends('vendor/layouts.backend')
@section('title', 'Campgain Order Details')

@section('content')
<style>
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .label-bold {
        font-weight: 600;
        width: 160px;
    }
</style>

<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="mb-2">Campgain Order Details</h1>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Campgain Order Details</li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if (!empty($orderInfo))

            <!-- Order Summary Card -->
			<br/>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="label-bold">Order Number:</span> {{ $orderInfo->order_number }}</p>
                            <p><span class="label-bold">Order Status:</span> {{ ucfirst($orderInfo->status) }}</p>
                            <p><span class="label-bold">Order Date:</span> {{ date('M d, Y', strtotime($orderInfo->created_at)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label-bold">Total Amount:</span> {{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}</p>
                            <p><span class="label-bold">Payment Method:</span> {{ $orderInfo->payment_method }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
								<tr>
									<td> {{ $orderInfo->product->name ?? "N/A" }}
									</td>
									<td>{{ $orderInfo->quantity }}</td>
									<td>{{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Totals -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>  
									<tr>
										<td class="label-bold">Total:</td>
										<td>{{ $orderInfo->currency }}{{ number_format($orderInfo->amount, 2) }}</td>
									</tr>   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="row">
                <!-- Billing -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Billing Address</h5><br/><br/><br/>
                            <p>{{ $orderInfo->billing_first_name }} {{ $orderInfo->billing_last_name }}</p>
                            <p>{{ $orderInfo->billing_company }}</p>
                            <p>{{ $orderInfo->billing_address_1 }}, {{ $orderInfo->billing_address_2 }}</p>
                            <p>{{ $orderInfo->billing_city }}, {{ $orderInfo->billing_state }}, {{ $orderInfo->billing_zipcode }}</p>
                            <p>{{ $orderInfo->billing_country }}</p>
                            <p>Phone: {{ $orderInfo->billing_phone }}</p>
                            <p>Email: {{ $orderInfo->billing_email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shipping -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Shipping Address</h5><br/><br/><br/>
                            <p>{{ $orderInfo->shipping_first_name }} {{ $orderInfo->shipping_last_name }}</p>
                            <p>{{ $orderInfo->shipping_company }}</p>
                            <p>{{ $orderInfo->shipping_address_1 }}, {{ $orderInfo->shipping_address_2 }}</p>
                            <p>{{ $orderInfo->shipping_city }}, {{ $orderInfo->shipping_state }}, {{ $orderInfo->shipping_zipcode }}</p>
                            <p>{{ $orderInfo->shipping_country }}</p>
                            <p>Phone: {{ $orderInfo->billing_phone }}</p>
                            <p>Email: {{ $orderInfo->billing_email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @else
                <div class="alert alert-warning">
                    No order details found.
                </div>
            @endif

        </div>
    </section>
</div>
@endsection
