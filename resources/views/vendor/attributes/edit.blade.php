@extends('vendor/layouts.backend')
@section('title', 'Update Attributes')
@section('content')
<style>
  .attr-row {
    margin-bottom: 15px;
  }
</style>
    <div class="content-wrapper admin-dashboard-content">
        <section class="content-header">
            <div class="container-fluid">
                <x-sweet-alert />

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Update Attributes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Update Attributes</b></li>
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
											<h3 class="card-title"> Update Attributes</b></h3>
											<a href="{{ route('attributes.index') }}" class="btn btn-danger btn float-right">
													<i class="fas fa-arrow-left"></i> Back
												</a>
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
											@if(session()->has('success'))
												<div class="alert alert-success">
												  <strong>Success!</strong> {{ session()->get('success') }}
												</div>
											@endif
											@if(session()->has('error'))
												<div class="alert alert-danger">
													<strong>Warning!</strong> {{ session()->get('error') }}
												</div>
											@endif
										  <div class="card-body">
											<form class="geniusform" action="{{ route('attributes.update',$attributes->id) }}" method="POST" enctype="multipart/form-data">
											 @csrf   
												@method('PATCH')
												<div class="form-group">
													<label for="inp-name">Attribute's name <span style="color:red;">*</span></label>
													<input type="text" class="form-control" id="inp-name" name="name" placeholder="Attribute's name" value="{{ $attributes->name }}" required>
													@error('name')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
												<div class="form-group">
													<label>Attribute Values <span style="color:red;">*</span></label>
													<div id="value-wrapper">
														@foreach ($attributes->values as $val)
														<div class="attr-row input-group mb-2">
															<input type="hidden" name="value_ids[]" value="{{ $val->id }}">
															<input type="text" name="values[]" class="form-control" value="{{ $val->value }}" required>
															<div class="input-group-append">
																<button type="button" class="btn btn-danger remove-value">X</button>
															</div>
														</div>
														@endforeach
													</div>
													<button type="button" class="btn btn-info" id="add-value">+ Add More</button>
													@error('values.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
												</div>
												<button type="submit" id="submit-btn" class="btn btn-primary  ">Submit</button>
											</form>
										  </div>
										</div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
				</section>
			</div>
		</div>
      </section>
	  <script>
		document.getElementById('add-value').addEventListener('click', function () {
			const wrapper = document.getElementById('value-wrapper');
			const row = document.createElement('div');
			row.classList.add('input-group', 'mb-2', 'attr-row');
			row.innerHTML = `
				<input type="hidden" name="value_ids[]" value="0">
				<input type="text" name="values[]" class="form-control" placeholder="e.g., Blue" required>
				<div class="input-group-append">
					<button type="button" class="btn btn-danger remove-value">X</button>
				</div>
			`;
			wrapper.appendChild(row);
		});

		document.addEventListener('click', function (e) {
			if (e.target.classList.contains('remove-value')) {
				e.target.closest('.attr-row').remove();
			}
		});
		</script>

@endsection
