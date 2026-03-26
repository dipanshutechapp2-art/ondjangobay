@extends('admin/layouts.backend')
@section('title', ' Setting')
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
              <li class="breadcrumb-item active">Setting</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Setting</h3>
              </div>
              <!-- /.card-header -->
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
				<form role="form" action="{{ route('admin.setting_action') }}" method="POST" enctype='multipart/form-data'>
				@CSRF
				<input type="hidden" name="user_id" value="{{ $data->id }}" required>
				<div class="box-body">
        <div class="form-group text-center" >
        @if($data->image)
                      <img src="{{ url('admin/images') }}/{{ $data->image }}"  alt="" width="150">
                      @else
                      <img src="{{ url('admin/images/no-image.png') }}" alt="" width="150">
                      @endif
                      <p><b>{{ $data->name }}</b></p>
							<!-- <label for="exampleInputEmail1">{{ __('Select File') }}  </label> -->
							<input id="select_files" type="file" class="form-control @error('image') is-invalid @enderror" name="image" placeholder="Select file..">
							@error('image')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
       
					  	   </div>
					<div class="form-group">
						<label for="exampleInputEmail1">Name</label>
						<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="exampleInputEmail1" placeholder="Name" value="{{ $data->name }}" >
						@error('name')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="form-group">
						<label for="exampleInputEmail1">Email</label>
						<input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail1" placeholder="Email" value="{{ $data->email }}" >
						@error('email')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
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
