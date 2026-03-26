@extends('admin/layouts.backend')
@section('title', 'Sections Heading')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Sections Headings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Sections Heading</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row">

                    <!-- Add Menu Form -->
                    <div class="col-md-12">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form class="geniusform" action=" {{ route('admin.homepage.sections_heading_update') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Explore Property</h3>

                                                </div>
                                                <div class="card-body">
                                                    <div class="gocover">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_title"> Title</label>
                                                        <input type="text" class="form-control" id="hero_title"
                                                            name="section[property][title]" placeholder="Enter About Title" value="{{ $section->property->title ?? ''}}"
                                                            required="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_subtitle"> Subtitle</label>
                                                        <textarea name="section[property][subtitle]" id="summernotes" style="width: 100%; height:200px" required="">{{ $section->property->subtitle ?? ''}}
                                                        </textarea>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Location</h3>

                                                </div>
                                                <div class="card-body">
                                                    <div class="gocover">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_title"> Title</label>
                                                        <input type="text" class="form-control" id="hero_title"
                                                            name="section[location][title]" placeholder="Enter About Title" value="{{ $section->location->title ?? ''}}"
                                                            required="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_subtitle"> Subtitle</label>
                                                        <textarea name="section[location][subtitle]" id="summernotes" style="width: 100%; height:200px" required="">{{ $section->location->subtitle ?? ''}}
                                                        </textarea>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Testimonials</h3>

                                                </div>
                                                <div class="card-body">
                                                    <div class="gocover">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_title"> Title</label>
                                                        <input type="text" class="form-control" id="hero_title"
                                                            name="section[testimonial][title]" placeholder="Enter About Title" value="{{ $section->testimonial->title ?? ''}}"
                                                            required="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_subtitle"> Subtitle</label>
                                                        <textarea name="section[testimonial][subtitle]" id="summernotes" style="width: 100%; height:200px" required="">{{ $section->testimonial->subtitle ?? ''}}
                                                        </textarea>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Blog</h3>

                                                </div>
                                                <div class="card-body">
                                                    <div class="gocover">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_title"> Title</label>
                                                        <input type="text" class="form-control" id="hero_title"
                                                            name="section[blog][title]" placeholder="Enter About Title" value="{{ $section->blog->title ?? ''}}"
                                                            required="">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="hero_subtitle"> Subtitle</label>
                                                        <textarea name="section[blog][subtitle]" id="summernotes" style="width: 100%; height:200px" required="">{{ $section->blog->subtitle ?? ''}}
                                                        </textarea>
                                                    </div>

                                                </div>
                                            </div>
                                            <button type="submit" id="submit-btn"
                                                class="btn btn-primary mt-2 w-100">Submit</button>

                                        </form>
                                    </div>

                                </div>
                            </div>
                    </div>

        </section>
    </div>


    </div>
    <!-- /.row -->
    <!-- Button trigger modal -->

    </div>

    </section>
    </div>

@endsection
