@extends('admin/layouts.backend')
@section('title', 'Blog Category')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Blog Category</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Blog Category</li>
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
                                                <h3 class="card-title">Add Blog Category</h3>
                                            </div>
                                            <form action="{{ route('blog-category.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="card-body">

                                                    <div class="form-group">
                                                        <label for="title">Name</label>
                                                        <input type="text" name="name" class="form-control"
                                                            id="location" placeholder="Enter Blog Category Name">

                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="image">Image</label>
                                                        <input type="file" name="image" class="form-control"
                                                            id="image">

                                                        @error('image')
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
                    </div>
        </section>
    </div>


    <!-- Menu List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title">Blog Category</h3>
               
            </div>
            <div class="card-body">

                <table id="example2" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>

                                <td>{{ $category->name }} </td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    @if ($category->image)
                                        <img src="{{ asset('uploads/blog-category/' . $category->image) }}"
                                            alt="{{ $category->name }}" width="50" height="50">
                                    @else
                                        <img src="{{ asset('images/default.png') }}" alt="Default Image" width="50"
                                            height="50">
                                    @endif
                                </td>
                                {{-- <td>{{ $category->status == 1 ? 'Active' : 'Deactive' }}</td> --}}
                                  <td>
                                    <input type="checkbox" name="status" class="blog-category-status-switch"
                                    data-bootstrap-switch data-off-color="danger" data-on-color="success"
                                    {{ $category->status == 1 ? 'checked' : '' }} data-id="{{ $category->id }}" </td>
                                  <td>

                                   <div class="btn-group">
                                        <button type="button" class="btn btn-success">Action</button>
                                        <button type="button" class="btn btn-success dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item edit-btn" href="#" data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}">
                                                Edit</a>

                                            <a class="dropdown-item delete-blog-category" href="#"
                                                data-id="{{ $category->id }}">Delete</a>
                                            <!-- Hidden form to trigger DELETE -->
                                            <form id="delete-form-{{ $category->id }}"
                                                action="{{ route('blog-category.destroy', $category->id) }}" method="POST"
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

    <div class="modal fade" id="editserviceModal" tabindex="-1" role="dialog" aria-labelledby="editserviceLabel"
        aria-hidden="true">
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


    <script>
        $(document).ready(function() {
            // Initialize Bootstrap Switch if needed
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            // Handle change event
            $('.blog-category-status-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                let locationId = $(this).data('id');
                let status = state ? 1 : 0;

                $.ajax({
                    url: '{{ route('blog-category.updateStatus') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: locationId,
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
                        toastr.error('Server error.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', '.delete-blog-category', function() {
            var locationId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this category. Every informtation under this category will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit delete request via form or AJAX
                    $('#delete-form-' + locationId).submit();
                }
            });
        });
    </script>
    {{-- Update location --}}
    <script>
        $(document).on('click', '.edit-btn', function() {
            const locationId = $(this).data('id');
            const currentName = $(this).data('name');

            Swal.fire({
                title: 'Update category',
                html: `
            <input type="text" id="swal-name" class="swal2-input" placeholder="Name" value="${currentName}">
            <input type="file" id="swal-image" class="swal2-file">
            <small class="text-muted d-block">Leave image blank to keep existing one.</small>
        `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const newName = $('#swal-name').val().trim();
                    if (!newName) {
                        Swal.showValidationMessage('Name is required');
                    }
                    return {
                        name: newName,
                        image: $('#swal-image').prop('files')[0] ?? null
                    };
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Build FormData so the file can travel with the request
                const fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                fd.append('_method', 'PUT');
                fd.append('name', result.value.name);
                if (result.value.image) fd.append('image', result.value.image);

                $.ajax({
                    url: '{{ url('admin/blog-category') }}/' + locationId,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: () => {
                        Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: xhr.responseJSON?.message || 'Server error'
                        });
                    }
                });
            });
        });
    </script>

@endsection
