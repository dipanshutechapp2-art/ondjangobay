@extends('admin/layouts.backend')
@section('title', 'Edit Partner Campaign')
@section('content')
<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert/>
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Edit Partner Campaign</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('partner-campaigns.index') }}">Partner Campaigns</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
			@if (session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> {{ session('success') }}
					</div>
				@endif

				@if (session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error!</strong> {{ session('error') }}
					</div>
				@endif

				@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Please fix the following errors:</strong>
						<ul class="mt-2 mb-0">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Campaign Details</h3>
                    <a href="{{ route('partner-campaigns.index') }}" class="btn btn-danger float-right"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('partner-campaigns.update', $partnerCampaign->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.partner_campaigns.form', ['partnerCampaign' => $partnerCampaign])
                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
