@extends('admin/layouts.backend')
@section('title', 'Settings')
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
                        <h1>Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Settings</li>
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
                                <h3 class="card-title"> Settings</h3>
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
                                <form role="form" action="{{ route('admin.theme_setting_action') }}" method="POST"
                                    enctype='multipart/form-data'>
                                    @CSRF
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Site Title<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="site_name"
                                                class="form-control @error('site_name') is-invalid @enderror" id="site_name"
                                                placeholder="site_name" value="{{ $setting->site_name }}">

                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Favicon Icon<span
                                                    style="color:red;">*</span></label>
                                            <input type="file" name="favicon" id="imagefavicon"
                                                class="form-control @error('favicon') is-invalid @enderror"
                                                onchange="previewImagefavicon()">

                                        </div>
                                        <div id="image-preview-favicon">
                                            @if ($setting->favicon)
                                                <img src="{{ asset('uploads/setting') }}/{{ $setting->favicon }}"
                                                    alt="favicon" width="60px">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Header Logo<span
                                                    style="color:red;">*</span></label>
                                            <input type="file" name="header_logo" id="image"
                                                class="form-control @error('header_logo') is-invalid @enderror"
                                                onchange="previewImage()">

                                        </div>
                                        <div id="image-preview">
                                            @if ($setting->header_logo)
                                                <img src="{{ asset('uploads/setting') }}/{{ $setting->header_logo }}"
                                                    alt="header_logo" width="150px">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Footer Logo<span
                                                    style="color:red;">*</span></label>
                                            <input type="file" name="footer_logo" id="footer_logo"
                                                class="form-control @error('footer_logo') is-invalid @enderror"
                                                onchange="footerLogo()">

                                        </div>
                                        <div id="footer_logo_preview">
                                            @if ($setting->footer_logo)
                                                <img src="{{ asset('uploads/setting') }}/{{ $setting->footer_logo }}"
                                                    alt="footer_logo" width="150px">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Primary Phone<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="primary_phone"
                                                class="form-control @error('primary_phone') is-invalid @enderror"
                                                id="primary_phone" placeholder="primary_phone"
                                                value="{{ $setting->primary_phone }}">

                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Alternate Phone<span
                                                    style="color:red;">*</span></label>
                                            <input type="text" name="alt_phone"
                                                class="form-control @error('alt_phone') is-invalid @enderror" id="alt_phone"
                                                placeholder="alt_phone" value="{{ $setting->alt_phone }}">

                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email<span style="color:red;">*</span></label>
                                            <input type="text" name="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                placeholder="email" value="{{ $setting->email }}">

                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Address<span style="color:red;">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="address" name="address">{{ $setting->address }}</textarea>
                                        </div>
										
										 <div class="form-group">
                                            <label for="exampleInputEmail1">Set Default Country<span style="color:red;">*</span></label>
											<select name="country" class="form-control" id="country" required >
											    <option value="">-Select-</option>
											    @if(!empty($getCountry))
													@foreach($getCountry as $country)
												        @if($country->name==$setting->country)
															<option value="{{$country->name}}" selected>{{$country->name}}</option>
														@else
															<option value="{{$country->name}}">{{$country->name}}</option>
														@endif
													@endforeach
											    @endif
											</select>
                                        </div>
										
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Copyright <span
                                                    style="color:red;">*</span></label>
                                            <textarea class="form-control @error('copyright') is-invalid @enderror" id="copyright" name="copyright">{{ $setting->copyright }}</textarea>
                                        </div>
                                        
                                    <div class="form-group">
                                        <label for="metatags">Meta Tags</label>
                                        <input name="meta_tags" class="form-control" placeholder="Enter Meta Tags"
                                            id="meta_tags" value="{{ old('meta_tags', $setting->meta_tags) }}">
                                        @error('meta_tags')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="details">Meta Description </label>
                                        <textarea class="form-control summernote" id="summernote2" name="meta_description" rows="3"
                                            placeholder="Meta Description" style="display: none;" rows="3"> {{ $setting->meta_description }}</textarea>
                                        @error('meta_description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                        {{-- <div class="form-group">
            <label for="parent_id">Header Menu</label>
            <select name="header_menu" id="header_menu" class="form-control">
                <option value="">-- None --</option>
                @foreach ($menus as $menuItem)
                    <option value="{{ $menuItem->id }}" {{ $menu->parent_id == $menuItem->id ? 'selected' : '' }}>
                        {{ $menuItem->title }}
                    </option>
                @endforeach
            </select>
        </div>  --}}
                                
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
