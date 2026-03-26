<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if (session('success'))
		<span style="color: green; font-weight: bold;">
			{{ session('success') }}
		</span><br/>
	@endif
    <form method="POST" action="{{ route('verify.otp') }}">
        @csrf
			<div>
				<x-input-label for="email" :value="__('OTP')" />
				<input name="otp" type="text" placeholder="Enter OTP"  class="block mt-1 w-full" required>
				<x-input-error :messages="$errors->get('otp')" class="mt-2" />
			</div><br/>
            <x-primary-button class="ms-3">
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
