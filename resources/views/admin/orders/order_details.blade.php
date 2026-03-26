@extends('admin/layouts.backend')
@section('title', 'Order Details')

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
            <h1 class="mb-2">Order Details</h1>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Order Details</li>
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
                            <p><span class="label-bold">Order Status:</span> {{ ucfirst($orderInfo->order_status) }}</p>
                            <p><span class="label-bold">Order Date:</span> {{ date('M d, Y', strtotime($orderInfo->created_at)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label-bold">Total Amount:</span> {{ $orderInfo->currency }}{{ number_format($orderInfo->total_amount, 2) }}</p>
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
                                @foreach ($orderInfo->orderProduct as $orderProductList)
                                    <tr>
                                        <td> {{ $orderProductList->name }}
                                            <!--<a href="{{ url('product/' . $orderProductList->product->slug) }}">
                                                {{ $orderProductList->name }}
                                            </a> -->
											@if (!empty($orderProductList->variant_text))
												<div class="text-muted small" style="font-size: 11px;font-weight: bold;">{{$orderProductList->variant_text}}</div>
											@endif
											<p class="text-muted small" style="font-size: 11px;"><b style="font-size: 11px;">Vendor:</b> {{$orderProductList->product->userInfo->name ?? "N/A"}} <br/><b style="font-size: 11px;">Orgin:</b> {{$orderProductList->product->type ?? "unknown"}}</p>
                                        </td>
                                        <td>{{ $orderProductList->quantity }}</td>
                                        <td>{{ $orderProductList->currency }}{{ number_format($orderProductList->total, 2) }}</td>
                                    </tr>
                                @endforeach
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
                                    @foreach ($orderInfo->orderTotal as $orderTotalList)
                                        <tr>
                                            <td class="label-bold">{{ ucfirst(str_replace('_', ' ', $orderTotalList->meta_key)) }}</td>
                                            <td>{{ $orderTotalList->currency }}{{ number_format($orderTotalList->meta_value, 2) }}</td>
                                        </tr>
                                    @endforeach
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
                            <p>Phone: {{ $orderInfo->phone }}</p>
                            <p>Email: {{ $orderInfo->email }}</p>
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
                            <p>Phone: {{ $orderInfo->phone }}</p>
                            <p>Email: {{ $orderInfo->email }}</p>
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
