@extends('admin/layouts.backend')
@section('title', 'View Partner Campaign')
@section('content')

<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert/>
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Campaign Details</h1></div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('partner-campaigns.index') }}" class="btn btn-danger">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header"><h3 class="card-title">Campaign Information</h3></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Name</th>
                            <td>{{ $campaign->name }}</td>
                        </tr>
                        {{-- <tr>
                            <th>Frequency</th>
                            <td>{{ ucfirst($campaign->frequency) }}</td>
                        </tr> --}}
                        <tr>
                            <th>Start Date</th>
                            <td>{{ \Illuminate\Support\Carbon::parse($campaign->start_date)->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>{{ \Illuminate\Support\Carbon::parse($campaign->end_date)->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Upload Deadline</th>
                            <td>{{ $campaign->upload_deadline ? \Illuminate\Support\Carbon::parse($campaign->upload_deadline)->format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Minimum Value</th>
                            <td>{{ $campaign->min_value ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Minimum Quantity</th>
                            <td>{{ $campaign->min_quantity }}</td>
                        </tr>
                        {{-- <tr>
                            <th>Goal Quantity</th>
                            <td>{{ $campaign->goal_quantity ?? '-' }}</td>
                        </tr> --}}
                        <tr>
                            <th>Category</th>
                            <td>{{ $campaign->category_id ? $campaign->category->name : '-' }}</td>
                        </tr>
                       {{-- <tr>
                            <th>Cart Timer (minutes)</th>
                            <td>{{ $campaign->cart_timer_minutes ?? '-' }}</td>
					   </tr> --}}
                        <tr>
                            <th>Cart Max Volume</th>
                            <td>{{ $campaign->cart_max_volume ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $color = $campaign->status === 'active' ? 'success' : ($campaign->status === 'closed' ? 'danger' : 'secondary');
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($campaign->status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $campaign->created_at ? $campaign->created_at->format('Y-m-d H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $campaign->updated_at ? $campaign->updated_at->format('Y-m-d H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($campaign->campaignProducts->count() > 0)
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Products in this Campaign</h3></div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Vendor</th>
                                    <th>Old Price</th>
                                    <th>New Price</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->vendor->name ?? '-' }}</td>
                                    <td>{{ $product->old_price }}</td>
                                    <td>{{ $product->new_price }}</td>
                                    <td>
                                        @if($product->old_price > 0)
                                            {{ round((($product->old_price - $product->new_price)/$product->old_price)*100) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

@endsection
