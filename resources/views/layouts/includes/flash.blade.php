@if (session()->has('message_warning'))
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Heads Up!',
        text: '{{ session()->get('message_warning') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
    });
</script>
@endif

@if (session()->has('message_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session()->get('message_success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
    });
</script>
@endif

@if (session()->has('message_info'))
<script>
    Swal.fire({
        icon: 'info',
        title: 'Did You Know?',
        text: '{{ session()->get('message_info') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
    });
</script>
@endif

@if (session()->has('message_danger'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session()->get('message_danger') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
    });
</script>
@endif