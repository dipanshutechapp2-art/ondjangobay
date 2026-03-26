@extends('admin/layouts.backend')
@section('title', 'Add Blog')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <style>
        .hidden {
            display: none;
        }

        .wrapper-image-preview {
            margin-left: -6px;
        }

        .back-preview-image {
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

        .image-upload1 {
            visibility: hidden;
        }

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

        .box-settings {
            display: block;
            height: 270px;
            width: 220px;
            margin: 0px 10px 0px 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
        }

        .upload-options {
            position: relative;
            height: 50px;
            padding: 14px;
            cursor: pointer;
            overflow: hidden;
            text-align: center;
        }

        .upload-options-settings {
            position: relative;
            height: 60px;
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
                        <h1>Add Blog</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Blog</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center mt-3">
                    <div class="col-md-10">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Add New Blog</h6>
                            </div>

                            <div class="card-body">
                                <form class="geniusform" action="{{ route('blog.store') }}" method="POST"
                                    enctype="multipart/form-data">

                                    @csrf

                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Enter Title" value="">
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="inp-name">Category</label>
                                        <select class="form-control mb-3" name="category_id">
                                            <option value="" selected="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Image </label>
                                        <div class="wrapper-image-preview">
                                            <div class="box">
                                                <div class="back-preview-image">
                                                </div>
                                                <div class="upload-options">
                                                    <label class="img-upload-label" for="img-upload"> <i
                                                            class="fas fa-camera"></i>
                                                        Upload Picture </label>
                                                    <input id="img-upload" type="file" class="image-upload"
                                                        name="photo" accept="image/*">
                                                    @error('photo')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="details">Description</label>
                                        <textarea class="form-control summernote" id="summernote" name="description" rows="3" placeholder="Description"
                                            style="display: none;" rows="3">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="tags">Tags</label>
                                        <input name="tags" class="form-control" placeholder="Enter tags" id="tags">
                                        @error('tags')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="source">Source</label>
                                        <input type="text" class="form-control" id="source" name="source"
                                            placeholder="Source" value="">
                                        @error('source')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="metatags">Meta Tags</label>
                                        <input name="meta_tags" class="form-control" placeholder="Enter Meta Tags"
                                            id="meta_tags">
                                        @error('meta_tags')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="details">Meta Description </label>
                                        <textarea class="form-control summernote" id="summernote2" name="meta_description" rows="3"
                                            placeholder="Meta Description" style="display: none;" rows="3">{{ old('meta_description') }}</textarea>
                                        @error('meta_description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" id="submit-btn" class="btn btn-primary w-100">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.getElementById('img-upload').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('.back-preview-image').style.backgroundImage =
                            `url(${e.target.result})`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>


        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

        <script>
            var input = document.querySelector('input[name=tags]');
            new Tagify(input);
        </script>

        <script>
            var input = document.querySelector('input[name=meta_tags]');
            new Tagify(input);
        </script>

    @endsection
