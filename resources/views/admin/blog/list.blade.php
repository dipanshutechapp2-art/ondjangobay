@extends('admin/layouts.backend')
@section('title', 'Blogs')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Blogs</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Blogs</li>
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
                <h3 class="card-title">Blogs</h3>
            <a href="{{ route('blog.create') }}" class="btn btn-success btn float-right">
                <i class="fas fa-plus"></i> Create
            </a>
            </div>
            <div class="card-body">

                <table id="example2" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blogs as $blog)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset('uploads/blog/' . $blog->photo) }}" alt="blog Image"
                                        class="img-fluid" style="width: 100px; height: 100px;">
                                </td>
                                <td>{{ $blog->category->name }}</td>
                                <td>{{ $blog->title }}</td>
                                <td>{{ $blog->slug }}</td>
                                <td>
                                    <input type="checkbox" name="status" class="blog-status-switch"
                                        data-bootstrap-switch data-off-color="danger" data-on-color="success"
                                        {{ $blog->status == 1 ? 'checked' : '' }} data-id="{{ $blog->id }}" </td>
                                <td>
                                    
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success">Action</button>
                                        <button type="button" class="btn btn-success dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item" href="{{ route('blog.edit', $blog->id) }}" data-id="{{ $blog->id }}"
                                                data-name="{{ $blog->name }}">
                                                Edit</a>
                                            <a class="dropdown-item delete-blog" href="#"
                                                data-id="{{ $blog->id }}">Delete</a>
                                            <!-- Hidden form to trigger DELETE -->
                                            <form id="delete-form-{{ $blog->id }}"
                                                action="{{ route('blog.destroy', $blog->id) }}" method="POST"
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
            $('.blog-status-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                let propertiesId = $(this).data('id');
                let status = state ? 1 : 0;

                $.ajax({
                    url: '{{ route('blog.updateStatus') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: propertiesId,
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
        $(document).on('click', '.delete-blog', function() {
            var propertiesId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this blog. Every informtation under this blog will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit delete request via form or AJAX
                    $('#delete-form-' + propertiesId).submit();
                }
            });
        });
    </script>

@endsection
