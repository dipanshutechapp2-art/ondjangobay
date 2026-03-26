@extends('admin/layouts.backend')
@section('title', 'Brand')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Brands</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Brands</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <!-- Add Menu Form -->
                    <div class="col-md-4">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Add Brands</h3>
                                            </div>
                                            <form action="{{ route('brands.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="title">Brand Name</label>
                                                        <input type="text" name="title" class="form-control"
                                                            id="category" placeholder="Enter title Name">

                                                        @error('title')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                
                                                    <div class="card-footer">

                                                        <button type="submit" class="btn btn-primary">Add</button>

                                                    </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>


                    </div>


                    <!-- Menu List -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Brands </h3>

                            </div>
                            <div class="card-body">

                                <table id="categoryTable"
                                    class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
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
                <!-- Button trigger modal -->

                <div class="modal fade" id="editserviceModal" tabindex="-1" role="dialog"
                    aria-labelledby="editserviceLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editserviceLabel">Update service</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="updateserviceForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="editserviceId">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="editTitle">service</label>
                                        <input type="text" name="service" class="form-control" id="editTitle" required>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
             
        </section>
    </div>

    <!-- /.content-wrapper -->

 
	<script>
		$(document).ready(function () {
			$('#categoryTable').DataTable({
				processing: true,
				serverSide: true,
				ajax: '{{ url('admin/brands') }}',
				columns: [
					{ data: 'title' },
					{ data: 'status' },
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
        $(document).ready(function() {
            $('.editserviceBtn').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var image = $(this).data('image');

                $('#editserviceId').val(id);
                $('#editTitle').val(title);
                $('#editPreviewImage').attr('src', image);
            });

            $('#updateserviceForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var id = $('#editserviceId').val();

                $.ajax({
                    service: '{{ url('admin / service / edit ') }}/' +
                        id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            alert('service Updated')
                            location.reload(); // Refresh to update the list
                        } else {
                            alert('Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        alert('Error updating service.');
                    }
                });
            });
        });

        function validateDelete() {
            return confirm('Are you sure you want to delete this service?');
        }
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Switch if needed
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            // Handle change event
            $(document).on('switchChange.bootstrapSwitch', '.category-status-switch', function(event, state) {
				let categoryId = $(this).data('id');
				let status = state ? 1 : 0;

				$.ajax({
					url: '{{ route('brands.updateStatus') }}',
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
                text: "You are about to delete this Category. Every informtation under this category will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit delete request via form or AJAX
                    $('#delete-form-' + categoryId).submit();
                }
            });
        });
    </script>
    #Update Category
    <script>
$(document).on('click', '.edit-btn', function () {
    let categoryId = $(this).data('id');
    let currentName = $(this).data('name');

    Swal.fire({
        title: 'Update Category Name',
        input: 'text',
        inputLabel: 'Name',
        inputValue: currentName,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: (newName) => {
            if (!newName) {
                Swal.showValidationMessage('Name is required');
            }
            return newName;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let newName = result.value;

            $.ajax({
                url: '{{ url('admin/brands') }}/' + categoryId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    name: newName
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Category name updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: xhr.responseJSON?.message || 'Server error'
                    });
                }
            });
        }
    });
});
</script>

@endsection
