@extends('admin/layouts.backend')
@section('title', 'Partner Campaigns')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
           @if (session('success'))
			<script>
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: "{{ session('success') }}",
					timer: 2000,
					showConfirmButton: false
				});
			</script>
			@endif

			@if (session('error'))
			<script>
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: "{{ session('error') }}",
					timer: 2000,
					showConfirmButton: false
				});
			</script>
			@endif

            <div class="row mb-2">
                <div class="col-sm-6"><h1>Partner Campaigns</h1></div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('partner-campaigns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Campaign
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
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
            <div class="card">
                <div class="card-header"><h3 class="card-title">Campaign List</h3></div>
                <div class="card-body">
                    <table id="campaignTable" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                {{-- <th>Frequency</th> --}}
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Upload Deadline</th>
                                <th>Min Value</th>
                                <th>Min Qty</th>
								{{-- <th>Goal Qty</th> --}}
                                <th>Category</th>
								{{-- <th>Cart Timer</th> --}}
                                <th>Cart Max Volume</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#campaignTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("partner-campaigns.index") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            /* { data: 'frequency', name: 'frequency' },  */
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'upload_deadline', name: 'upload_deadline' },
            { data: 'min_value', name: 'min_value' },
            { data: 'min_quantity', name: 'min_quantity' },
            /* { data: 'goal_quantity', name: 'goal_quantity' }, */
            { data: 'category', name: 'category' },
            /* { data: 'cart_timer_minutes', name: 'cart_timer_minutes' }, */
            { data: 'cart_max_volume', name: 'cart_max_volume' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });

    $(document).on('click', '.delete-campaign', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "This campaign will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection
