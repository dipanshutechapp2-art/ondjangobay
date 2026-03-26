@extends('layouts.app_inner')
@section('title', ' Agricultural Commodities')
@section('content')
    <!-- Start of Main -->
    <main class="main">
        <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">Agricultural Commodities</h1>
            </div>
        </div>
        <!-- End of Page Header -->
        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li>Agricultural Commodities</li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Pgae Contetn -->
        <section class="commodities-content pb-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="category-section-title mb-4">
                            Agricultural Commodities
                        </h2>

                        <div class="mb-4">
                            <p>
                                Agricultural commodities form the backbone of global trade and food security.
                                Ondjango provides a reliable digital marketplace for sourcing and trading
                                <strong>high-quality agricultural products</strong> from trusted farmers,
                                cooperatives, and suppliers.
                            </p>
                            <p>
                                Our platform enables buyers and sellers to connect directly, ensuring
                                <strong>fair pricing, consistent quality, and transparent transactions</strong>
                                across local and international markets.
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Wide Range of Agricultural Products</h5>
                            <p>
                                Ondjango supports a diverse range of agricultural commodities including
                                <strong>grains, cereals, pulses, oilseeds, spices, fruits, vegetables,
                                    and plantation crops</strong>. All products are sourced from verified
                                suppliers and meet industry quality standards.
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Built for Bulk &amp; Trade</h5>
                            <ul>
                                <li>Bulk and wholesale agricultural commodity trading</li>
                                <li>Direct sourcing from farmers and producers</li>
                                <li>Competitive market-based pricing</li>
                                <li>Secure payment and order management</li>
                                <li>Reliable logistics and delivery support</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Quality, Transparency &amp; Trust</h5>
                            <p>
                                Each agricultural product listed on Ondjango undergoes quality checks
                                and is traded with full transparency. Our integrated logistics and tracking
                                systems ensure timely delivery and dependable supply chains.
                            </p>
                        </div>

                        <div class="mt-4">
                            <p>
                                <strong>
                                    Ondjango – Your Trusted Marketplace for Agricultural Commodities.
                                </strong>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- End of Main -->
@endsection