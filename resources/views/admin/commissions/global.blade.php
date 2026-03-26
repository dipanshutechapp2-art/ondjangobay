@extends('admin/layouts.backend')
@section('title', 'Global Commission Settings')
@section('content')
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Global Commission Settings</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Global Commission Settings</li>
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
						<div class="container-fluid">
							<div class="row">
								<div class="col-md-12">
									<div class="card mb-4">
										{{-- Success Message --}}
										@if(session('success'))
											<div class="alert alert-success">{{ session('success') }}</div>
										@endif

										{{-- Error Validation --}}
										@if($errors->any())
											<div class="alert alert-danger">
												<ul class="mb-0">
													@foreach($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@endif
										<div class="card-header">
										   <h3 class="card-title"> Global Commission Settings</h3>
											<a href="{{ route('users.index') }}" class="btn btn-danger btn float-right">
												<i class="fas fa-arrow-left"></i> Back
											</a>
										</div>
										<div class="card-body">
											<form method="POST" action="{{ route('admin.global.commission.save') }}">
												@csrf

												<div class="mb-3">
													<label for="commission_value" class="form-label">Global Commission Percentage (%)</label>
													<input 
														type="number" 
														step="0.01" 
														min="0" 
														max="100" 
														name="commission_value" 
														id="commission_value"
														class="form-control"
														value="{{ old('commission_value', $commission->commission_value ?? '') }}"
														required
													>
													<small class="text-muted">
														This percentage will be applied as the default Ondjango commission for all vendors and products that don’t have a custom commission.
													</small>
												</div>

												<button type="submit" class="btn btn-success">
													💾 Save Commission
												</button>
											</form>
										</div>
									</div> 
								</div>
							</div>
						</div>
					</div>
				</div>      
			</div>
		</section>
    </div>
@endsection
