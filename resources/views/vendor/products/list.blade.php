@extends('vendor/layouts.backend')
@section('title', 'Products')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Products</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Products</li>
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
								<h3 class="card-title">Products</h3>
								
								<a href="{{ route('products.create') }}" class="btn btn-success btn float-right">
									<i class="fas fa-plus"></i> Create
								</a>
								
								<a href="{{ route('vendor.exportProducts') }}" class="btn btn-success btn float-right" style="margin-right:10px" >
									<i class="fas fa-plus"></i> Export
								</a>
								
								<form action="{{ route('vendor.importProducts') }}" method="POST" enctype="multipart/form-data"class="float-right"  style="margin-right:10px">
									@csrf
									<input type="file" name="file" required>
									<button type="submit" class="btn btn-success btn">Import (xlsx,xls,csv)</button>
								</form>
								
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
                                <table id="productsTable"
                                    class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Type</th>
                                            <th>SKU </th>
                                            <th>Price </th>
                                            <th>Quantity </th>
                                            <th>Category </th>
                                            <th>Stores </th>
                                            <th>Image </th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Is Varient</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
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
			$('#productsTable').DataTable({
				processing: true,
				serverSide: true,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				ajax: '{{ route("products.index") }}',
				columns: [
					{ data: 'name' },
					{ data: 'slug' },
					{ data: 'type' },
					{ data: 'sku' },
					{ data: 'price' },
					{ data: 'quantity' },
					{ data: 'category' },
					{ data: 'stores' },
					{ data: 'image' },
					{ data: 'status' },
					{ data: 'created_at'},
					{ data: 'is_varient', orderable: false, searchable: false },
					{ data: 'action', orderable: false, searchable: false }
				],
				drawCallback: function () {
					$('input[data-bootstrap-switch]').each(function () {
						$(this).bootstrapSwitch('destroy');
						$(this).bootstrapSwitch();
					});
				}
			});
		});
		function validateDelete() {
            return confirm('Are you sure you want to delete this service?');
        }
	</script>
	<script>
        $(document).ready(function() {
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            $(document).on('switchChange.bootstrapSwitch', '.category-status-switch', function(event, state) {
				let categoryId = $(this).data('id');
				let status = state ? 1 : 0;

				$.ajax({
					url: '{{ route('products.updateStatus') }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						id: categoryId,
						status: status
					},
					success: function(response) {
						if (response.success) {
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'success',
								title: 'Status updated successfully.',
								showConfirmButton: false,
								timer: 1500
							});
						} else {
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'error',
								title: 'Failed to update status.',
								showConfirmButton: false,
								timer: 1500
							});
						}
					},
					error: function() {
						Swal.fire({
							toast: true,
							position: 'top-end',
							icon: 'error',
							title: 'Server error.',
							showConfirmButton: false,
							timer: 1500
						});
					}
				});
			});

        });
    </script>
    <script>
        $(document).on('click', '.delete-category', function() {
            var categoryId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this product. Every informtation under this product will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + categoryId).submit();
                }
            });
        });
    </script>

@endsection
