@extends('admin/layouts.backend')
@section('title', 'Testimonials')
@section('content')

    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Testimonials</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Testimonials</li>
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
                                                <h3 class="card-title">Add Testimonials</h3>
                                            </div>
                                            <form action="{{ route('testimonial.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="card-body">

                                                    <div class="form-group">
                                                        <label for="title">Name</label>
                                                        <input type="text" name="name" class="form-control"
                                                            id="location" placeholder="Enter Testimonials Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="title">Designation</label>
                                                        <input type="text" name="designation" class="form-control"
                                                            id="location" placeholder="Enter Designation ">
                                                        @error('designation')
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

                                                    <div class="form-group">
                                                        <label for="title">Description</label>
                                                        <input type="textarea" name="description" class="form-control"
                                                            id="location" placeholder="Enter Description ">
                                                        @error('description')
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
                <h3 class="card-title">Testimonials</h3>
               
            </div>
            <div class="card-body">

                <table id="example2" class="table table-hover dt-responsive dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($testimonials as $testimonial)
                            <tr>

                                <td>{{ $testimonial->name }} </td>
                                <td>{{ $testimonial->designation }}</td>
                                <td>
                                    @if ($testimonial->image)
                                        <img src="{{ asset('uploads/testimonial/' . $testimonial->image) }}"
                                            alt="{{ $testimonial->name }}" width="50" height="50">
                                    @else
                                        <img src="{{ asset('images/default.png') }}" alt="Default Image" width="50"
                                            height="50">
                                    @endif
                                </td>
                                {{-- <td>{{ $testimonial->status == 1 ? 'Active' : 'Deactive' }}</td> --}}
                                  <td>
                                    <input type="checkbox" name="status" class="testimonial-status-switch"
                                    data-bootstrap-switch data-off-color="danger" data-on-color="success"
                                    {{ $testimonial->status == 1 ? 'checked' : '' }} data-id="{{ $testimonial->id }}" </td>
                                  <td>

                                   <div class="btn-group">
                                        <button type="button" class="btn btn-success">Action</button>
                                        <button type="button" class="btn btn-success dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item edit-btn" href="#" data-id="{{ $testimonial->id }}"
                                                data-name="{{ $testimonial->name }}"
                                                data-description="{{ $testimonial->description }}"
                                                data-designation="{{ $testimonial->designation }}" data-toggle="modal"
                                                >
                                                Edit</a>

                                            <a class="dropdown-item delete-testimonial" href="#"
                                                data-id="{{ $testimonial->id }}">Delete</a>
                                            <!-- Hidden form to trigger DELETE -->
                                            <form id="delete-form-{{ $testimonial->id }}"
                                                action="{{ route('testimonial.destroy', $testimonial->id) }}" method="POST"
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
            $('.testimonial-status-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                let locationId = $(this).data('id');
                let status = state ? 1 : 0;

                $.ajax({
                    url: '{{ route('testimonial.updateStatus') }}',
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
        $(document).on('click', '.delete-testimonial', function() {
            var locationId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this testimonial. Every informtation under this testimonial will be deleted.",
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
            const currentDescription = $(this).data('description');
            const currentDesignation = $(this).data('designation');

            Swal.fire({
                title: 'Update testimonial',
                html: `
            <input type="text" id="swal-name" class="swal2-input" placeholder="Name" value="${currentName}">
            <input type="text" id="swal-designation" class="swal2-input" placeholder="Designation" value="${currentDesignation}">
            <textarea id="swal-desc" class="swal2-textarea" placeholder="" >${currentDescription}</textarea>

            <input type="file" id="swal-image" class="swal2-file">
            <small class="text-muted d-block">Leave image blank to keep existing one.</small>
        `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const newName = $('#swal-name').val().trim();
                    const newDesignation = $('#swal-designation').val().trim();
                    const newDescription = $('#swal-desc').val().trim();
                    if (!newName) {
                        Swal.showValidationMessage('Name is required');
                    }
                    return {
                        name: newName,
                        designation: newDesignation,
                        description: newDescription,
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
                fd.append('designation', result.value.designation);
                fd.append('description', result.value.description);
                if (result.value.image) fd.append('image', result.value.image);

                $.ajax({
                    url: '{{ url('admin/testimonial') }}/' + locationId,
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
