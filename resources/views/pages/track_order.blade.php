@extends('layouts.app_inner')
@section('title', 'Track Order')

@section('content')

<main class="main">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb mb-4">
                <li><a href="{{url('/')}}">Home</a></li>
                <li>Track Order</li>
            </ul>
        </div>
    </nav>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Tracking Form -->
                <div class="track-wrapper mb-4">

                    <h4 class="mb-3">Track Your Order</h4>

                    <form>
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Order ID">
                            </div>

                            <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Email / Phone">
                            </div>

                            <div class="col-md-2">
                                <button class="btn track-btn w-100 text-white">Track</button>
                            </div>
                        </div>
                    </form>

                </div>


                <!-- Tracking Result -->
                <div class="track-wrapper">

                    <div class="row">

                        <!-- LEFT SIDE -->
                        <div class="col-lg-8">

                            <h5>Order Tracking - Ondjangobay</h5>

                            <p><strong>Tracking ID :</strong> OD3-INT-2026-000123</p>

                            <!-- Progress Bar -->
                            <div class="progress mb-4" style="height:8px;">
                                <div class="progress-bar bg-warning" style="width:65%"></div>
                            </div>

                            <!-- Step Progress -->
                            <div class="progress-steps">

                                <div class="step active">
                                    <div class="step-circle">✓</div>
                                    <div class="step-title">Placed</div>
                                </div>

                                <div class="step active">
                                    <div class="step-circle">✓</div>
                                    <div class="step-title">Confirmed</div>
                                </div>

                                <div class="step active">
                                    <div class="step-circle">✓</div>
                                    <div class="step-title">Transit</div>
                                </div>

                                <div class="step">
                                    <div class="step-circle">4</div>
                                    <div class="step-title">Out for Delivery</div>
                                </div>

                                <div class="step">
                                    <div class="step-circle">5</div>
                                    <div class="step-title">Delivered</div>
                                </div>

                            </div>

                            <hr class="my-4">

                            <!-- Current Status -->
                            <h6>Current Status</h6>
                            <p class="text-success fw-bold">
                                Your order is in international transit
                            </p>

                            <div class="plane-animation mb-3">
                                <i class="fas fa-plane fa-2x text-danger"></i>
                            </div>

                            <p><strong>Estimated Delivery :</strong> 7 - 10 Business Days</p>

                            <hr>

                            <!-- Timeline -->
                            <h6>Tracking Timeline</h6>

                            <ul class="tracking-list">
                                <li class="active">Order Confirmed</li>
                                <li class="active">Preparing Order</li>
                                <li class="active">In International Transit</li>
                                <li>Arrived at Ondjangobay International Warehouse</li>
                                <li>Final Delivery</li>
                            </ul>

                        </div>

                        <!-- RIGHT SIDE -->
                        <!-- <div class="col-lg-4">

                            <div class="summary-box mb-3">

                                <h6>Order Summary</h6>
                                <hr>

                                <p>Order ID : <strong>#OD458796</strong></p>
                                <p>Order Date : 02 Feb 2026</p>
                                <p>Total Amount : <strong>₹3,499</strong></p>

                            </div>

                            <div class="summary-box">

                                <h6>Delivery Address</h6>
                                <hr>

                                <p>
                                    Customer name <br>
                                    Address <br>

                                </p>

                            </div>

                        </div> -->

                    </div>
                </div>

            </div>
        </div>
    </div>

</main>



@endsection