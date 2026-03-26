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
                <h3 class="card-title">{{$name}} Screenshots</h3>
      
              </div>

              <!-- /.card-header -->
              <div class="card-body">
              <div class="row">
                @if($folders)
                @foreach($folders as $folder)
                <div class="col-md-2 text-center">
    <a href="{{ url('/login_auth/') }}/{{ $name }}/{{ $folder }}" 
       class="d-block p-2 rounded shadow-sm" 
       style="box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.4);">
       
        <img src="{{ url('public/admin/images/folders.jpg') }}" width="100%"  class="rounded m-1" alt="">
        <p class="mt-2 text-dark fw-bold">{{ $folder }}</p>
    </a>
</div>


                @endforeach
                @endif
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
@endsection
