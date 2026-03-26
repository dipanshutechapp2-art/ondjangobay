@extends('admin/layouts.backend')
@section('title', 'Add Coupon')
@section('content')
<div class="content-wrapper admin-dashboard-content">
    <section class="content-header">
        <div class="container-fluid">
            <x-sweet-alert />

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Coupon</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Add Coupon</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <x-sweet-alert />
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Add Coupon</h3>
                            <a href="{{ route('admin.coupon.show') }}" class="btn btn-danger float-right">
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
                            <form class="geniusform" action="{{ route('admin.coupon.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="inp-vendor_id">Vendor <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inp-vendor_id" name="vendor_id" required>
                                        <option value="">-Select-</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

							   <div class="form-group">
									<label for="inp-code">Coupon Code <span class="text-danger">*</span></label>
									<div class="input-group">
										<input type="text" class="form-control" id="inp-code" name="code" readonly required>
										<div class="input-group-append">
											<button type="button" class="btn btn-secondary" onclick="generateCouponCode()">Generate</button>
										</div>
									</div>
								</div>

                                <div class="form-group">
                                    <label for="inp-type">Discount Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inp-type" name="type" required>
                                        <option value="">-Select-</option>
                                        <option value="fixed">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="inp-value">Discount Value <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="inp-value" name="value"
                                           placeholder="e.g. 10 for $10 or 10%" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label for="inp-min_order_amount">Minimum Order Amount</label>
                                    <input type="number" class="form-control" id="inp-min_order_amount" name="min_order_amount"
                                           placeholder="Optional" step="0.01">
                                </div>

                                <div class="form-group">
                                    <label for="inp-max_uses">Max Uses (overall)</label>
                                    <input type="number" class="form-control" id="inp-max_uses" name="max_uses"
                                           placeholder="Optional">
                                </div>

                                <div class="form-group">
                                    <label for="inp-max_uses_per_user">Max Uses Per User</label>
                                    <input type="number" class="form-control" id="inp-max_uses_per_user" name="max_uses_per_user"
                                           placeholder="Optional">
                                </div>

                                <div class="form-group">
                                    <label for="inp-starts_at">Start Date</label>
                                    <input type="datetime-local" class="form-control" id="inp-starts_at" name="starts_at">
                                </div>

                                <div class="form-group">
                                    <label for="inp-expires_at">Expiry Date</label>
                                    <input type="datetime-local" class="form-control" id="inp-expires_at" name="expires_at">
                                </div>
								
                                <div class="form-group">
                                    <label for="inp-is_active">Status</label>
                                    <select class="form-control" id="inp-is_active" name="is_active">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div> <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function generateCouponCode(length = 10) {
        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < length; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('inp-code').value = code;
    }
</script>
@endsection
