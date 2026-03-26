@extends('admin/layouts.backend')
@section('title', 'Partner Products')
@section('content')

<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Partner Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Partner Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Partner Products</h3>
                </div>
                <div class="card-body">
                    <form method="GET" id="filterForm" class="form-inline mb-3">
                        <select name="campaign_id" class="form-control mr-2">
                            <option value="">All Campaigns</option>
                            @foreach($campaigns as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="form-control mr-2">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
                    </form>

                    <table id="partnerProductsTable" class="table table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vendor</th>
                                <th>Campaign</th>
                                <th>Product Name</th>
                                <th>Image</th>
                                <th>Old Price</th>
                                <th>New Price</th>
                                <th>Discount</th>
                                <th>Min Qty</th>
                                <th>Max Qty</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>

        </div>
    </section>
</div>

<script>
$(document).ready(function() {

    let table = $('#partnerProductsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.partner-products.index") }}',
            data: function (d) {
                d.campaign_id = $('select[name=campaign_id]').val();
                d.status = $('select[name=status]').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'vendor', name: 'vendor' },
            { data: 'campaign', name: 'campaign' },
            { data: 'name', name: 'name' },
            { data: 'image', name: 'image' },
            { data: 'old_price', name: 'old_price' },
            { data: 'new_price', name: 'new_price' },
			{ data: 'discount', name: 'discount' },
            { data: 'min_quantity', name: 'min_quantity' },
            { data: 'max_quantity', name: 'max_quantity' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

});
</script>
@endsection
