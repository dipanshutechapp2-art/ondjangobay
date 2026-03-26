@extends('layouts.app_inner')

@section('title', 'Wallet')

@section('content')
    <!-- Start of Main -->
    <main class="main">
        <!-- Start of Page Header -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title mb-0">My Wallet</h1>
            </div>
        </div>
        <!-- End of Page Header -->

        <!-- Start of Breadcrumb -->
        <nav class="breadcrumb-nav">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li>My Wallet</li>
                </ul>
            </div>
        </nav>
        <!-- End of Breadcrumb -->

        <!-- Start of PageContent -->
        <div class="page-content pt-2">
            <div class="container">
                <div class="tab tab-vertical row gutter-lg">
                    @include('account.sidebar')

                    <div class="tab-content mb-6">
                        <h3><i class="w-icon-wallet"></i> My Wallet</h3>
						@if (session('success'))
							<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
								{{ session('success') }}
							</div><br/>
						@endif
						@if (session('error'))
							<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
								{{ session('error') }}
							</div><br/>
						@endif
                        <div class="tab-pane active in">
                            <!-- Wallet Balance -->
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h4>💳 Your Wallet Balance</h4>
                                    <h2 class="text-success">{{formatCurrency($balance)}}</h2>
                                    <div class="mt-3">
                                        <a href="{{ route('wallet.add') }}" class="btn btn-primary">
                                            ➕ Add Money
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction History -->
							@if($transactions->isNotEmpty())
							
								<h3> <i class="w-icon-orders"></i> Transaction History</h3>
								<div class="wallet-table">
								<table class="shop-table account-orders-table mb-6">
									<thead>
										<tr>
											<th align="left" class="order-id">Type</th>
											<th align="left" class="order-date">Amount</th>
											<th align="left" class="order-date">Method</th>
											<th align="left" class="order-status">Status</th>
											<th align="left" class="order-total">Txn ID</th>
											<th align="left" class="order-total">Remarks</th>
											<th align="left" class="order-date">Date</th>
										</tr>
									</thead>
									<tbody>
										@if($transactions->isNotEmpty())
											@forelse($transactions as $tx)
												<tr>
													<td>
														@if($tx->type == 'credit')
															<span class="badge bg-success">Credit</span>
														@else
															<span class="badge bg-danger">Debit</span>
														@endif
													</td>
													<td>{{ $tx->currency_obj->symbol }}{{ number_format($tx->amount, 2) }}</td>
													<td>{{ ucfirst($tx->method ?? 'N/A') }}</td>
													<td>
														@if($tx->status == 'completed')
															<span class="badge bg-success">Completed</span>
														@elseif($tx->status == 'pending')
															<span class="badge bg-warning text-dark">Pending</span>
														@else
															<span class="badge bg-danger">Failed</span>
														@endif
													</td>
													<td>{{ $tx->transaction_id ?? 'N/A' }}</td>
													<td>{{ $tx->remarks ?? '-' }}</td>
													<td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
												</tr>
											@endforeach
										@endif	
									</tbody>
								</table>
								</div>
								@if($transactions->hasPages())
									<div class="d-flex justify-content-center">
										{{ $transactions->links('pagination::bootstrap-5') }}
									</div>
								@endif
								
							@else
								<p>No transactions yet.</p><br/>
							@endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of PageContent -->
    </main>
    <!-- End of Main -->
@endsection
