@extends('vendor/layouts.backend')
@section('title', 'All Store')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>All Store</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">All Store</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Menu List -->
                    <div class="col-md-12">
                        <div class="card">
							<div class="card-header ">
								<h3 class="card-title">All Store</h3>
									<a href="{{ route('vendor.vendorstore.add') }}" class="btn btn-success btn float-right">
										<i class="fas fa-plus"></i> Create
									</a>
							</div>

						<div class="card">
 
						<div class="card-body">
							@if(session()->has('success'))
								<div class="alert alert-success">
									<strong>Success!</strong> {{ session()->get('success') }}
								</div>
							@endif
							@if(session()->has('error'))
								<div class="alert alert-danger">
									<strong>Error!</strong> {{ session()->get('error') }}
								</div>
							@endif

							<table id="storeTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
								<thead>
									<tr>
										<th>Logo</th>
										<th>Store Name</th>
										<th>Vendor Name</th>
										<th>Description</th>
										<th>Status</th>
										<th>Created At</th>
										<th>Updated At</th>
										<th>Action</th>
									</tr>
								</thead>
							</table>
					</div>
				</div>
						</div>	
					</div>	
						
                    </div>
                    <!-- /.col-md-8 -->
                </div>
                <!-- /.row -->
            </div>
             
        </section>
    </div>
	
	<script>
    $(document).ready(function () {
        $('#storeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("vendor.vendorstore.show") }}',
            columns: [
                { data: 'logo', name: 'logo', orderable: false, searchable: false },
                { data: 'store_name', name: 'store_name' },
                { data: 'vendor_name', name: 'vendor_name' },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
		function validateDelete() {
            return confirm('Are you sure you want to delete this service?');
        }

</script>



@endsection
