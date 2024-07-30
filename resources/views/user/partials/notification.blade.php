@if (isset($success))
    <script>
        // Display SweetAlert success message
        Swal.fire({
            title: 'Success!',
            text: '{{ $success }}.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
@elseif (isset($error))
    <script>
        // Display SweetAlert error message
        Swal.fire({
            title: 'Error!',
            text: '{{$error}}.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>
@endif
