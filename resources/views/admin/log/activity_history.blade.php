@extends('admin/layouts.backend')
@section('title', 'Activity Log History')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Activity Log History</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Activity Log History</li>
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
                <h3 class="card-title">Activity Log History</h3>
      
            </div>
            <div class="card-body">
				<table id="logHistoryTable" class="table table-hover dt-responsive nowrap w-100">
					<thead>
						<tr>
							<th>User</th>
							<th>Action</th>
							<th>Details</th>
							<th>IP address</th>
							<th>User Agent</th>
							<th>Logged In</th>
						</tr>
					</thead>
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
        $(document).ready(function() {
            // Initialize Bootstrap Switch if needed
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            // Handle change event
            $('.users-status-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                let usersId = $(this).data('id');
                let status = state ? 1 : 0;

                $.ajax({
                    url: '{{ route('users.updateStatus') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: usersId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: `User ${status == 1 ? 'Unblocked' : 'Blocked'} successfully.`,
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
                        toastr.error('Server error.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.delete-users', function() {
            var usersId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this users. Every informtation under this users will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit delete request via form or AJAX
                    $('#delete-form-' + usersId).submit();
                }
            });
        });
    </script>

  <script>
    $(document).ready(function () {
        $('#logHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.activityLlogHistory") }}',
            columns: [
                { data: 'user', name: 'user' },
                { data: 'action', name: 'action' },
                { data: 'details', name: 'details' },
                { data: 'ip_address', name: 'ip_address' },
                { data: 'user_agent', name: 'user_agent' },
                { data: 'logged_at', name: 'logged_at' },
            ],

            drawCallback: function () {
                $("input[data-bootstrap-switch]").each(function () {
                    $(this).bootstrapSwitch('state', $(this).prop('checked'));
                });
            }
        });
    });
</script>


@endsection
