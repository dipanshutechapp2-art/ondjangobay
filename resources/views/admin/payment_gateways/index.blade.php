@extends('admin.layouts.backend')
@section('title', 'Payment Gateway Settings')
@section('content')
<div class="content-wrapper admin-dashboard-content">

    <!-- Page Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1><i class="fas fa-credit-card"></i> Payment Gateway Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Payment Gateway</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
			@php
				$icons = [
					'Stripe'   => 'fab fa-stripe',
					'PayPal'   => 'fab fa-paypal',
					'Wallet'   => 'fas fa-wallet',
					'COD'      => 'fas fa-truck',
					'Razorpay' => 'fas fa-credit-card',
					'Paytm'    => 'fas fa-mobile-alt',
					'Paystack' => 'fas fa-coins',
					'FlutterWave' => 'fas fa-water',
					'MercadoPago' => 'fas fa-globe',
					'Xendit'   => 'fas fa-random',
					'OrangePay'=> 'fas fa-money-bill',
					'MidTrans' => 'fas fa-exchange-alt',
					'Payfast'  => 'fas fa-bolt',
					'Default'  => 'fas fa-coins'
				];
			@endphp
            <div class="card card-primary card-outline card-outline-tabs shadow-sm">
                <div class="card-header p-0 border-bottom-0 bg-light">
                    <ul class="nav nav-tabs" id="gatewayTabs" role="tablist">
						@foreach($gateways as $name => $gateway)
							@php
								$slug = \Illuminate\Support\Str::slug($name);
								$icon = $icons[$name] ?? 'fas fa-coins'; // fallback icon
							@endphp
							<li class="nav-item">
								<a class="nav-link @if($loop->first) active @endif"
								   id="tab-{{ $slug }}-tab"
								   data-toggle="tab"
								   href="#tab-{{ $slug }}"
								   role="tab"
								   aria-controls="tab-{{ $slug }}"
								   aria-selected="{{ $loop->first ? 'true' : 'false' }}">
									<i class="{{ $icon }} nav-icon"></i> {{ $name }}
								</a>
							</li>
						@endforeach
					</ul>

                </div>

                <div class="card-body">
                    <div class="tab-content" id="gatewayTabsContent">
                        @foreach($gateways as $name => $gateway)
                            @php
                                $slug  = \Illuminate\Support\Str::slug($name);
                                $test  = $gateway->test_credentials ?? [];
                                $live  = $gateway->live_credentials ?? [];
                            @endphp

                            <div class="tab-pane fade show @if($loop->first) active @endif"
                                 id="tab-{{ $slug }}" role="tabpanel">

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.payment_gateways.update', $gateway->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><i class="fas fa-toggle-on"></i> Mode</label>
                                                <select name="mode" class="form-control">
                                                    <option value="test" {{ $gateway->mode === 'test' ? 'selected' : '' }}>Test</option>
                                                    <option value="live" {{ $gateway->mode === 'live' ? 'selected' : '' }}>Live</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center">
                                            <div class="form-check mt-4">
                                                <input type="hidden" name="status" value="0">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                       id="status_{{ $gateway->id }}" name="status"
                                                       {{ $gateway->status ? 'checked' : '' }}>
                                                <label class="form-check-label font-weight-bold"
                                                       for="status_{{ $gateway->id }}">
                                                    Enable {{ $name }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center">
                                            <div class="form-check mt-4">
                                                <input type="radio" name="is_default" value="1" id="default_{{ $gateway->id }}"
                                                       {{ $gateway->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label" for="default_{{ $gateway->id }}">
                                                    <i class="fas fa-star text-warning"></i> Set as Default
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    {{-- Gateway specific fields --}}
                                    @if($name === 'Stripe')
                                        <h5><i class="fab fa-stripe text-primary"></i> Stripe Test Keys</h5>
                                        <div class="form-group">
                                            <label>Test Publishable Key</label>
                                            <input type="text" name="test_credentials[publishable_key]" class="form-control"
                                                   value="{{ old('test_credentials.publishable_key', $test['publishable_key'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Test Secret Key</label>
                                            <input type="text" name="test_credentials[secret_key]" class="form-control"
                                                   value="{{ old('test_credentials.secret_key', $test['secret_key'] ?? '') }}">
                                        </div>

                                        <h5 class="mt-4"><i class="fab fa-stripe text-success"></i> Stripe Live Keys</h5>
                                        <div class="form-group">
                                            <label>Live Publishable Key</label>
                                            <input type="text" name="live_credentials[publishable_key]" class="form-control"
                                                   value="{{ old('live_credentials.publishable_key', $live['publishable_key'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Live Secret Key</label>
                                            <input type="text" name="live_credentials[secret_key]" class="form-control"
                                                   value="{{ old('live_credentials.secret_key', $live['secret_key'] ?? '') }}">
                                        </div>

                                    @elseif($name === 'PayPal')
                                        <h5><i class="fab fa-paypal text-primary"></i> PayPal Test Credentials</h5>
                                        <div class="form-group">
                                            <label>Client ID</label>
                                            <input type="text" name="test_credentials[client_id]" class="form-control"
                                                   value="{{ old('test_credentials.client_id', $test['client_id'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Secret</label>
                                            <input type="text" name="test_credentials[secret]" class="form-control"
                                                   value="{{ old('test_credentials.secret', $test['secret'] ?? '') }}">
                                        </div>

                                        <h5 class="mt-4"><i class="fab fa-paypal text-success"></i> PayPal Live Credentials</h5>
                                        <div class="form-group">
                                            <label>Client ID</label>
                                            <input type="text" name="live_credentials[client_id]" class="form-control"
                                                   value="{{ old('live_credentials.client_id', $live['client_id'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Secret</label>
                                            <input type="text" name="live_credentials[secret]" class="form-control"
                                                   value="{{ old('live_credentials.secret', $live['secret'] ?? '') }}">
                                        </div>

                                    @elseif($name === 'Wallet')
                                        <div class="form-group">
                                            <label><i class="fas fa-wallet"></i> Wallet Balance Field</label>
                                            <input type="text" name="test_credentials[balance_field]" class="form-control"
                                                   value="{{ old('test_credentials.balance_field', $test['balance_field'] ?? 'wallet_balance') }}">
                                        </div>
									
									@elseif($name === 'EMIS')
                                        <h5><i class="fab fa-paypal text-primary"></i> EMIS Test Credentials</h5>
                                        <div class="form-group">
                                            <label>EMIS frame token</label>
                                            <input type="text" name="test_credentials[emis_frame_token]" class="form-control" value="{{ old('test_credentials.emis_frame_token', $test['emis_frame_token'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Terminal ID</label>
                                            <input type="text" name="test_credentials[emis_termnal_id]" class="form-control"
                                                   value="{{ old('test_credentials.emis_termnal_id', $test['emis_termnal_id'] ?? '') }}">
                                        </div>
										
										 <div class="form-group">
                                            <label>EMIS URL</label>
                                            <input type="text" name="test_credentials[emis_url]" class="form-control"
                                                   value="{{ old('test_credentials.emis_url', $test['emis_url'] ?? '') }}">
                                        </div>

                                        <h5 class="mt-4"><i class="fab fa-paypal text-success"></i> EMIS Live Credentials</h5>
                                        <div class="form-group">
                                            <label>EMIS frame token</label>
                                            <input type="text" name="live_credentials[emis_frame_token]" class="form-control" value="{{ old('live_credentials.emis_frame_token', $live['emis_frame_token'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Terminal ID</label>
                                            <input type="text" name="live_credentials[emis_termnal_id]" class="form-control"
                                                   value="{{ old('live_credentials.emis_termnal_id', $live['emis_termnal_id'] ?? '') }}">
                                        </div>
										<div class="form-group">
                                            <label>EMIS URL</label>
                                            <input type="text" name="live_credentials[emis_url]" class="form-control"
                                                   value="{{ old('live_credentials.emis_url', $live['emis_url'] ?? '') }}">
                                        </div>

                                    @elseif($name === 'COD')
                                        <div class="form-group">
                                            <label><i class="fas fa-truck"></i> Notes</label>
                                            <input type="text" name="test_credentials[notes]" class="form-control"
                                                   value="{{ old('test_credentials.notes', $test['notes'] ?? 'Cash on Delivery') }}">
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label>Test API Key</label>
                                            <input type="text" name="test_credentials[api_key]" class="form-control"
                                                   value="{{ old('test_credentials.api_key', $test['api_key'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Test API Secret</label>
                                            <input type="text" name="test_credentials[api_secret]" class="form-control"
                                                   value="{{ old('test_credentials.api_secret', $test['api_secret'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Live API Key</label>
                                            <input type="text" name="live_credentials[api_key]" class="form-control"
                                                   value="{{ old('live_credentials.api_key', $live['api_key'] ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Live API Secret</label>
                                            <input type="text" name="live_credentials[api_secret]" class="form-control"
                                                   value="{{ old('live_credentials.api_secret', $live['api_secret'] ?? '') }}">
                                        </div>
                                    @endif
									<div class="form-group">
										<label><i class="fas fa-image"></i> Upload Logo</label>
										<input type="file" name="logo" class="form-control-file">

										@if($gateway->logo)
											<div class="mt-2">
												<img src="{{ asset('uploads/payment_gateways/' . $gateway->logo) }}" 
													 alt="{{ $name }} Logo" class="img-thumbnail" 
													 style="height:60px;">
											</div>
										@endif
									</div>
                                    <div class="mt-4">
                                        <button class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save {{ $name }} Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div><!-- /.card-body -->
            </div><!-- /.card -->

        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Make selected tab primary color */
    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: #fff !important;
        border-color: #007bff #007bff #fff;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
    }
</style>
@endpush
