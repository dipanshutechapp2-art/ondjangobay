@extends('layouts.app_inner')
@section('title', 'Account details')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Page Header -->
            <div class="page-header">
                <div class="container">
                    <h1 class="page-title mb-0">Account details</h1>
                </div>
            </div>
            <!-- End of Page Header -->

            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li>Account details</li>
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
								
								      <h3> <i class="w-icon-user"></i> Account Details</h3>
                                <!--<div class="icon-box icon-box-side icon-box-light" align="left">
                                    <span class="icon-box-icon icon-account mr-2">
                                        <i class="w-icon-user"></i>
                                    </span>
                                    <div class="icon-box-content">
                                        <h4 class="icon-box-title mb-0 ls-normal">Account Details</h4>
                                    </div>
                                </div>-->
                                <form class="form account-details-form" action="{{url('/account/update_account_action')}}" method="post" enctype="multipart/form-data">
								   @CSRF
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="firstname">First name *</label>
                                                <input type="text" id="firstname" name="firstname" placeholder="First name" class="form-control form-control-md"  value="{{$userinfo->name}}" required>
												@error('firstname')
													<span class="invalid-feedback" style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname">Last name *</label>
                                                <input type="text" id="lastname" name="lastname" placeholder="Last name" class="form-control form-control-md"  value="{{$userinfo->last_name}}" required>
												@error('lastname')
													<span class="invalid-feedback"  style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="profile_pic">Profile Picture </label>
                                                <input type="file" id="profile_pic" name="profile_pic" onchange="previewImage(event)" class="form-control form-control-md">
												@error('profile_pic')
													<span class="invalid-feedback" style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
												@if(!empty($userinfo->image))
													<div style="margin-top: 10px;">
														<img id="preview" src="{{asset('/uploads/users/')}}/{{$userinfo->image}}" alt="Image Preview" style="display: block; max-width: 70px; max-height: 70px;" />
													</div>
												@else
													<div style="margin-top: 10px;">
														<img id="preview" src="#" alt="Image Preview" style="display: none; max-width: 70px; max-height: 70px;" />
													</div>
												@endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname">Date of birth </label>
                                                <input type="date" id="dob" name="dob" class="form-control form-control-md"  value="{{$userinfo->dob}}">
												@error('dob')
													<span class="invalid-feedback"  style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="display-name">Display name *</label>
                                                <input type="text" id="display-name" name="display_name" placeholder="Display name" class="form-control form-control-md mb-0" value="{{$userinfo->display_name}}" required>
												@error('display_name')
													<span class="invalid-feedback"  style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname">Gender</label>
												<select name="gender" class="form-control">
												    <option value="">-Select-</option>
												    <option value="Male"   @if($userinfo->gender=='Male') selected @endif>Male</option>
												    <option value="Female" @if($userinfo->gender=='Female') selected @endif>Female</option>
												    <option value="Other"  @if($userinfo->gender=='Other') selected @endif>Other</option>
												</select>
												@error('gender')
													<span class="invalid-feedback"  style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                    </div>
									
									<div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="display-name">Email address *</label>
                                                <input type="email" id="email_1" name="email" class="form-control form-control-md"  value="{{$userinfo->email}}" required readonly>
												@error('email')
													<span class="invalid-feedback" style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname">Phone *</label>
												<input type="number" id="phone" name="phone" class="form-control"  placeholder="phone" value="{{$userinfo->phone}}" required readonly>
												@error('phone')
													<span class="invalid-feedback"  style="color:red;" role="alert">
														<strong>{{ $message }}</strong>
													</span>
												@enderror
                                            </div>
                                        </div>
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
	<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
        