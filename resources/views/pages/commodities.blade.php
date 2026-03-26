@extends('layouts.app_inner')
@section('title', 'Commodities')
@section('content')
    <!-- Start of Main -->
    <main class="main">
        <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">Commodities</h1>
            </div>
        </div>
        <!-- End of Page Header -->
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb mb-6">
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li><a href="{{url('/commodities')}}">Commodities</a></li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!--Commodities us -->
       <section class="commodities-content pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="category-section-title mb-5">
                    Ondjango – Commodities Trading Platform
                </h2>

                <div class="mb-4">
                    <p>
                        Ondjango is a modern <strong>commodities trading and shopping platform</strong>
                        designed to simplify the buying and selling of commodities across local,
                        regional, and global markets.
                    </p>
                    <p>
                        The platform connects <strong>farmers, producers, traders, suppliers,
                        exporters, and buyers</strong> in one secure digital marketplace, enabling
                        transparent, efficient, and reliable commodity trade.
                    </p>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Built for Commodity Trade</h5>
                    <p>
                        Ondjango supports a wide range of commodities including
                        <strong>agricultural products, cash crops, spices, oilseeds, minerals,
                        construction materials, and industrial raw materials</strong>.
                        Sellers gain direct access to buyers, while buyers benefit from
                        competitive pricing and verified suppliers.
                    </p>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">One Platform. Multiple Opportunities.</h5>
                    <ul>
                        <li>Source commodities from trusted local and international suppliers</li>
                        <li>Sell commodities directly to verified buyers and businesses</li>
                        <li>Access bulk and group purchasing options</li>
                        <li>Trade securely with integrated payment systems</li>
                        <li>Track orders through reliable logistics partners</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Secure, Transparent &amp; Efficient</h5>
                    <p>
                        Every transaction on Ondjango is supported by
                        <strong>secure digital payments</strong>, transparent pricing,
                        and reliable logistics solutions. Real-time tracking and
                        streamlined order management ensure smooth and dependable trade operations.
                    </p>
                </div>

                <div class="mb-4">
                    <h5 class="mb-3">Supporting Growth &amp; Global Trade</h5>
                    <p>
                        Ondjango enables <strong>sustainable commerce and economic growth</strong>
                        by providing direct market access for producers and efficient sourcing
                        solutions for buyers. The platform brings traditional commodity markets
                        into the digital age.
                    </p>
                </div>

                <div class="mt-4">
                    <p>
                        <strong>Ondjango – Buy, Sell, and Trade Commodities with Confidence.</strong>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>


        <!--Commodities us end -->
    </main>
    <!-- End of Main -->
@endsection