@extends('layouts.app_inner')
@section('title', 'Cancel Campaign Order')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Cancel Campaign Order</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Cancel Campaign Order</li>
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
								<br/>
								<h3> <i class="w-icon-orders"></i> Cancel Campaign Order</h3>
                                <form action="{{url('/cancel/partner_order_action')}}" method="post" enctype="multipart/form-data">
								   @CSRF
								    <input type="hidden" name="order_number" value="{{$orders->order_number}}" required />
                                    <div class="row">
										<div class="form-group mt-3">
											<label for="order-notes">Reason</label>
											<textarea class="form-control mb-0" id="order-notes" name="reason" cols="30" rows="4" placeholder="Reason about your order, e.g give us reason.." required></textarea>
									    </div>
                                    </div>
                                    </div>	
                                    <button type="submit" class="btn btn-dark btn-rounded btn-sm mb-4">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of PageContent -->
        </main>
        <!-- End of Main -->
@endsection
        