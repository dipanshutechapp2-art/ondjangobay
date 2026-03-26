@extends('layouts.app_inner')
@section('title', 'Link ' . ucfirst($loginMethod->name))
@section('content')

<main class="main">
    <div class="page-header">
        <div class="container">
            <h1 class="page-title mb-0">Link {{ $loginMethod->name }}</h1>
        </div>
    </div>

    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('account.link_accounts') }}">Linked Accounts</a></li>
                <li>Link {{ $loginMethod->name }}</li>
            </ul>
        </div>
    </nav>

    <div class="page-content pt-2">
        <div class="container">
            <div class="tab tab-vertical row gutter-lg">
                @include('account.sidebar')

                <div class="tab-content mb-6">
                    <div class="tab-pane active in">
						 <h3><i class="w-icon-account"></i> Linked Login Methods</h3>
						   @if (session('success'))
								<div class="alert alert-icon alert-success alert-bg alert-inline show-code-action">
									{{ session('success') }}
								</div>
							@endif

							@if (session('error'))
								<div class="alert alert-icon alert-error alert-bg alert-inline show-code-action">
									{{ session('error') }}
								</div>
							@endif
                        <form action="{{ route('user.storeLogin') }}" method="POST">
                            @csrf
                            <input type="hidden" name="login_method" value="{{ $loginMethod->code }}">
                            <div class="mb-3">
                                <label class="form-label">Identifier</label>
                                <input type="text" name="identifier" class="form-control" placeholder="Email / Phone" required>
                            </div>
                            @if(in_array($loginMethod->code, ['email', 'phone']))
								<div class="mb-3">
									<label class="form-label">Password</label>
									<input type="password" name="secret" class="form-control" placeholder="Password" required>
								</div>
                            @endif
                            <button type="submit" class="btn btn-primary">Link {{ $loginMethod->name }}</button>
                            <a href="{{ route('account.link_accounts') }}" class="btn btn-secondary">Cancel</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
