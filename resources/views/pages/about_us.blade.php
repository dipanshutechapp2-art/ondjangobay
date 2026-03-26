@extends('layouts.app_inner')
@section('title', 'About Us')
@section('content')
    <!-- Start of Main -->
    <main class="main">
        <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">About Us</h1>
            </div>
        </div>
        <!-- End of Page Header -->

        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li>About Us</li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Page Content -->
        <div class="page-content">


            <!--about us -->
            <section class="about-us-content pb-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="category-section-title mb-5">About Ondjango</h2>
                            <div class="mb-5">
                                <p>Welcome to <strong>Ondjango</strong>, the international platform that connects
                                    <strong>Africa to the world</strong> and
                                    the world to Africa.
                                </p>
                                <p>Our mission is simple yet powerful: <strong>to make the process of importing and
                                        exporting products simple, secure, affordable, and practical</strong> for all
                                    <strong>Africans individuals who simply want to buy something for personal use, easily
                                        and conveniently</strong> and for who wishes to expand their business opportunities.
                                </p>
                            </div>
                            <div class="mb-5">
                                <p class="mb-0">At Ondjango, we believe that global trade should be accessible to everyone.
                                </p>
                                <p>Imagine one single platform where you can:</p>
                                <ul>
                                    <li><strong>Import products</strong> from key global markets — <strong>China, India, the
                                            United States, Europe, South America, and Africa</strong> — quickly and
                                        transparently;</li>
                                    <li><strong>Export African commodities and finished products</strong> to international
                                        markets, promoting “Made in Africa” and supporting sustainable economic growth
                                        across the continent;</li>
                                    <li><strong>Form group purchasing partnerships</strong>, allowing individuals and small
                                        businesses to combine orders for better prices and conditions;</li>
                                    <li>In addition, <strong>Ondjango allows local stores and vendors to integrate with the
                                            platform</strong>, creating their own online space — <strong>selling within
                                            Africa and exporting to other countries.</strong></li>
                                </ul>
                            </div>
                            <div class="mb-5">
                                <p>Here, you can find everything — from <strong>technology and professional tools</strong>
                                    to <strong>fashion, beauty, agriculture, construction, electronics, and much
                                        more.</strong></p>
                                <p>Ondjango was built to be <strong>the digital home of modern African commerce</strong>,
                                    integrating <strong>local and international payment systems, internal and global
                                        logistics partners</strong>, and a transparent tracking experience.</p>
                                <p>And the best part — it all happens in one place, with the <strong>security, convenience,
                                        and innovation</strong> that digital trade requires.</p>
                                <p>Whether you are buying, selling, or distributing, <strong>Ondjango is your global meeting
                                        point</strong>, where <strong>opportunities flow in both directions</strong> — from
                                    Africa to the world, and from the world to Africa.</p>
                                <p><strong>Ondjango – connect, import, export, and transform.</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--about us end -->




        </div>

        <style>
            .about-us-content p {
                margin-bottom: 8px;
            }
        </style>
    </main>
    <!-- End of Main -->
@endsection