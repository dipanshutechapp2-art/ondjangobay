@extends('admin/layouts.backend')
@section('title', 'Product Commissions')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Product Commissions</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Product Commissions</li>
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
                                <h3 class="card-title">Product Commissions </h3>
								<a href="{{ route('admin.product_commissions.create') }}" class="btn btn-success btn float-right">
									<i class="fas fa-plus"></i> Add Product
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
											<th>Product Name</th>
											<th>Vendor</th>
											<th>Commission Type</th>
											<th>Commission (%)</th>
											<!--<th>Origin</th>
											<th>Shipping Condition</th>-->
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										@forelse($products as $product)
										<tr>
											<td>{{ $product->id }}</td>
											<td>{{ $product->name }}</td>
											<td>{{ $product->vendor->name ?? '—' }}</td>
											<td>{{ ucfirst($product->commission_type) }}</td>
											<td>{{ $product->commission_value ?? '-' }}</td>
											<!--<td>{{ $product->origin ?? '-' }}</td>
											<td>{{ $product->shipping_condition ?? '-' }}</td>-->
											<td>
												<a href="{{ route('admin.product_commissions.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
												<form action="{{ route('admin.product_commissions.destroy', $product->id) }}" method="POST" class="d-inline">
													@csrf @method('DELETE')
													<button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
												</form>
											</td>
										</tr>
										@empty
										<tr><td colspan="9" class="text-center">No products found.</td></tr>
										@endforelse
									</tbody>
								</table>
								<div class="mt-3">
									{{ $products->links() }}
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
