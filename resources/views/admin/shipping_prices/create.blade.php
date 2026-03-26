@extends('admin/layouts.backend')
@section('title', ' Shipping Prices')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper admin-dashboard-content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Shipping Prices</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Shipping Prices</li>
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
                <h3 class="card-title">Shipping Prices</h3>
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
				<form role="form" action="{{ route('shipping-prices.store') }}" method="POST" enctype='multipart/form-data'>
				@CSRF
				<div class="box-body">
				{{-- <div class="form-group">
						<label for="exampleInputEmail1">All Countries (Global)</label>
						<select name="country_id"  class="form-control @error('country_id') is-invalid @enderror">
						  <option value="">All Countries (Global)</option>
						  @foreach($countries as $country)
							<option value="{{ $country->id }}">{{ $country->name }}</option>
						  @endforeach
						</select>
					</div> --}}
					
					<div class="form-group">
						<label for="exampleInputEmail1">Shipping Options</label>
						<select name="shipping_option_id"  class="form-control @error('shipping_option_id') is-invalid @enderror" required>
						  <option value="">-Select-</option>
						  @foreach($options as $option)
							<option value="{{ $option->id }}">{{ $option->title }}</option>
						  @endforeach
						</select>
					</div>
					
					<div class="form-group">
						<label for="exampleInputEmail1">Price</label>
						<input name="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" required>
					</div>
					<div class="form-group">
						<label for="exampleInputEmail1">ETA Min Days</label>
						<input name="eta_min" class="form-control @error('eta_min') is-invalid @enderror" placeholder="ETA Min" required>
					</div>
					<div class="form-group">
						<label for="exampleInputEmail1">ETA Max Days</label>
						<input name="eta_max" class="form-control @error('eta_max') is-invalid @enderror" placeholder="ETA Max" required>
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
