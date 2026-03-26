@extends('admin/layouts.backend')
@section('title', 'Shipping Options')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Shipping Options</h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Shipping Options</li>
                        </ol>
                    </div>

                </div>
            </div>
        </section>
		
        <section class="content">
            <div class="container-fluid">
					  <!-- Bootstrap Alerts -->
				@if (session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> {{ session('success') }}
					</div>
				@endif

				@if (session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error!</strong> {{ session('error') }}
					</div>
				@endif

				@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Please fix the following errors:</strong>
						<ul class="mt-2 mb-0">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
                <div class="row">

    <!-- Menu List -->
    <div class="col-md-12">
        <div class="card">
		{{-- <div class="card-header ">
                <h3 class="card-title">Shipping Options</h3>
				<a href="{{ route('shipping-options.create') }}" class="btn btn-success btn float-right">
					<i class="fas fa-plus"></i> Create
				</a>
            </div> --}}
            <div class="card-body">
				<table id="shippingOptionsTable" class="table table-hover dt-responsive nowrap w-100">
					<thead>
						<tr>
							<th>Title</th>
							<th>Code</th>
							<th>Carrier</th>
							<th>Description</th>
						</tr>
					</thead>
					@foreach($shippingOptions as $shippingOption)
						<tr>
						  <td>{{ $shippingOption->title }}</td>
						  <td>{{ $shippingOption->code }}</td>
						  <td>{{ $shippingOption->default_carrier }}</td>
						  <td>{{ $shippingOption->description }}</td>
						</tr>
					@endforeach
				</table>
            </div>
        </div>
    </div>
    <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
    <!-- Button trigger modal -->

      
    </div>

    </section>
    </div>
	<script>
    $(document).ready(function () {
        $('#shippingOptionsTable').DataTable({
            processing: true,
            serverSide: false,
        });
    });
</script>
@endsection
