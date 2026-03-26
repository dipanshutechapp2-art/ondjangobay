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
                     <div class="row">
                         <div class="col-md-3">
                             <div class="store-list-left">
								<div class="filter-actions mb-4">
									<label>Filter :</label>
									<a href="{{url('/stores')}}" class="btn btn-dark btn-link" style="padding-bottom: 0;text-transform: capitalize;font-weight: 400;background-color: transparent;color: #333;border-color: #333;">Clear All</a>
								</div>
                                 <div class="store-list-filter">
                                     <h3 class="store-filter-title">Categories</h3>
                                  <ul class="filter-list-items">
    <li class="{{ request('category') ? '' : 'active' }}">
        <a href="{{ url('/stores') }}">All</a>
    </li>
    @if(!empty($storeCategories))
        @foreach($storeCategories as $category)
            <li class="{{ request('category') == $category->id ? 'active' : '' }}">
                <a href="{{ url('/stores') }}?category={{ $category->id }}">
                    {{ $category->name ?? "" }}
                </a>
            </li>
        @endforeach
    @endif
</ul>

                                 </div>
                             </div>
                         </div>
                        <div class="col-md-9">
                            <div class="store-list-right">
                                <div class="store-grid-row row">
									@if($stores->count()>0)
										@foreach($stores as $storeList)
											@php 
												$reviewCount   = $storeList->review_count;
												$averageRating = number_format($storeList->average_rating, 1);
												$ratingPercent = $averageRating * 20;
											@endphp
											<div class="col-sm-6 col-md-6 col-lg-4">
											  <div class="store-grids">  
													<div class="store-images-wrapper">
														<img src="{{asset('uploads/store/')}}/{{$storeList->logo}}" class="store-image" / >
													</div>   
													<div class="store-short-content">
													  <h5 class="store-name">{{$storeList->store_name ?? ""}}</h5>
													  <p class="store-short-description-text">{!!Str::limit($storeList->description, 50)!!} </p>
													  <a href="{{url('/store/product/')}}/{{$storeList->slug}}" class="btn btn-primary btn-outline light visit-store-btn">Visit Store</a>
													</div>
												</div>
											</div>
										@endforeach
									@endif
									@if($stores->hasPages())
										{{ $stores->links('pagination.custom') }}
									@endif
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
                </div>
            </div>
            <!-- End of Page Content -->
        </main>
        <!-- End of Main -->
@endsection
        