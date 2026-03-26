@extends('admin/layouts.backend')
@section('title', 'Hero Section')
@section('content')
<style>
    .wrapper-image-preview {
        margin-left: -6px;
    }

    .back-preview-image,
    .back-preview-image1,
    .back-preview-image2,
    .back-preview-image3 {
        height: 200px;
        width: 100%;
        position: relative;
        overflow: hidden;
        background-color: #f5f5f5 !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-size: contain !important;
    }

    .image-upload {
        visibility: hidden;
    }

    .img-upload-label {
        cursor: pointer;
    }

    .box {
        display: block;
        height: 250px;
        width: 100%;
        background-color: white;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        margin-bottom: 15px;
    }

    .upload-options {
        position: relative;
        height: 50px;
        padding: 14px;
        cursor: pointer;
        overflow: hidden;
        text-align: center;
    }
</style>

<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Hero Section</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Hero Section</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Hero Section Posts</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.homepage.hero_section_update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- Post 1 --}}
                                <h2 class="bg-light mt-1 mb-2">Side Post 1</h2>

                                <div class="form-group">
                                    <label>Title 1</label>
                                    <input type="text" class="form-control" name="title1" value="{{ $hero_section->title1 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>URL 1</label>
                                    <input type="text" class="form-control" name="url1" value="{{ $hero_section->url1 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Description 1</label>
                                    <input type="text" class="form-control" name="description1" value="{{ $hero_section->description1 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Desktop Image 1</label>
                                    <div class="wrapper-image-preview">
                                        <div class="box">
                                            <div class="back-preview-image" id="preview-desktop1" style="background-image: url('{{ isset($hero_section->desktop_image1) ? asset('uploads/homepage/hero/' . $hero_section->desktop_image1) : asset('assets/images/hero.jpg') }}');">
                                            </div>
                                            <div class="upload-options">
                                                <label class="img-upload-label"> <i class="fas fa-camera"></i> Upload Image
                                                    <input type="file" class="image-upload" id="img-desktop1" name="desktop_image1" accept="image/*">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mobile Image 1</label>
                                    <div class="wrapper-image-preview">
                                        <div class="box">
                                            <div class="back-preview-image1" id="preview-mobile1" style="background-image: url('{{ isset($hero_section->mobile_image1) ? asset('uploads/homepage/hero/' . $hero_section->mobile_image1) : asset('assets/images/hero.jpg') }}');">
                                            </div>
                                            <div class="upload-options">
                                                <label class="img-upload-label"> <i class="fas fa-camera"></i> Upload Image
                                                    <input type="file" class="image-upload" id="img-mobile1" name="mobile_image1" accept="image/*">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Post 2 --}}
                                <h2 class="bg-light mt-3 mb-2">Side Post 2</h2>

                                <div class="form-group">
                                    <label>Title 2</label>
                                    <input type="text" class="form-control" name="title2" value="{{ $hero_section->title2 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>URL 2</label>
                                    <input type="text" class="form-control" name="url2" value="{{ $hero_section->url2 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Description 2</label>
                                    <input type="text" class="form-control" name="description2" value="{{ $hero_section->description2 ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Desktop Image 2</label>
                                    <div class="wrapper-image-preview">
                                        <div class="box">
                                            <div class="back-preview-image2" id="preview-desktop2" style="background-image: url('{{ isset($hero_section->desktop_image2) ? asset('uploads/homepage/hero/' . $hero_section->desktop_image2) : asset('assets/images/hero.jpg') }}');">
                                            </div>
                                            <div class="upload-options">
                                                <label class="img-upload-label"> <i class="fas fa-camera"></i> Upload Image
                                                    <input type="file" class="image-upload" id="img-desktop2" name="desktop_image2" accept="image/*">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mobile Image 2</label>
                                    <div class="wrapper-image-preview">
                                        <div class="box">
                                            <div class="back-preview-image3" id="preview-mobile2" style="background-image: url('{{ isset($hero_section->mobile_image2) ? asset('uploads/homepage/hero/' . $hero_section->mobile_image2) : asset('assets/images/hero.jpg') }}');">
                                            </div>
                                            <div class="upload-options">
                                                <label class="img-upload-label"> <i class="fas fa-camera"></i> Upload Image
                                                    <input type="file" class="image-upload" id="img-mobile2" name="mobile_image2" accept="image/*">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Image preview scripts --}}
<script>
    function previewImage(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    previewImage('img-desktop1', 'preview-desktop1');
    previewImage('img-mobile1', 'preview-mobile1');
    previewImage('img-desktop2', 'preview-desktop2');
    previewImage('img-mobile2', 'preview-mobile2');
</script>
@endsection
