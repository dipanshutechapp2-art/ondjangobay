@extends('layouts.app_inner')
@section('title', 'EMIS Payment')
@section('content')

<div class="container mt-4">
    <iframe
        src="{{ $emisIframeOrigin }}/online-payment-gateway/webframe/frame?token={{ $frameToken }}"
        width="100%"
        height="820"
        frameborder="0"
        allowfullscreen>
    </iframe>
</div>

<script> 
    window.addEventListener("message", function (event) {

        if (event.origin !== "{{ $emisIframeOrigin }}") {
            return;
        }

        console.log('EMIS Result:', event.data);

        if (event.data?.status === 'PAYMENT_COMPLETED') {
            window.location.href = "{{ route('order.order_complete') }}";
        }

        if (event.data?.status === 'PAYMENT_FAILED') {
            window.location.href = "{{ route('emis.payment.failed') }}";
        }

    }, false);
</script>
@endsection
