<!-- Bootstrap JS -->
<script src="{{ asset('backend_assets') }}/js/bootstrap.bundle.min.js"></script>
<!--plugins-->
<script src="{{ asset('backend_assets') }}/js/jquery.min.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.0.6/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/datatables.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.0.6/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/datatables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('backend_assets') }}/plugins/simplebar/js/simplebar.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/chartjs/js/Chart.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/sparkline-charts/jquery.sparkline.min.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/jquery-knob/excanvas.js"></script>
<script src="{{ asset('backend_assets') }}/plugins/jquery-knob/jquery.knob.js"></script>
<script>
    $(function() {
        $(".knob").knob();
    });
</script>
<script src="{{ asset('backend_assets') }}/js/index.js"></script>
<!--app JS-->
<script src="{{ asset('backend_assets') }}/js/app.js"></script>
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

