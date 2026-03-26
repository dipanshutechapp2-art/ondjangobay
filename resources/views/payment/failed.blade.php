@extends('layouts.app_inner')
@section('title', 'EMIS Payment Failed')

@section('content')

<!-- Start of Main -->
<main class="main">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb bb-no">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/emis/payment-failed') }}">Emis Payment failed</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-content">
        <div class="container max-container">
            <div class="shop-content row gutter-lg mb-10">

                <!-- Title -->
                    <h3 class="fw-bold text-danger mb-2" style="color:red;font-weight:bold;">
                        Payment Failed
                    </h3>

                    <!-- Message -->
                    <p class="text-muted mb-4">
                        Your EMIS payment could not be processed.<br>
                        Please try again or contact support if the amount was deducted.
                    </p>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url('/') }}" class="btn btn-primary  btn-rounded ml-0">
                            Go to Home
                        </a>
                    </div>

            </div>
        </div>
    </div>
</main>

@endsection
