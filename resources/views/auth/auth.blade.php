@extends('admin/layouts.backend')
@section('title', 'Users Screenshots')
@section('content')
<!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper admin-dashboard-content">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Screenshots</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Screenshots</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
              <div class="text-center mb-4">
    <h3 class="card-title bg-primary text-white py-2 px-4 rounded shadow-sm d-inline-block">
        <i class="fas fa-folder-open"></i> {{ $name }} → {{ $folder }} → Screenshots
    </h3>
</div>

      
              </div>

              <!-- /.card-header -->
              <div class="card-body">
              <div class="container">
    <div class="row justify-content-center">    
    @if($paginatedScreenshots->count())
    @foreach($paginatedScreenshots as $screenshot)
        <div class="col-md-3 col-sm-4 col-6 mb-4 d-flex justify-content-center">
            <div class="card shadow-sm border-0 position-relative" style="width: 100%; max-width: 180px;">
                <a href="{{ $screenshot }}" target="_blank">
                    <img src="{{ $screenshot }}" class="card-img-top rounded" style="height: 120px; object-fit: cover;">
                </a>
                <div class="card-body p-2 text-center">
                    <small class="text-muted">Screenshot</small>
                </div>

                <!-- Delete Button -->
                <button class="btn btn-danger btn-sm delete-screenshot position-absolute" 
                        style="top: 5px; right: 5px; border-radius: 50%; width: 24px; height: 24px; padding: 0;" 
                        data-image="{{ $screenshot }}">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach
@else
    <p class="text-center text-muted">No screenshots available.</p>
@endif

    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $paginatedScreenshots->links('pagination::bootstrap-4') }}
    </div>
</div>


              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <script>
  function validateDelete(){
	var confirms = confirm('Do you want to delete ?.');
	if(confirms==false){
		return false;
	}
  }
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.delete-screenshot', function() {
    var imageUrl = $(this).data('image');
    console.log("Deleting file: ", imageUrl); // Debugging

    if (confirm('Are you sure you want to delete this screenshot?')) {
        $.ajax({
            url: "{{ route('delete.login_auth_trash') }}",
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}",
                image: imageUrl
            },
            success: function(response) {
                if (response.success) {
                    console.log("File deleted successfully");
                    location.reload(); // Refresh after delete
                } else {
                    console.error("Error:", response.message, "Path:", response.path);
                }
            },
            error: function(xhr) {
                alert('Error deleting the screenshot.');
            }
        });
    }
});

</script>


@endsection
