@extends('admin/layouts.backend')
@section('title', 'All Languages')
@section('content')
<div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>All Languages</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">All Languages</li>
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
								<h3 class="card-title">All Languages</h3>
									<a href="{{ route('languages.create') }}" class="btn btn-success btn float-right">
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

							<table id="languageTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
								<thead>
									<tr>
										<th>Name</th>
										<th>Code</th>
										<th>Image</th>
										<th>Is default</th>
										<th>Created At</th>
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
        $(document).on('click', '.delete-users', function() {
            var usersId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this language. Every informtation under this language will be deleted.",
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
        $('#languageTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("languages.index") }}',
            columns: [
                { data: 'name', name: 'name'},
                { data: 'code', name: 'code' },
                { data: 'image', name: 'image' },
                { data: 'is_default', name: 'is_default' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
