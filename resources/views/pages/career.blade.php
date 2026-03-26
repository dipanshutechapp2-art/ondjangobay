@extends('layouts.app_inner')
@section('title', 'Career')
@section('content')
    <!-- Start of Main -->
    <main class="main"> <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">Career</h1>
            </div>
        </div>
        <!-- End of Page Header -->

        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb mb-6">
                    <li><a href="{{url('/')}}">Home</a></li>
                    <li><a href="{{url('/career')}}">Career</a></li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of Pgae Contetn -->
        <div class="page-content mb-10 pb-2">
            <section class="career-content pb-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="category-section-title mb-4">
                                Careers
                            </h2>

                            <div class="mb-4">
                                <p>
                                    At <strong>Ondjango</strong>, we are building more than a digital marketplace —
                                    we are creating a platform that empowers communities, strengthens trade,
                                    and connects markets globally. We are always looking for passionate,
                                    skilled, and motivated individuals to grow with us.
                                </p>
                                <p>
                                    If you are driven by innovation, collaboration, and impact,
                                    Ondjango offers an environment where your ideas matter and your work
                                    makes a real difference.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3">Why Work With Us</h5>
                                <ul>
                                    <li>Be part of a fast-growing digital commerce platform</li>
                                    <li>Work on meaningful projects that support global trade</li>
                                    <li>Collaborative, inclusive, and growth-oriented culture</li>
                                    <li>Opportunities for learning and career advancement</li>
                                    <li>Competitive compensation and performance-based growth</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3">Who We’re Looking For</h5>
                                <p>
                                    We welcome professionals and fresh talent across various fields,
                                    including <strong>technology, operations, logistics, sales,
                                        marketing, customer support, data, and community management</strong>.
                                </p>
                                <p>
                                    Whether you are an experienced professional or just starting your
                                    career, if you share our vision of transforming trade through
                                    technology and collaboration, we would love to hear from you.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5 class="mb-3">Grow With Ondjango</h5>
                                <p>
                                    We believe in continuous learning and internal growth. Team members
                                    are encouraged to develop new skills, take ownership of their work,
                                    and contribute to shaping the future of the platform.
                                </p>
                            </div>

                            <div class="mt-4">
                                <p>
                                    <strong>
                                        Ready to take the next step in your career?
                                        Join Ondjango and help build the future of digital trade.
                                    </strong>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <!-- End of Page Content -->
        </div>
    </main>
    <!-- End of Main -->
@endsection