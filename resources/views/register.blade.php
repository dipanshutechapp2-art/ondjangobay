@extends('layouts.app_inner')
@section('title', 'Register')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Register</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav mb-10 pb-1">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Register</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content contact-us">
                <div class="container">
                    <hr class="divider mb-10 pb-1">
                    <section class="contact-section">
                        <div class="row gutter-lg pb-3">
                            <div class="col-lg-6 mb-8">
                                <h4 class="title mb-3">Register </h4>
                                <form class="form contact-us-form" action="{{url('/user/register_action')}}" method="post">
                                    <div class="form-group">
                                        <label for="username">Your Name</label>
                                        <input type="text" id="username" name="username" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="email_1">Your Email</label>
                                        <input type="email" id="email_1" name="email_1" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Your Message</label>
                                        <textarea id="message" name="message" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-rounded">Submit</button>
                                </form>
                            </div>
                        </div>
                    </section>
                    <!-- End of Contact Section -->
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
@endsection