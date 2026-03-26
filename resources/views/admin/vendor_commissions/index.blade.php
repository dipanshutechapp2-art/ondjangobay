@extends('admin/layouts.backend')
@section('title', 'Vendor Commissions')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Vendor Commissions</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Vendor Commissions</li>
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
                            <div class="card-header">
                                <h3 class="card-title">Vendor Commissions </h3>
								<a href="{{ route('admin.vendor_commissions.create') }}" class="btn btn-success btn float-right">
									<i class="fas fa-plus"></i> Add Vendor
								</a>
                            </div>
							
                            <div class="card-body">
								@if(session('success'))
									<div class="alert alert-success">{{ session('success') }}</div>
								@endif
								@if(session('error'))
									<div class="alert alert-danger">{{ session('error') }}</div>
								@endif
                                <table class="table table-bordered align-middle">
									<thead>
										<tr>
											<th>ID</th>
											<th>Name</th>
											<th>Category</th>
											<th>Commission Type</th>
											<th>Commission (%)</th>
											<!--<th>Bank Account</th>-->
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@forelse($vendors as $vendor)
										<tr>
											<td>{{ $vendor->id }}</td>
											<td>{{ $vendor->name }}</td>
											<td>{{ ucfirst($vendor->category_code) }}</td>
											<td>{{ ucfirst($vendor->commission_type) }}</td>
											<td>{{ $vendor->commission_value ?? '-' }}</td>
											<!--<td>{{ $vendor->bank_account ?? '-' }}</td>-->
											<td>
												<a href="{{ route('admin.vendor_commissions.edit', $vendor->id) }}" class="btn btn-sm btn-warning">Edit</a>
												<form action="{{ route('admin.vendor_commissions.destroy', $vendor->id) }}" method="POST" class="d-inline">
													@csrf @method('DELETE')
													<button class="btn btn-sm btn-danger" onclick="return confirm('Delete this vendor?')">Delete</button>
												</form>
											</td>
										</tr>
										@empty
										<tr><td colspan="7" class="text-center">No vendors found.</td></tr>
										@endforelse
									</tbody>
								</table>
								<div class="mt-3">
									{{ $vendors->links() }}
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

    <!-- /.content-wrapper -->

	<script>
		$(document).ready(function () {
			$('#transactionTable').DataTable({
				processing: true,
				serverSide: false
			});
		});
	</script>
@endsection
