@extends('layouts.app_inner')
@section('title', 'Add Wallet Balance')
@section('content')
    <!-- Start of Main -->
    <main class="main">
        <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">Add Wallet Balance</h1>
            </div>
        </div>
        <!-- End of Page Header -->

        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li>Add Wallet Balance</li>
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
                            <div class="icon-box icon-box-side icon-box-light mb-3">
                                <span class="icon-box-icon icon-wallet">
                                    <i class="w-icon-wallet"></i>
                                </span>
                                <div class="icon-box-content">
                                    <h4 class="icon-box-title mb-0 ls-normal">Add Money to Wallet</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 mb-6">
                                    <div class="ecommerce-address billing-address pr-lg-8">
                                        <h4 class="title title-underline ls-25 font-weight-bold mb-4">Add Wallet Amount</h4>
										@if (session('success'))
											<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
												{{ session('success') }}
											</div><br/>
										@endif
										@if (session('error'))
											<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
												{{ session('error') }}
											</div><br/>
										@endif	
                                        <form class="form account-details-form" action="{{ route('wallet.add') }}" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="amount">Amount *</label>
                                                        <input type="number" id="amount" name="amount" placeholder="Enter amount" class="form-control form-control-md" min="1" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="gateway">Payment Gateway *</label>
                                                        <select name="gateway" id="gateway" class="form-control form-control-md" required>
                                                            <option value="">-- Select Gateway --</option>
                                                            @if(!empty($paymentGatewayList))
																@foreach($paymentGatewayList as $paymentGateway)
																   <option value="{{strtolower($paymentGateway->name)}}">{{$paymentGateway->name}}</option>
																@endforeach
														    @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-dark btn-rounded btn-sm mt-4">Proceed to Pay</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of PageContent -->
    </main>
    <!-- End of Main -->
@endsection
