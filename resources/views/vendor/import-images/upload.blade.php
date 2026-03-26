@extends('vendor/layouts.backend')
@section('title', 'Import Product Images')
@section('content')
<div class="content-wrapper admin-dashboard-content">

    {{-- 🔹 Page Header --}}
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Import Product Images</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Import Product Images</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔹 Upload Form --}}
    <section class="content">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row">
                <div class="col-md-12">
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4 shadow-sm">
                                        <div class="card-header">
                                            <h3 class="card-title">Upload Product Images Path <b>({{$defaultFilePath}})</b></h3>
                                        </div>

                                        @if ($errors->any())
											<div class="alert alert-danger">
												<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
											</div>
										@endif

										@if (session('error'))
											<div class="alert alert-danger">{{ session('error') }}</div>
										@endif

										@if (session('success'))
											<div class="alert alert-success">{{ session('success') }}</div>
										@endif

                                        {{-- ✅ Upload Form --}}
                                        <div class="card-body">
                                            <form action="{{ route('images.upload') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label class="fw-bold">Upload Images ZIP File</label>
                                                    <input type="file" name="images_zip" class="form-control" accept=".zip">
                                                    <small class="text-muted">Upload a ZIP file containing multiple product images. Images will be extracted automatically. Max upload size: 1 GB</small>
                                                </div>

                                                <div class="form-group mt-3">
                                                    <label class="fw-bold">Or Upload Multiple Images</label>
                                                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                                    <small class="text-muted">Select multiple image files (JPG, PNG, etc.). Max upload size per image: 100 MB</small>
                                                </div>

                                                {{-- Image Preview --}}
                                                <div id="preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>

                                                <button type="submit" id="submit-btn" class="btn btn-primary mt-4">Upload</button>
                                            </form>
                                        </div>
                                    </div> 

                                    {{-- ✅ Uploaded Image Table (Show only when paths exist) --}}
                                    @isset($uploadedPaths)
                                        @if (count($uploadedPaths) > 0)
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header bg-success text-white">
                                                    <h3 class="card-title mb-0">✅ Images Uploaded Successfully!</h3>
                                                </div>
                                                <div class="card-body">

                                                    <p>Use the following paths in your Excel/CSV file (under <code>product_image</code> or <code>variant_image</code>):</p>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>Preview</th>
                                                                    <th>CSV Path</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($uploadedPaths as $path)
                                                                    <tr>
                                                                        <td>
                                                                            <img src="{{ asset($path) }}" width="80" height="80" style="object-fit:cover; border-radius:6px;">
                                                                        </td>
                                                                        <td><code>{{ $path }}</code></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="alert alert-info mt-4">
                                                        <strong>Example:</strong><br>
                                                        In your Excel/CSV file, set the image column as:<br>
                                                        <code>uploads/import_images/image_name.jpg</code>
                                                    </div>

                                                    <a href="{{ route('images.upload.form') }}" class="btn btn-secondary mt-3">⬅ Upload More</a>
                                                </div>
                                            </div>
                                        @endif
                                    @endisset

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ✅ Preview Script --}}
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.querySelector('input[name="images[]"]');
    const previewContainer = document.getElementById("preview-container");

    if (fileInput) {
        fileInput.addEventListener("change", function () {
            previewContainer.innerHTML = "";
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.width = 100;
                        img.height = 100;
                        img.classList.add("rounded", "border", "m-1");
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
});
</script>
@endpush
@endsection
