@extends('layouts.app_inner')
@section('title', 'Contact Us')
@section('content')
<!-- Start of Main -->
<main class="main">
    <!-- Start of Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title mb-0">Contact Us</h1>
        </div>
    </div>
    <!-- End of Page Header -->

    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav mb-10 pb-1">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{url('/')}}">Home</a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </nav>
    <!-- End of Breadcrumb -->

    <!-- Start of PageContent -->
    <div class="page-content contact-us">
        <div class="container">
            <section class="content-title-section mb-10">
                <h3 class="title title-center mb-3">Contact
                    Information
                </h3>
                <p class="text-center">Lorem ipsum dolor sit amet,
                    consectetur
                    adipiscing elit, sed do eiusmod tempor incididunt ut</p>
            </section>
            <!-- End of Contact Title Section -->

            <div class="row mb-10 justify-content-center">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="contact-grid">
                        <span class="icon-box-icon icon-email">
                            <i class="w-icon-envelop-closed"></i>
                        </span>
                        <div class="icon-box-content">
                            <h4 class="icon-box-title">E-mail Address</h4>
                            <p><a href="mailto:info@ondjango.co.ao" class="__cf_email__">info@ondjango.co.ao</a></p>
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="contact-grid">
                        <span class="icon-box-icon icon-headphone">
                            <i class="w-icon-headphone"></i>
                        </span>
                        <div class="icon-box-content">
                            <h4 class="icon-box-title">Phone Number</h4>
                            <p>(123) 456-7890 / (123) 456-9870</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="contact-grid">
                        <span class="icon-box-icon icon-map-marker">
                            <i class="w-icon-map-marker"></i>
                        </span>
                        <div class="icon-box-content">
                            <h4 class="icon-box-title">Address</h4>
                            <p>Lawrence, NY 11345, USA</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="contact-grid">
                        <span class="icon-box-icon icon-fax">
                            <i class="w-icon-fax"></i>
                        </span>
                        <div class="icon-box-content">
                            <h4 class="icon-box-title">Fax</h4>
                            <p>1-800-570-7777</p>
                        </div>
                    </div>
                </div>
            </div>

            <section class="contact-information-section mb-10 d-none">
                <div class=" swiper-container swiper-theme " data-swiper-options="{
                            'spaceBetween': 20,
                            'slidesPerView': 1,
                            'breakpoints': {
                                '480': {
                                    'slidesPerView': 2
                                },
                                '768': {
                                    'slidesPerView': 3
                                },
                                '992': {
                                    'slidesPerView': 4
                                }
                            }
                        }">
                    <div class="swiper-wrapper row cols-xl-4 cols-md-3 cols-sm-2 cols-1">
                        <div class="swiper-slide icon-box text-center icon-box-primary">
                            <span class="icon-box-icon icon-email">
                                <i class="w-icon-envelop-closed"></i>
                            </span>
                            <div class="icon-box-content">
                                <h4 class="icon-box-title">E-mail Address</h4>
                                <p><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="e588848c89a5809d8488958980cb868a88">[email&#160;protected]</a></p>
                            </div>
                        </div>
                        <div class="swiper-slide icon-box text-center icon-box-primary">
                            <span class="icon-box-icon icon-headphone">
                                <i class="w-icon-headphone"></i>
                            </span>
                            <div class="icon-box-content">
                                <h4 class="icon-box-title">Phone Number</h4>
                                <p>(123) 456-7890 / (123) 456-9870</p>
                            </div>
                        </div>
                        <div class="swiper-slide icon-box text-center icon-box-primary">
                            <span class="icon-box-icon icon-map-marker">
                                <i class="w-icon-map-marker"></i>
                            </span>
                            <div class="icon-box-content">
                                <h4 class="icon-box-title">Address</h4>
                                <p>Lawrence, NY 11345, USA</p>
                            </div>
                        </div>
                        <div class="swiper-slide icon-box text-center icon-box-primary">
                            <span class="icon-box-icon icon-fax">
                                <i class="w-icon-fax"></i>
                            </span>
                            <div class="icon-box-content">
                                <h4 class="icon-box-title">Fax</h4>
                                <p>1-800-570-7777</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End of Contact Information section -->

            <hr class="divider mb-10 pb-1">

            <section class="contact-section">
                <div class="row gutter-lg pb-3">
                    <div class="col-lg-6 mb-8">
                        <h4 class="title mb-3">People usually ask these</h4>
                        <div class="accordion-pro">

                            <div class="acc-item active">
                                <div class="acc-header">
                                    <span>How can I cancel my order?</span>
                                    <span class="acc-icon"></span>
                                </div>
                                <div class="acc-body">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit...</p>
                                </div>
                            </div>

                            <div class="acc-item">
                                <div class="acc-header">
                                    <span>Why is my registration delayed?</span>
                                    <span class="acc-icon"></span>
                                </div>
                                <div class="acc-body">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                </div>
                            </div>

                            <div class="acc-item">
                                <div class="acc-header">
                                    <span>What do I need to buy products?</span>
                                    <span class="acc-icon"></span>
                                </div>
                                <div class="acc-body">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
                                </div>
                            </div>

                            <div class="acc-item">
                                <div class="acc-header">
                                    <span>How can I track an order?</span>
                                    <span class="acc-icon"></span>
                                </div>
                                <div class="acc-body">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>

                            <div class="acc-item">
                                <div class="acc-header">
                                    <span>How can I get money back?</span>
                                    <span class="acc-icon"></span>
                                </div>
                                <div class="acc-body">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-6 mb-8">
                        <h4 class="title mb-3">Send Us a Message</h4>
                        <form class="form contact-us-form" action="#" method="post">
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
                            <button type="submit" class="btn btn-dark btn-rounded">Send Now</button>
                        </form>
                    </div>
                </div>
            </section>
            <!-- End of Contact Section -->
        </div>

        <!-- Google Maps - Go to the bottom of the page to change settings and map location. -->
        <div class="google-map contact-google-map d-none" id="googlemaps"></div>
        <!-- End Map Section -->
    </div>
    
    <!-- End of PageContent -->
</main>

<script>
    (function () {
    document.addEventListener("click", function (e) {

        const header = e.target.closest(".accordion-pro .acc-header");
        if (!header) return;

        const item = header.parentElement;
        const container = item.closest(".accordion-pro");
        const isActive = item.classList.contains("active");

        // sab band karo
        container.querySelectorAll(".acc-item").forEach(el => {
            el.classList.remove("active");
        });

        // agar pehle se open tha to band hi rahe
        if (!isActive) {
            item.classList.add("active");
        }

    });
})();
</script>
<!-- End of Main -->
@endsection