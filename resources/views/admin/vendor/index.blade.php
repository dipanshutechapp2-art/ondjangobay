@extends('admin/layouts.backend')
@section('title', 'vendors')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Vendor</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Vendor</li>
                        </ol>
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
                <div class="row">

    <!-- Menu List -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title">Vendor</h3>
            <a href="{{ route('vendor.create') }}" class="btn btn-success btn float-right">
                <i class="fas fa-plus"></i> Create
            </a>
            </div>
            <div class="card-body">

                <table id="example2" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>   
                            <th>Status</th>
                            <th>Register</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    <input type="checkbox" name="status" class="users-status-switch"
                                        data-bootstrap-switch data-off-color="danger" data-on-color="success"
                                        {{ $user->status == 1 ? 'checked' : '' }} data-id="{{ $user->id }}" </td>
                                <td>
                                    {{ $user->created_at->format('d-M-Y') }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success">Action</button>
                                        <button type="button" class="btn btn-success dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item" href="{{ route('vendor.edit', $user->id) }}" data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}">
                                                Edit</a>
                                            <a class="dropdown-item delete-users" href="#"
                                                data-id="{{ $user->id }}">Delete</a>
                                            <!-- Hidden form to trigger DELETE -->
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('vendor.destroy', $user->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                        </div>
                                    </div>
                                 
                                </td>
                            </tr>
                        @endforeach
                         
                    </tbody>
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
                    url: '{{ route('vendor.updateStatus') }}',
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
     
@endsection
