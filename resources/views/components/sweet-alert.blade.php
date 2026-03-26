<!-- resources/views/components/sweet-alert.blade.php -->
@if(session('success') || session('error'))
<script>
Swal.fire({
    icon: '{{ session('success') ? 'success' : 'error' }}',
    title: '{{ session('success') ? 'Success' : 'Error' }}',
    text: '{{ session('success') ?? session('error') }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif

@if(session('post_limit_reached'))
<script>
Swal.fire({
    icon: 'warning',
    title: 'Post Limit Reached',
    text: '{{ session('post_limit_reached') }}',
    showConfirmButton: true,
    confirmButtonText: 'View Plans',
    showCancelButton: true,
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href = '{{ url('plans') }}';
    }
    // No action needed for cancel, it just closes the alert
});
</script>
@endif

 @if(session('subscription_post_limit'))
<script>
Swal.fire({
    icon: 'warning',
    title: 'Subscription Post Limit',
    text: '{{ session('subscription_post_limit') }}',
    showConfirmButton: true,
    confirmButtonText: 'Upgrade Subscription',
    showCancelButton: true,
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        window.location.href = '{{ url('plans') }}';
    }
    // No action needed for cancel, it just closes the alert
});
</script>
@endif
