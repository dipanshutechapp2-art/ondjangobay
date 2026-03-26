@extends('vendor/layouts.backend')
@section('title', 'Partner Products')
@section('content')
<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
           
			<x-sweet-alert timeout="20000" />

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Partner Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/vendor/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Partner Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Partner Product List -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">All Partner Products</h3>
                            <div>
                                <button class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                                    <i class="fas fa-upload"></i> Upload Products
                                </button>

                                <a href="{{ route('vendor.partner-products.import_errors') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-exclamation-triangle"></i> View Import Errors
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <table id="partnerProductsTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Campaign</th>
                                        <th>Product Name</th>
                                        <th>Image</th>
                                        <th>Old Price</th>
                                        <th>New Price</th>
                                        <th>Discount</th>
                                        <th>Min Qty</th>
                                        <th>Max Qty</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Upload Modal - unchanged from yours -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('vendor.partner-products.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Partner Products</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="text-muted">Use the provided template to upload products for your selected campaign.</p>

                    <div class="form-group">
                        <label for="campaign_id">Select Campaign</label>
                        <select name="campaign_id" id="campaign_id" class="form-control" required>
                            <option value="">-- Choose Campaign --</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                        @error('campaign_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mt-2">
                        <label for="file">Select File (.xlsx or .csv)</label>
                        <input type="file" name="file" id="file" class="form-control" required>
                        @error('file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mt-3 text-right">
                        <a href="#" id="downloadTemplateBtn" class="btn btn-outline-primary" >
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
/* document.addEventListener('DOMContentLoaded', function() {
    const campaignSelect = document.getElementById('campaign_id');
    const downloadBtn = document.getElementById('downloadTemplateBtn');

    campaignSelect.addEventListener('change', function() {
        const campaignId = this.value;
        if (campaignId) {
            downloadBtn.href = "{{ url('vendor/partner-products/upload-template') }}?campaign_id=" + campaignId;
            downloadBtn.classList.remove('disabled');
        } else {
            downloadBtn.href = "#";
            downloadBtn.classList.add('disabled');
        }
    });
}); */
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const campaignSelect = document.getElementById('campaign_id');
    const downloadBtn = document.getElementById('downloadTemplateBtn');

    function updateDownloadLink() {
        const campaignId = campaignSelect.value;

        let baseUrl = "{{ url('vendor/partner-products/upload-template') }}";
        downloadBtn.href = baseUrl;  
    }

    // Update URL on dropdown change
    campaignSelect.addEventListener('change', updateDownloadLink);

    // Run function on page load as well
    updateDownloadLink();
});
</script>


<!-- DataTables Script -->
<script>
$(document).ready(function () {
    $('#partnerProductsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("vendor.partner-products.index") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'campaign_name', name: 'campaign_name' },
            { data: 'name', name: 'name' },
            { data: 'image', name: 'image' , orderable:false, searchable:false},
            { data: 'old_price', name: 'old_price' },
            { data: 'new_price', name: 'new_price' },
            { data: 'discount', name: 'discount' },
            { data: 'min_quantity', name: 'min_quantity' },
            { data: 'max_quantity', name: 'max_quantity' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' }
        ],
        order: [[0, 'desc']],
        responsive: true
    });
});
</script>
@endsection
