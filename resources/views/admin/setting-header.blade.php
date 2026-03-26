@extends('admin/layouts.backend')
@section('title', 'Setting')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper admin-dashboard-content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Setting</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Header Setting</li>
            </ol>
          </div>
        </div>
      </div>
    </section>


    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All Setting</h3>
              </div>

              <div class="card-body">
                @if(session()->has('success'))
					<div class="alert alert-success">
					  <strong>Success!</strong> {{ session()->get('success') }}
					</div>
				@endif
				@if(session()->has('error'))
					<div class="alert alert-danger">
						<strong>Warning!</strong> {{ session()->get('error') }}
					</div>
				@endif
				<form role="form" action="{{ url('admin/header-setting-action') }}" method="POST"  enctype='multipart/form-data'>
					@CSRF

					@if(isset($settingInfo->id))
					   <input type="hidden" name="setting_id" value="{{ $settingInfo->id }}" required>
					@endif
					<div class="box-body">
						<div class="form-group">
							<label for="exampleInputEmail1">Email</label>
							@if(isset($settingInfo->email))
							   <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email.." value="{{ $settingInfo->email }}">
							@else
								<input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email.." value=" ">
							@endif
						</div>
						<div class="form-group">
							<label for="exampleInputEmail1">{{ __('Header Logo') }}</label>
							<input id="files" type="file" class="form-control @error('header_logo') is-invalid @enderror" name="header_logo" placeholder="Header Logo..">
							@error('header_logo')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
							<br/>
							@if(!empty($settingInfo->header_logo))
							   <img src="{{ $settingInfo->header_logo }}" height="70">
							@endif
						</div>
                        <div class="form-group">
							<label for="exampleInputEmail1">Footer Logo</label>
							<input id="files" type="file" class="form-control @error('banner') is-invalid @enderror" name="footer_logo" placeholder="footer Logo..">
              @if(!empty($settingInfo->footer_logo))
              <img src="{{$settingInfo->footer_logo}}" height="70" class="mt-3">
              @endif
						</div>


                        <div class="form-group">
							<label for="exampleInputEmail1">Address</label>
              @if(isset($settingInfo->address))
							   <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address.." value="{{$settingInfo->address}} ">
                 @else
                 <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address.." value=" ">
                 @endif
						</div>
                        <div class="form-group">
							<label for="exampleInputEmail1">number</label>
              @if(isset($settingInfo->number))
							   <input type="text" name="number" class="form-control" id="exampleInputEmail1" placeholder="Enter number.." value="{{$settingInfo->number}} ">
                 @else
                 <input type="text" name="number" class="form-control" id="exampleInputEmail1" placeholder="Enter number" value=" ">
                 @endif
						</div>





					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

@endsection
