@extends('admin/layouts.backend')
@section('title', 'Edit Shipping Prices')
@section('content')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper admin-dashboard-content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Shipping Prices</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Edit Shipping Prices</li>
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
				<form action="{{ route('shipping-prices.update', $price->id) }}" method="POST">
					@csrf
					@method('PUT')

					{{-- <div class="form-group">
						<label>Country</label>
						<select name="country_id" class="form-control">
							<option value="">All Countries (Global)</option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}"
									{{ $price->country_id == $country->id ? 'selected' : '' }}>
									{{ $country->name }}
								</option>
							@endforeach
						</select>
					</div> --}}

					<div class="form-group">
						<label>Shipping Option</label>
						<select name="shipping_option_id" class="form-control" required>
							@foreach($options as $option)
								<option value="{{ $option->id }}"
									{{ $price->shipping_option_id == $option->id ? 'selected' : '' }}>
									{{ $option->title }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Price</label>
						<input type="text" name="price" class="form-control"
							   value="{{ $price->price }}" required>
					</div>

					<div class="form-group">
						<label>ETA Min Days</label>
						<input type="number" name="eta_min" class="form-control"
							   value="{{ $price->eta_min }}" required>
					</div>

					<div class="form-group">
						<label>ETA Max Days</label>
						<input type="number" name="eta_max" class="form-control"
							   value="{{ $price->eta_max }}" required>
					</div>

					{{-- 🔢 Display Order --}}
					<div class="form-group">
						<label>Display Order</label>
						<input type="number" name="sort_order" class="form-control"
							   value="{{ $price->sort_order ?? 0 }}">
					</div>

					{{-- ⭐ Default --}}
						{{-- <div class="form-check mb-3">
						<input type="checkbox"
							   name="is_default"
							   value="1"
							   class="form-check-input"
							   {{ $price->is_default ? 'checked' : '' }}>
						<label class="form-check-label">
							Set as Default Shipping
						</label>
					</div> --}}

					<button class="btn btn-primary">Update</button>
					<a href="{{ route('shipping-prices.index') }}" class="btn btn-secondary">
						Cancel
					</a>

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
