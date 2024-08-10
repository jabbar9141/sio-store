@php
    use App\MyHelpers;
    use Illuminate\Support\Facades\Auth;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Vendors')
@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Vendor</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Vendor List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="data_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th>View Details</th>
                            <th>Activate</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->user->email ?? '-' }}</td>
                                <td>{{ MyHelpers::getDiffOfDate($item->user->created_at ?? '-') }}</td>
                                {{--                            <td>{{$item->status}}</td> --}}
                                <td>
                                    @if ($item->user->status)
                                        <div class="badge rounded-pill bg-light-success text-success w-100">Active</div>
                                    @else
                                        <div class="badge rounded-pill bg-light-danger text-danger w-100">Not active</div>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm radius-30 px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleVerticallycenteredModal-{{ $item->user->id }}">View
                                        Details
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleVerticallycenteredModal-{{ $item->user->id }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Vendor Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ url('uploads/images/profile/' . $item->user->photo ?? '-') }}"
                                                        class="card-img-top"
                                                        style="max-width: 300px; margin-left:
                                                         10px">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Name : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->user->name ?? '-' }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Email : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->user->email ?? '-' }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Username : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->user->username ?? '-' }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Address : <span
                                                                style="font-weight:lighter">{{ $item->user->address ?? 'No address' }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Phone Number : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->user->phone_number ?? 'No phone number' }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Status : <span
                                                                style="font-weight:
                                                         lighter">
                                                                @if ($item->user->status)
                                                                    <span style="color: lime">active</span>
                                                                @else
                                                                    <span style="color: red">Not active</span>
                                                                @endif
                                                            </span>
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin-activate-vendor') }}"
                                        class="active-deactive-form">
                                        @csrf
                                        <input name="vendor_id" value="{{ $item->user->id }}" hidden />
                                        <input name="current_status" value="{{ $item->user->status }}" hidden />
                                        <div class="form-check form-switch">
                                            @if ($item->user->status)
                                                <input name="de_activate"
                                                    class="btn
                                            btn-outline-danger"
                                                    type="submit" value="De-Active">
                                            @else
                                                <input name="activate"
                                                    class="btn
                                            btn-outline-success"
                                                    type="submit" value=" Activate ">
                                            @endif

                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex order-actions">
                                        <a href="javascript:void(0)"
                                            onclick="deleteVendor('{{ $item->vendor_id }}')">
                                            <i class='bx bxs-trash'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                </table>
            </div>
        </div>
    </div>
@endsection
@section('plugins')
    <link href="{{ asset('backend_assets') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js')
    <script src="{{ asset('backend_assets') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('backend_assets') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#data_table').DataTable({
                lengthChange: true,
                buttons: ['excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#data_table_wrapper .col-md-6:eq(0)');
        });
    </script>

    <script src="sweetalert2.all.min.js"></script>



@section('AjaxScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#brand_image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#show_image').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('form.active-deactive-form').click('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: "{{ route('admin-activate-vendor') }}",
                    method: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.msg,
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {

                    }
                });
            });

        });

        function deleteVendor(vendorId) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin-vendor-remove') }}",
                        method: 'POST',
                        data: {
                            id: vendorId
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: response.msg,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(response) {
                            console.log(response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
