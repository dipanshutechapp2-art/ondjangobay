@extends('admin/layouts.backend')
@section('title', 'Mail settings')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

    <style>
        #image-preview {
            max-width: 300px;
        }

        #image-preview img {
            max-width: 100%;
        }

        div#home_banner_preview img {
            max-width: 100%;
        }
    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper admin-dashboard-content">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Mail settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Mail settings</li>
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
                                <h3 class="card-title"> Mail settings</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success">
                                        <strong>Success!</strong> {{ session()->get('success') }}
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger">
                                        <strong>Warning!</strong> {{ session()->get('error') }}
                                    </div>
                                @endif
                                <form role="form" action="{{ route('admin.mail_setting_action') }}" method="POST"
                                    enctype='multipart/form-data'>
                                    @CSRF
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">SMTP Host<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="smtp_host"
                                                class="form-control @error('smtp_host') is-invalid @enderror" id="smtp_host"
                                                placeholder="smtp host" value="{{ $setting->smtp_host }}">
                                        </div>
                         
                                         <div class="form-group">
                                            <label for="exampleInputEmail1">SMTP Port<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="smtp_port"
                                                class="form-control @error('smtp_port') is-invalid @enderror" id="smtp_port"
                                                placeholder="smtp port" value="{{ $setting->smtp_port }}">
                                        </div>
                                              <div class="form-group">
                                            <label for="exampleInputEmail1">Encryption<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="encryption"
                                                class="form-control @error('encryption') is-invalid @enderror" id="encryption"
                                                placeholder="smtp Encryption" value="{{ $setting->encryption }}">
                                        </div>
                             
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">SMTP Username<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="smtp_username"
                                                class="form-control @error('smtp_username') is-invalid @enderror" id="smtp_username"
                                                placeholder="smtp username" value="{{ $setting->smtp_username }}">
                                        </div>
                                       <div class="form-group">
                                            <label for="exampleInputEmail1">SMTP Password<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="smtp_password"
                                                class="form-control @error('smtp_password') is-invalid @enderror" id="smtp_password"
                                                placeholder="smtp password" value="{{ $setting->smtp_password }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1"> From Email<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="from_email"
                                                class="form-control @error('from_email') is-invalid @enderror" id="from_email"
                                                placeholder=" from email" value="{{ $setting->from_email }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">from name<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="from_name"
                                                class="form-control @error('from_name') is-invalid @enderror" id="from_name"
                                                placeholder=" from name" value="{{ $setting->from_name }}">
                                        </div>
                                
                                        <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
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
        function previewImagefavicon() {
            // Get the selected file input
            var input = document.getElementById('imagefavicon');

            // Check if a file is selected
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set up the image preview once the file is loaded
                reader.onload = function(e) {
                    var preview = document.getElementById('image-preview-favicon');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Image Preview">';
                };

                // Read the file as a data URL
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        function previewImage() {
            // Get the selected file input
            var input = document.getElementById('image');

            // Check if a file is selected
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set up the image preview once the file is loaded
                reader.onload = function(e) {
                    var preview = document.getElementById('image-preview');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Image Preview">';
                };

                // Read the file as a data URL
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        function footerLogo() {
            // Get the selected file input
            var input = document.getElementById('footer_logo');

            // Check if a file is selected
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set up the image preview once the file is loaded
                reader.onload = function(e) {
                    var preview = document.getElementById('footer_logo_preview');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="footer_logo">';
                };

                // Read the file as a data URL
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>



        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script>
            var input = document.querySelector('input[name=meta_tags]');
            new Tagify(input);
        </script>

@endsection
