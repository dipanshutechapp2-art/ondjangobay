@extends('layouts.app_inner')
@section('title', 'Stores')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb mb-6">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/vendor')}}">Vendor</a></li>
                        <li>Store List</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of Pgae Contetn -->
            <div class="page-content mb-10 pb-2">
                
                
                
             <!--store listing -->
             <div class="store-listing">
                 <div class="container">
                     <div class="row justify-content-center">
                         <div class="col-md-3">
                             <div class="store-list-left">
                                 <div class="store-list-filter">
                                     <h3 class="store-filter-title">Categories</h3>
                                     <ul class="filter-list-items">
                                         <li><a href="">Clothing</a></li>
                                         <li><a href="">Electronics</a></li>
                                         <li><a href="">Groceries</a></li>
                                         <li><a href="">Jewelry</a></li>
                                         <li><a href="">Sports</a></li>
                                         <li><a href="">Shoes</a></li>
                                     </ul>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-9">
                             <div class="store-list-right">
                                  
                                  <div class="store-grid-row row">
                                      <div class="col-sm-6 col-md-6 col-lg-4">
                                          <div class="store-grids">
                                              
                                             <div class="store-images-wrapper">
                                                  <img src="https://marketplace.dipanshutech.co.in/uploads/store/1752040142.png" class="store-image" / >
                                             </div>
                                              
                                              <div class="store-short-content">
                                                  <h5 class="store-name">Bike Vendor Stores</h5>
                                                  <p>ESCX Garment Store is your go-to destination for stylish, comfortable, and high-quality clothing for all occasions....</p>
                                                  <a href="#" class="btn btn-primary btn-outline light visit-store-btn">Visit Store</a>
                                                  
                                              </div>
                                          </div>
                                      </div>
                                      
                                       <div class="col-sm-6 col-md-6 col-lg-4">
                                          <div class="store-grids">
                                              
                                             <div class="store-images-wrapper">
                                                  <img src="https://marketplace.dipanshutech.co.in/uploads/store/1752040142.png" class="store-image" / >
                                             </div>
                                              
                                              <div class="store-short-content">
                                                  <h5 class="store-name">Bike Vendor Stores</h5>
                                                  <p>ESCX Garment Store is your go-to destination for stylish, comfortable, and high-quality clothing for all occasions....</p>
                                                  <a href="#" class="btn btn-primary btn-outline light visit-store-btn">Visit Store</a>
                                                  
                                              </div>
                                          </div>
                                      </div>
                                       <div class="col-sm-6 col-md-6 col-lg-4">
                                          <div class="store-grids">
                                              
                                             <div class="store-images-wrapper">
                                                  <img src="https://marketplace.dipanshutech.co.in/uploads/store/1752040142.png" class="store-image" / >
                                             </div>
                                              
                                              <div class="store-short-content">
                                                  <h5 class="store-name">Bike Vendor Stores</h5>
                                                  <p>ESCX Garment Store is your go-to destination for stylish, comfortable, and high-quality clothing for all occasions....</p>
                                                  <a href="#" class="btn btn-primary btn-outline light visit-store-btn">Visit Store</a>
                                                  
                                              </div>
                                          </div>
                                      </div>
                                      
                                       <div class="col-sm-6 col-md-6 col-lg-4">
                                          <div class="store-grids">
                                              
                                             <div class="store-images-wrapper">
                                                  <img src="https://marketplace.dipanshutech.co.in/uploads/store/1752040142.png" class="store-image" / >
                                             </div>
                                              
                                              <div class="store-short-content">
                                                  <h5 class="store-name">Bike Vendor Stores</h5>
                                                  <p>ESCX Garment Store is your go-to destination for stylish, comfortable, and high-quality clothing for all occasions....</p>
                                                  <a href="#" class="btn btn-primary btn-outline light visit-store-btn">Visit Store</a>
                                                  
                                              </div>
                                          </div>
                                      </div>
                                      
                                      
                                      
                                  </div>
                                  
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!--store listing end -->
                
                
                
                
                
                
                
                <div class="container">
                    <!-- Start of Vendor Toolbox -->
                    <!--<div class="toolbox vendor-toolbox pb-0">
                        <div class="toolbox-left mb-4 mb-md-0">
                            <a href="#" class="btn btn-primary btn-outline btn-rounded btn-icon-left vendor-search-toggle "><i class="w-icon-category"></i>Filter</a>
                            <label class="d-block">Total Store Showing {{$stores->count()>0 ? $stores->count() :0}}</label>
                        </div>
                        <div class="toolbox-right">
                            <div class="toolbox-item toolbox-sort select-box mb-0">
                                <label class="font-weight-normal">Sort by:</label>
                                <select name="orderby" class="form-control">
                                    <option value="default" selected="selected">Default</option>
                                    <option value="recent">Most Recent</option>
                                    <option value="popular">Most Popular</option>
                                </select>
                            </div>
                            <div class="toolbox-item toolbox-layout mb-0 d-flex">
                                <a href="{{url('/vendor')}}" class="icon-mode-grid btn-layout">
                                    <i class="w-icon-grid"></i>
                                </a>
                                <a href="{{url('/vendor')}}" class="icon-mode-list btn-layout active">
                                    <i class="w-icon-list"></i>
                                </a>
                            </div>
                        </div>
                    </div>-->
                    <!-- End of Vendor Toolbox -->

                    <!-- Start of Vendor Search Wrapper -->
                    <!--<div class="vendor-search-wrapper">
                        <form class="vendor-search-form">
                            <input type="email" class="form-control mr-4 bg-white" name="vendor" id="vendor" placeholder="Search Vendors">
                            <button class="btn btn-primary btn-rounded" type="submit">Apply</button>
                        </form>
                    </div>-->
                    <!-- End of Vendor Search Wrapper -->
				@if($stores->count()>0)
					@foreach($stores as $storeList)
				
					 @php 
						$reviewCount   = $storeList->review_count;
						$averageRating = number_format($storeList->average_rating, 1);
						$ratingPercent = $averageRating * 20;
					@endphp
				
                    <div class="store store-list mt-4">
                        <div class="store-header">
                            <a href="{{url('/store/product/')}}/{{$storeList->slug}}">
                                <figure class="store-banner">
                                    <img src="{{asset('uploads/store/')}}/{{$storeList->logo}}" alt="Vendor" width="200" height="100" style="background-color: #40475E;">
                                </figure>
                            </a>
                            <label class="featured-label">Featured</label>
                        </div>
                        <!-- End of Store Header -->
                        <div class="store-content">
                            <!--<figure class="seller-brand">
                                <img src="{{asset('frontend/assets/images/vendor/brand/1-1.jpg')}}" alt="Brand" width="80" height="80">
                            </figure>-->
                            <div class="seller-date">
                                <h4 class="store-title">
                                    <a href="{{url('/store/product/')}}/{{$storeList->slug}}">{{$storeList->store_name}}</a>
                                </h4>
                                <div class="ratings-container">
                                    <div class="ratings-full">
                                        <span class="ratings" style="width: {{$ratingPercent}}%;"></span>
                                        <span class="tooltiptext tooltip-top"></span>
                                    </div>
									<a href="{{url('/store/product/')}}/{{$storeList->slug}}" class="rating-reviews">({{$reviewCount}} Reviews)</a>
                                </div>
                                <div class="store-address">
                                   {!!$storeList->description!!}
                                </div>
                                <a href="{{url('/store/product/')}}/{{$storeList->slug}}" class="btn btn-visit-store btn-link btn-underline btn-icon-right btn-visit">
                                    Visit Store<i class="w-icon-long-arrow-right"></i></a>
                            </div>
                        </div>
                        <!-- End of Store Content -->
                    </div>
					@endforeach
					{{ $stores->links('pagination::bootstrap-5') }}
                @else 
                   <p>No storec</p>					
                @endif  
							
                </div>
            </div>
            <!-- End of Page Content -->
        </main>
        <!-- End of Main -->
@endsection
        