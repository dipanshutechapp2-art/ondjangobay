@extends('admin/layouts.backend')
@section('title', 'Transactions')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Transactions</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Transactions</li>
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
                                <h3 class="card-title">Transactions </h3>
                            </div>
							
                            <div class="card-body">
								@if(session('success'))
									<div class="alert alert-success">{{ session('success') }}</div>
								@endif
								@if(session('error'))
									<div class="alert alert-danger">{{ session('error') }}</div>
								@endif
								<!--<div class="d-flex justify-content-between align-items-center mb-3">
									<form method="GET" class="d-flex gap-2">
										<select name="vendor_id" class="form-select">
											<option value="">All Vendors</option>
											@foreach($vendors as $vendor)
												<option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
													{{ $vendor->name }}
												</option>
											@endforeach
										</select>
										<input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
										<input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
										<button type="submit" class="btn btn-secondary">Filter</button>
									</form>
								</div>-->
							
                                <table id="transactionTable" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
											<th>Order ID</th>
											<th>Vendor</th>
											<th>Product</th>
											<th>Vendor Amount</th>
											<th>Ondjango Commission</th>
											<th>Commission rate</th>
											<th>Payment Method</th>
											<th>Payment flow</th>
											<th>Payment note</th>
											<th>Vendor type</th>
											<th>Status</th>
											<th>Timestamp</th>
											<th>Actions</th>
                                        </tr>
                                    </thead>
									<tbody>
										@forelse($transactions as $txn)
										<tr>
											<td>{{ $txn->id }}</td>
											<td>{{ $txn->order_id }}</td>
											<td>{{ $txn->vendor->name }}</td>
											<td>{{ $txn->product->name }}</td>
											<td>{{ number_format($txn->vendor_amount, 2) }}</td>
											<td>{{ number_format($txn->ondjango_commission, 2) }}</td>
											<td>{{ number_format($txn->commission_rate, 2) }}</td>
											<td>{{ $txn->payment_method }}</td>
											<td>{{ $txn->payment_flow ?? "" }}</td>
											<td>{{ $txn->payment_note ?? "" }}</td>
											<td>{{ $txn->vendor_type ?? "" }}</td>
											<td>
												<span class="badge bg-{{ $txn->status == 'paid' ? 'success' : ($txn->status == 'refunded' ? 'danger' : 'warning') }}">
													{{ ucfirst($txn->status) }}
												</span>
											</td>
											<td>{{ $txn->created_at->format('d M Y H:i') }}</td>
											<td>
												@if($txn->vendor->category_code === 'internal' && $txn->status === 'paid')
													<form method="POST" action="{{ route('admin.transactions.settle', $txn->id) }}" class="d-inline">
														@csrf
														<button class="btn btn-sm btn-primary">Settle</button>
													</form>
												@endif

												@if($txn->status !== 'refunded')
													<form method="POST" action="{{ route('admin.transactions.refund', $txn->id) }}" class="d-inline">
														@csrf
														<button class="btn btn-sm btn-danger" onclick="return confirm('Refund this transaction?')">Refund</button>
													</form>
												@endif
											</td>
										</tr>
										@empty
										<tr><td colspan="10" class="text-center">No transactions found.</td></tr>
										@endforelse
									</tbody>
                                </table>
								<div class="mt-3">
									{{ $transactions->withQueryString()->links() }}
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
	<!--<script>
		$(document).ready(function () {
			$('#transactionTable').DataTable({
				processing: true,
				serverSide: false,
				ajax: '{{ url('admin/newsletters') }}',
				columns: [
					{ data: 'email' },
					{ data: 'created_at' },
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
	</script>

    <script>
        $(document).on('click', '.delete-category', function() {
            var categoryId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this Category. Every informtation under this category will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + categoryId).submit();
                }
            });
        });
    </script>-->
@endsection
