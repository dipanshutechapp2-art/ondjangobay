@extends('layouts.app_inner')
@section('title', 'Recent Search History')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Recent Search History</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Recent Search History</li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of PageContent -->
            <div class="page-content pt-2">
            
                <div class="container">
                    <div class="tab tab-vertical row gutter-lg">
                       @include('account.sidebar')
               
                        <div class="tab-content mb-6">
                            <div class="tab-pane active in">
                                @if($recentSearches->isNotEmpty())
                                
									<h3> <i class="w-icon-orders"></i> Recent Search History</h3>
									@if (session('success'))
										<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
											{{ session('success') }}
										</div>
									@endif

									@if (session('error'))
										<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
											{{ session('error') }}
										</div>
									@endif
									<table class="shop-table account-orders-table mb-6">
										<thead>
											<tr>
												<th align="left" class="order-id">Keyword</th>
												<th align="left" class="order-id">Date</th>
											</tr>
										</thead>
										<tbody>
											@if($recentSearches->isNotEmpty())
												@foreach($recentSearches as $searchData)
												<tr>
													<td class="order-id"><a href="{{url('/shop')}}?search={{$searchData->query}}" target="_blank">{{$searchData->query ?? ""}}</a></td>
													<td class="order-date">{{date('M d, Y',strtotime($searchData->created_at))}}</td>
												</tr>
												@endforeach
											@endif
											
										</tbody>
									</table>
									@if($recentSearches->hasPages())
										<div class="d-flex justify-content-center">
											{{ $recentSearches->links('pagination::bootstrap-5') }}
										</div>
									@endif
									
								@else
									<p>No orders.</p><br/>
								@endif

                                <a href="{{url('/shop')}}" class="btn btn-dark btn-rounded btn-icon-right">Go
                                    Shop<i class="w-icon-long-arrow-right"></i></a>

								
								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
		<script>
			function reorderAlert() {
				return confirm('Do you want to reorder?');
			}
		</script>
@endsection
        