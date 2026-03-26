@extends('layouts.app_inner')
@section('title', 'Linked Accounts')
@section('content')
<!-- Start of Main -->
<main class="main">
    <!-- Start of Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title mb-0">Linked Accounts</h1>
        </div>
    </div>
    <!-- End of Page Header -->

    <!-- Start of Breadcrumb -->
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Linked Accounts</li>
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

                        @if($user->logins->isNotEmpty())
                            <table class="shop-table account-orders-table mb-6">
                                <thead>
                                    <tr>
                                        <th>Login Method</th>
                                        <th>Identifier</th>
                                        <th>Primary</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->logins as $login)
                                        <tr align="center">
                                            <td>{{ $login->method->name ?? 'N/A' }}</td>
                                            <td>{{ $login->identifier }}</td>
                                            <td>
                                                @if($login->is_primary)
                                                    <span class="badge bg-success">Primary</span>
                                                @else
                                                    <span class="badge bg-secondary">Secondary</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="order-buttons">
                                                    @if(!$login->is_primary)
                                                        <form action="{{ route('user.switchPrimary', $login->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to set this login as primary?');">
                                                            @csrf
                                                            <button class="btn btn-sm btn-primary" title="Set as Primary">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('user.unlinkLogin', $login->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to unlink this login method?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger" title="Unlink Login">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No linked accounts found.</p>
                        @endif
						
						<h4>Link a New Login Method</h4>
                        <div class="mb-4">
                            @foreach($availableMethods as $method)
								@php
									$isLinked = $user->logins->contains('login_method_id', $method->id);
								@endphp
								@if($isLinked)
									<button class="btn btn-outline btn-secondary mb-2" disabled>
										{{ $method->name }} already linked
									</button>
								@else
									@if($method->code=='google')
										<a id="linkGoogleBtn" class="btn btn-outline btn-primary mb-2">
											Link {{ $method->name }}
										</a>
									@elseif($method->code=='facebook')
										<a  id="linkFacebookBtn" class="btn btn-outline btn-primary mb-2">
											Link {{ $method->name }}
										</a>
									@elseif($method->code=='apple')
										<a href="#" class="btn btn-outline btn-primary mb-2">
											Link {{ $method->name }}
										</a>
									@elseif($method->code=='biometric')
										<a href="#" class="btn btn-outline btn-primary mb-2">
											Link {{ $method->name }}
										</a>
									@else
										<a href="{{ route('user.addLogin', $method->code) }}" class="btn btn-outline btn-primary mb-2">
											Link {{ $method->name }}
										</a>
									@endif
								@endif
                            @endforeach
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of PageContent -->
</main>
<!-- End of Main -->
<!-- Firebase SDK -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getAuth, signInWithPopup, GoogleAuthProvider, FacebookAuthProvider } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

    const firebaseConfig = {
        apiKey: "AIzaSyCcuUxjs4Co2MU9o-7X7pitGDecCpKh7mA",
        authDomain: "exco-e7b3f.firebaseapp.com",
        projectId: "exco-e7b3f"
    };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    async function sendToLaravel(token, uid, provider, email) {
        try {
            const response = await fetch("{{ route('link-social-account') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    provider_code: provider,
                    identifier: uid,
                    secret: token,
					email: email
                })
            });
            const data = await response.json();
			
            if (data.success) {
				Swal.fire({
					icon: 'success',
					title: 'Linked!',
					text: `Your ${provider.charAt(0).toUpperCase() + provider.slice(1)} account has been linked successfully!`,
					confirmButtonColor: '#3085d6',
				}).then((result) => {
					if (result.isConfirmed) {
						location.reload(); 
					}
				});
            } else {
                Swal.fire({
					icon: 'error',
					title: 'Failed!',
					text: data.message || "Linking failed.",
					confirmButtonColor: '#d33',
				}).then((result) => {
					if (result.isConfirmed) {
						location.reload();
					}
				});
            }
        } catch (err) {
            console.error(err);
            
			Swal.fire({
				icon: 'error',
				title: 'Error!',
				text: "Error linking account.",
				confirmButtonColor: '#d33',
			}).then((result) => {
				if (result.isConfirmed) {
					location.reload();
				}
			});
        }
    }

    async function linkGoogle() {
        const provider = new GoogleAuthProvider();
        try {
            const result = await signInWithPopup(auth, provider);
            const token = await result.user.getIdToken();
            const uid = result.user.uid;
			const email = result.user.email;
            sendToLaravel(token, uid, 'google',email);
        } catch (error) {
            alert("Google linking failed: " + error.message);
        }
    }

    async function linkFacebook() {
        const provider = new FacebookAuthProvider();
        try {
            const result = await signInWithPopup(auth, provider);
            const token = await result.user.getIdToken();
            const uid = result.user.uid;
			const email = result.user.email;
            sendToLaravel(token, uid, 'facebook',email);
        } catch (error) {
            alert("Facebook linking failed: " + error.message);
        }
    }

    document.getElementById('linkGoogleBtn').addEventListener('click', linkGoogle);
    document.getElementById('linkFacebookBtn').addEventListener('click', linkFacebook);
    </script>
@endsection
