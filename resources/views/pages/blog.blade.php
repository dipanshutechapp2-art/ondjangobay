@extends('layouts.app_inner')
@section('title', 'Blog')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Blog</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav mb-6">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/blog')}}">Blog</a></li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of Page Content -->
            <div class="page-content mb-10 pb-2">
                <div class="container">
                    <div class="row gutter-lg">
                        <div class="main-content">
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-4">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/1-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Fashion</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">New found the men dress for summer</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p>
                                        <a href="post-single-1.html" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-4">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/2-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Others</a>,
                                        <a href="#">Technology</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">Recognitory the needs is primary condition  for design</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p> 
                                        <a href="{{url('/single-blog')}}" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-4">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/3-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Clothes</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">New found the women’s shirt  for summer season</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p> 
                                        <a href="post-single-1.html" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-4">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/4-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Lifestyle</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">We want to be different and fashion gives to me that outlet</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p> 
                                        <a href="{{url('/single-blog')}}" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-4">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/5-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Entertainment</a>,
                                        <a href="#">Lifestyle</a>,
                                        <a href="#">Others</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">Comes a cool blog post with Images</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p> 
                                        <a href="{{url('/single-blog')}}" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <article class="post post-list post-listing mb-md-10 mb-6 pb-2 overlay-zoom mb-2">
                                <figure class="post-media br-sm">
                                    <a href="{{url('/single-blog')}}">
                                        <img src="{{ asset('frontend/assets/images/blog/classic/6-1.jpg')}}" width="930" height="500" alt="blog">
                                    </a>
                                </figure>
                                <div class="post-details">
                                    <div class="post-cats text-primary">
                                        <a href="#">Fashion</a>,
                                        <a href="#">Technology</a>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{url('/single-blog')}}">Fusce lacinia arcuet nulla</a>
                                    </h4>
                                    <div class="post-content">
                                        <p>Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, 
                                        eget blandit nunc tortor eu nibh. Suspendisse potenti.Sed egstas, ant at 
                                        vulputate volutpat, uctus metus libero eu augue, vitae luctus…</p> 
                                        <a href="{{url('/single-blog')}}" class="btn btn-link btn-primary">(read more)</a>
                                    </div>
                                    <div class="post-meta">
                                        by <a href="#" class="post-author">John Doe</a>
                                        - <a href="#" class="post-date">03.05.2021</a>
                                    </div>
                                </div>
                            </article>
                            <ul class="pagination justify-content-center">
                                <li class="prev disabled">
                                    <a href="#" aria-label="Previous" tabindex="-1" aria-disabled="true">
                                        <i class="w-icon-long-arrow-left"></i>Prev
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="next">
                                    <a href="#" aria-label="Next">
                                        Next<i class="w-icon-long-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- End of Main Content -->
                        <aside class="sidebar right-sidebar blog-sidebar sidebar-fixed sticky-sidebar-wrapper">
                            <div class="sidebar-overlay">
                                <a href="#" class="sidebar-close">
                                    <i class="close-icon"></i>
                                </a>
                            </div>
                            <a href="#" class="sidebar-toggle">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <div class="sidebar-content">
                                <div class="sticky-sidebar">
                                    <div class="widget widget-search-form">
                                        <div class="widget-body">
                                            <form action="#" method="GET" class="input-wrapper input-wrapper-inline">
                                                <input type="text" class="form-control" placeholder="Search in Blog" autocomplete="off" required="">
                                                <button class="btn btn-search"><i class="w-icon-search"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- End of Widget search form -->
                                    <div class="widget widget-categories">
                                        <h3 class="widget-title bb-no mb-0">Categories</h3>
                                        <ul class="widget-body filter-items search-ul">
                                            <li><a href="{{url('/blog')}}">Clothes</a></li>
                                            <li><a href="{{url('/blog')}}">Entertainment</a></li>
                                            <li><a href="{{url('/blog')}}">Fashion</a></li>
                                            <li><a href="{{url('/blog')}}">Lifestyle</a></li>
                                            <li><a href="{{url('/blog')}}">Others</a></li>
                                            <li><a href="{{url('/blog')}}">Shoes</a></li>
                                            <li><a href="{{url('/blog')}}">Technology</a></li>
                                        </ul>
                                    </div>
                                    <!-- End of Widget categories -->
                                    <div class="widget widget-posts">
                                        <h3 class="widget-title bb-no">Popular Posts</h3>
                                        <div class="widget-body">
                                            <div class="swiper">
                                                <div class="swiper-container swiper-theme nav-top" data-swiper-options="{
                                                    'spaceBetween': 20,
                                                    'slidesPerView': 1
                                                }">
                                                    <div class="swiper-wrapper row cols-1">
                                                        <div class="swiper-slide widget-col">
                                                            <div class="post-widget mb-4">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/1-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 1, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}">Fashion tells about who you are from external point</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div class="post-widget mb-4">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/2-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 5, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}">New found the men dress for summer</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div class="post-widget mb-2">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/3-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 1, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}Cras ornare tristique elit</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="swiper-slide widget-col">
                                                            <div class="post-widget mb-4">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/4-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 1, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}">Vivamus vestibulum ntulla nec ante</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div class="post-widget mb-4">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/5-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 5, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}">Fusce lacinia arcuet nulla</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div class="post-widget mb-2">
                                                                <figure class="post-media br-sm">
                                                                    <img src="{{ asset('frontend/assets/images/blog/sidebar/6-1.jpg')}}" alt="150" height="150">
                                                                </figure>
                                                                <div class="post-details">
                                                                    <div class="post-meta">
                                                                        <a href="#" class="post-date">March 1, 2021</a>
                                                                    </div>
                                                                    <h4 class="post-title">
                                                                        <a href="{{url('/single-blog')}}">Comes a cool blog post with Images</a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-button-prev"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of Widget posts -->
                                    <div class="widget widget-custom-block">
                                        <h3 class="widget-title bb-no">Custom Block</h3>
                                        <div class="widget-body">
                                            <p class="text-default mb-0">Fringilla urna porttitor rhoncus dolor purus.
                                                Luctus veneneratis lectus magna fring.
                                                Suspendisse potenti. Sed egestas, ante et 
                                                vulputate volutpat, uctus metus libero.</p>
                                        </div>
                                    </div>
                                    <!-- End of Widget custom block -->
                                    <div class="widget widget-tags">
                                        <h3 class="widget-title bb-no">Browse Tags</h3>
                                        <div class="widget-body tags">
                                            <a href="#" class="tag">Fashion</a>
                                            <a href="#" class="tag">Style</a>
                                            <a href="#" class="tag">Travel</a>
                                            <a href="#" class="tag">Women</a>
                                            <a href="#" class="tag">Men</a>
                                            <a href="#" class="tag">Hobbies</a>
                                            <a href="#" class="tag">Shopping</a>
                                            <a href="#" class="tag">Photography</a>
                                        </div>
                                    </div>
                                    <div class="widget widget-calendar">
                                        <h3 class="widget-title bb-no">Calendar</h3>
                                        <div class="widget-body">
                                            <div class="calendar-container" data-calendar-options="{
                                                'dayExcerpt': 1
                                            }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
            <!-- End of Page Content -->
        </main>
        <!-- End of Main -->
@endsection
        