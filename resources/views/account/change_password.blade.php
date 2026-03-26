@extends('layouts.app_inner')
@section('title', 'Change Password')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Change Password</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Change Password</li>
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
								
								      <h3> <i class="w-icon-user"></i> Change Password</h3>
                                <!--<div class="icon-box icon-box-side icon-box-light" align="left">
                                    <span class="icon-box-icon icon-account mr-2">
                                        <i class="w-icon-user"></i>
                                    </span>
                                    <div class="icon-box-content">
                                        <h4 class="icon-box-title mb-0 ls-normal">Account Details</h4>
                                    </div>
                                </div>-->
                                <form class="form account-details-form" action="{{url('/account/changePasswordAction')}}" method="post" enctype="multipart/form-data">
								   @CSRF
                                    <div class="form-group">
                                        <label class="text-dark" for="cur-password">Current Password leave blank to leave unchanged</label>
                                        <input type="password" class="form-control form-control-md" id="cur-password" name="current_password" required>
										@error('current_password')
											<span class="invalid-feedback"  style="color:red;" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="text-dark" for="new-password">New Password leave blank to leave unchanged</label>
                                        <input type="password" class="form-control form-control-md" id="new-password" name="password" required>
										@error('password')
											<span class="invalid-feedback"  style="color:red;" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                    </div>
                                    <div class="form-group mb-10">
                                        <label class="text-dark" for="conf-password">Confirm Password</label>
                                        <input type="password" class="form-control form-control-md" id="conf-password" name="password_confirmation" required>
										@error('password_confirmation')
											<span class="invalid-feedback"  style="color:red;" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@enderror
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-rounded btn-sm mb-4">Save Changes</button>
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
        