@extends('vendor/layouts.backend')
@section('title', 'Edit Category')
@section('content')
 <style>
    .hidden {
        display: none;
    }

    .wrapper-image-preview {
        margin-left: -6px;
    }

    .back-preview-image,
    .back-preview-mobile-image {
        height: 200px;
        width: 100%;
        position: relative;
        overflow: hidden;
        background-color: #f5f5f5 !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-size: contain !important;
    }

    .image-upload,
    .image-upload1,
    .image-upload2 {
        visibility: hidden;
    }

    .img-upload-label {
        cursor: pointer;
    }

    .box {
        display: block;
        height: 250px;
        width: 250px;
        margin: 0px 10px 0px 10px;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
    }
</style>
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Category</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vednor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Category</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>


        <section class="content">
            <div class="container-fluid">
                  <x-sweet-alert />
                <div class="row">

                    <!-- Add Menu Form -->
                    <div class="col-md-12">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                         <div class="card mb-4">
            <div class="card-header">
        <h3 class="card-title"> User Form</h3>
        <a href="{{ route('category.index') }}" class="btn btn-danger btn float-right">
                <i class="fas fa-arrow-left"></i> Back
            </a>
      </div>

      <div class="card-body">
     
        <form class="geniusform" action="{{ route('category.update',$category->id) }}" method="POST" enctype="multipart/form-data">
         @csrf   
         @method('PUT')
            <input type="hidden" name="id" value="{{ $category->id }}">

                          <div class="form-group">
                            <label for="title">Parent</label>
                            <select name="parent_id" id="" class="form-control">
                                    <option value="">Select Category</option>
                                    @if($categories->count() > 0)
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    @endif
                            </select>
                            @error('parent_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

            <div class="form-group">
                <label for="inp-name">Name</label>
                <input type="text" class="form-control" id="inp-name" name="name" placeholder="Enter Name" value="{{ $category->name }}" >
            </div>

			<div class="form-group">
				<label>Upload Desktop Icon</label>
				<div class="wrapper-image-preview">
					<div class="box">
						<div class="back-preview-image" style="background-image: url('{{ asset('uploads/categories/'.$category->desktop_image) }}');"></div>
						<div class="upload-options">
							<label class="img-upload-label" for="img-upload">
								<i class="fas fa-camera"></i> Upload Picture
							</label>
							<input id="img-upload" type="file" class="image-upload" name="desktop_image" accept="image/*">
							@error('desktop_image')
								<span class="text-danger">{{ $message }}</span>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label>Upload Mobile Icon</label>
				<div class="wrapper-image-preview">
					<div class="box">
						<div class="back-preview-mobile-image" style="background-image: url('{{ asset('uploads/categories/'.$category->mobile_image) }}');"></div>
						<div class="upload-options">
							<label class="img-upload-label" for="img-mobile-upload">
								<i class="fas fa-camera"></i> Upload Picture
							</label>
							<input id="img-mobile-upload" type="file" class="image-upload" name="mobile_image" accept="image/*">
							@error('mobile_image')
								<span class="text-danger">{{ $message }}</span>
							@enderror
						</div>
					</div>
				</div>
			</div>
            
            <button type="submit" id="submit-btn" class="btn btn-primary  ">Update</button>

        </form>
      </div>
    </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal fade" id="setgallery" tabindex="-1" role="dialog" aria-labelledby="setgallery" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Image Gallery</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="top-area">
                <div class="row">
                    <div class="col-sm-6 text-right">
                        <div class="upload-img-btn">
                            <label id="property_gallery"><i class="icofont-upload-alt"></i>Upload File</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <a href="javascript:;" class="upload-done" data-dismiss="modal"> <i class="fas fa-check"></i> Done</a>
                    </div>
                    <div class="col-sm-12 text-center">( <small>You can upload multiple Images.</small> )</div>
                </div>
            </div>
            <div class="gallery-images">
                <div class="selected-image">
                    <div class="row">


                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
        </section>
    </div>


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
{{-- Image Preview JS --}}
<script>
    document.getElementById('img-upload').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.querySelector('.back-preview-image');
                if (preview) preview.style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('img-mobile-upload').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.querySelector('.back-preview-mobile-image');
                if (preview) preview.style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
<script>
    document.getElementById('img-upload').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.querySelector('.back-preview-image').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
    <script>
          $(document).on("click", "#property_gallery", function () {
    $("#propertyuploadgallery").click();
  });
    $("#propertyuploadgallery").change(function (event) {
    var total_file = document.getElementById("propertyuploadgallery").files
      .length;
    for (var i = 0; i < total_file; i++) {
      $(".selected-image .row").append(
        '<div class="col-sm-6">' +
          '<div class="img gallery-img">' +
          '<span class="remove-img"><i class="fas fa-times"></i>' +
          '<input type="hidden" value="' +
          i +
          '">' +
          "</span>" +
          '<a href="' +
          URL.createObjectURL(event.target.files[i]) +
          '" target="_blank">' +
          '<img class="back-preview-imag" src="' +
          URL.createObjectURL(event.target.files[i]) +
          '" alt="gallery image">' +
          "</a>" +
          "</div>" +
          "</div> "
      );
            $("#geniusform").append(
        '<input type="hidden" name="gallery[]" id="galval' +
          i +
          '" class="removegal" value="' +
          i +
          '">'
      );
    }
  });
        $(document).ready(function() {
            // Initialize Bootstrap Switch if needed
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

            // Handle change event
            $('.location-status-switch').on('switchChange.bootstrapSwitch', function(event, state) {
                let locationId = $(this).data('id');
                let status = state ? 1 : 0;

                $.ajax({
                    url: '{{ route('category.updateStatus') }}',
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
        $(document).on('click', '.delete-location', function() {
            var locationId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this location. Every informtation under this location will be deleted.",
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
                title: 'Update Location',
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
                    url: '{{ url('admin/categories') }}/' + locationId,
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
