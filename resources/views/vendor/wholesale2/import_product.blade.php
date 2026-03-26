@extends('vendor/layouts.backend')
@section('title', 'Import Product')
@section('content')
  <div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Import Product</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Import Product</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
			<div class="col-12">
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
			</div>
            {{-- <div class="col-6">
				<div class="card">
				  <div class="card-header">
					<h3 class="card-title">Import Products from <strong>Wholesale2</strong></h3>
				  </div>
					<div class="card-body">
						<form method="POST" action="{{ url('/vendor/wholesale2b-products/import') }}">
							@csrf
							<div class="form-group">
								<label for="category_id">Select Category</label>
								<select name="category_id" id="category_id" class="form-control" required>
									<option value="">-- Select Category --</option>
									<?php renderCategorySingleOptions($categories, null, 0, old('category_id', $selectedCategoryId ?? null)); ?>
								</select>
							</div>
							<div class="form-group">
								<label for="store_id">Select Store</label>
								<select name="store_id" id="store_id" class="form-control" required>
									<option value="">-- Select Store --</option>
									@foreach($stores as $store)
										<option value="{{ $store->id }}">{{ $store->store_name }}</option>
									@endforeach
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Start Import</button>
						</form>
										
						
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="card">
				  <div class="card-header">
					<h3 class="card-title">Import Products from <strong>CJ Dropshipping</strong></h3>
				  </div>
					<div class="card-body">
						<form method="POST" action="{{ route('vendor.cj.import') }}">
							@csrf
							<div class="form-group">
								<label for="category_id">Select Category</label>
								<select name="category_id" id="category_id" class="form-control" required>
									<option value="">-- Select Category --</option>
									<?php renderCategorySingleOptions($categories, null, 0, old('category_id', $selectedCategoryId ?? null)); ?>
								</select>
							</div>
							<div class="form-group">
								<label for="store_id">Select Store</label>
								<select name="store_id" id="store_id" class="form-control" required>
									<option value="">-- Select Store --</option>
									@foreach($stores as $store)
										<option value="{{ $store->id }}">{{ $store->store_name }}</option>
									@endforeach
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Start Import</button>
						</form>
					</div>
				</div>
			</div> --}}
			<?php 
				$clientID    = getenv('AUTODS_CLIENT_ID');
				$authURL     = getenv('AUTODS_AUTH_URL');
				$redirectURL = getenv('AUTODS_REDIRECT_URI');
				$state = base64_encode(json_encode([
					'flow' => 'web',
				]));
			?>
			@if(!empty($clientID) && !empty($authURL) && !empty($redirectURL))
				<div class="col-12">
					<div class="card">
					  <div class="card-header">
						<h3 class="card-title">Import Products from <strong>Auto-DS</strong></h3><br/>
						
						<p class="text-muted mb-2">
							<small>
								<b>Note:</b> Please connect your Auto-DS account before importing products.
							</small>
						</p>
						
						&nbsp; &nbsp; <a href="<?php echo $authURL;?>/login?client_id=<?php echo $clientID;?>&response_type=code&scope=email+openid+phone&redirect_uri=<?php echo $redirectURL;?>&state={{ $state }}">
							<button class="btn btn-success">Connect</button>
						</a>

					  </div>
						<div class="card-body">
							<form method="POST" action="{{ route('vendor.autods.import') }}">
								@csrf
								<div class="form-group">
									<label for="store_id">Select Auto-DS Store</label>
									<select name="autods_store_id" id="autods_store_id" class="form-control" required>
										<option value="">-- Select Auto-DS Store --</option>
										@foreach($autoDsStores as $autodsstore)
										    @if($autoDsTokenInfo->autods_store_id==$autodsstore->autods_store_id)
												<option value="{{ $autodsstore->autods_store_id }}" selected>{{ $autodsstore->name }}</option>
											@else
											    <option value="{{ $autodsstore->autods_store_id }}">{{ $autodsstore->name }}</option>
										   @endif
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="category_id">Select Category</label>
									<select name="category_id" id="category_id" class="form-control" required>
										<option value="">-- Select Category --</option>
										<?php renderCategorySingleOptions($categories, null, 0, old('category_id', $selectedCategoryId ?? null)); ?>
									</select>
								</div>
								<div class="form-group">
									<label for="store_id">Select Store</label>
									<select name="store_id" id="store_id" class="form-control" required>
										<option value="">-- Select Store --</option>
										@foreach($stores as $store)
											<option value="{{ $store->id }}">{{ $store->store_name }}</option>
										@endforeach
									</select>
								</div>
								<button type="submit" class="btn btn-primary">Start Import</button>
							</form>
						</div>
					</div>
				</div>
			@endif
        </div>
      </div>
    </section>
  </div>
@endsection
