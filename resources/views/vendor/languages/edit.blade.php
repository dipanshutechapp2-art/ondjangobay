@extends('vendor/layouts.backend')
@section('title', 'Edit Language')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Language</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Edit Language</li>
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
											<h3 class="card-title"> Edit Language</h3>
										  </div>
											@if ($errors->any())
												<div class="alert alert-danger">
													<ul>
														@foreach ($errors->all() as $error)
															<li>{{ $error }}</li>
														@endforeach
													</ul>
												</div>
											@endif
											<div class="card-body">
												 @include('vendor.languages.form', ['language' => $language])
											</div>
										</div> 
                                    </div>
                                </div>
                            </div>
						</section>
                    </div>
				</div>
			</div>
        </section>
	</div>
@endsection
