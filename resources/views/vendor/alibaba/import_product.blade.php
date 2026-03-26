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
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Import Products from Alibaba</h3>
              </div>
				<div class="card-body">
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
					<div id="status"></div><br/>
					<label for="keyword">Enter product keyword:</label>
					<input type="text" id="keyword" class="form-control" placeholder="laptop" value="" required/>
					<br>

					<button class="btn btn-primary" onclick="importProducts()">Import Products</button>
					<script>
						const BASE_URL = "{{ url('/vendor/') }}";

						function importProducts() {
							const keyword = document.getElementById('keyword').value.trim();
							const statusDiv = document.getElementById('status');

							if (!keyword) {
								statusDiv.innerHTML = '<div class="alert alert-warning">Please enter a keyword to import products.</div>';
								return;
							}

							statusDiv.innerHTML = `<div class="alert alert-info">🔄 Importing products for keyword "<strong>${keyword}</strong>"... Please wait.</div>`;

							fetch(`${BASE_URL}/alibaba/import`, {
								method: "POST",
								headers: {
									"Content-Type": "application/json",
									"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
								},
								body: JSON.stringify({ keyword: keyword })
							})
							.then(response => response.json())
							.then(data => {
								if (data.success) {
									statusDiv.innerHTML = `<div class="alert alert-success">✅ ${data.message || 'Products successfully imported from Alibaba.'}</div>`;
								} else {
									statusDiv.innerHTML = `<div class="alert alert-danger">❌ ${data.message || 'Product import failed.'}</div>`;
								}
							})
							.catch(error => {
								statusDiv.innerHTML = `<div class="alert alert-danger">❌ Failed to import products. Please try again later.</div>`;
								console.error("Import error:", error);
							});
						}
					</script>

					
					
				</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
