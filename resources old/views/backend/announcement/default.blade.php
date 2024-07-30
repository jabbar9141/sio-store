@php
    use Illuminate\Support\Facades\Auth;
    $role = Auth::user()->role;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Announcements')
@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Announcement</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route($role . '-profile') }}"><i
                                class="bx
                    bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="ms-auto" style="margin-bottom: 20px">
                    <a href="{{ route('announce.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                        <i class="bx bxs-plus-square"></i>Add New Announcement</a>
                </div>

                <table id="data_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Link</th>
                            <th>View Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ann as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->links_to }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm radius-30 px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleVerticallycenteredModal-{{ $item->id }}">View
                                        Details
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleVerticallycenteredModal-{{ $item->id }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Announcement Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ url('uploads/images/announcements/' . $item->image) }}"
                                                        class="card-img-top"
                                                        style="max-width: 300px; margin-left:
                                                         10px">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Title : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->title }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Body : <span
                                                                style="font-weight:
                                                         lighter">{{ $item->body }}</span>
                                                        </h5>
                                                        <h5 class="card-title">Link : <span
                                                                style="font-weight:
                                                        lighter">{{ $item->links_to }}</span>
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
                                    <div class="d-flex order-actions">
                                        <a href="" class="" data-bs-toggle="modal"
                                            data-bs-target="#exampleFullScreenModal-{{ $item->id }}"><i
                                                class='bx
                                       bxs-edit'></i></a>

                                        <div class="modal fade" id="exampleFullScreenModal-{{ $item->id }}"
                                            tabindex="-1" aria-hidden="true" style="display: none;">
                                            <div class="modal-dialog modal-fullscreen">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Announcement</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <form action="{{ route('announce.update', $item->id) }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="row mb-3">
                                                                        <div class="col-sm-3">
                                                                            <h6 class="mb-0">Title</h6>
                                                                        </div>
                                                                        <div class="col-sm-9 text-secondary">
                                                                            <input name="title" type="text"
                                                                                class="form-control"
                                                                                value="{{ $item->title }}" required
                                                                                autofocus />
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-sm-3">
                                                                            <h6 class="mb-0">Link</h6>
                                                                        </div>
                                                                        <div class="col-sm-9 text-secondary">
                                                                            <input name="links_to" type="text"
                                                                                class="form-control"
                                                                                value="{{ $item->links_to }}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-sm-3">
                                                                            <h6 class="mb-0">Body</h6>
                                                                        </div>
                                                                        <div class="col-sm-9 text-secondary">
                                                                            <textarea name="body" class="form-control">{{ $item->body }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-sm-3">
                                                                            <h6 class="mb-0">Image</h6>
                                                                        </div>
                                                                        <div class="col-sm-9 text-secondary">
                                                                            <input name="image" id="image"
                                                                                class="form-control" type="file">
                                                                            <div>
                                                                                <img class="card-img-top"
                                                                                    src="{{ url('uploads/images/announcements/' . $item->image) }}"
                                                                                    style="max-width: 250px; margin-top: 20px"
                                                                                    id="show_image">
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-3"></div>
                                                                        <div class="col-sm-9 text-secondary">
                                                                            <input type="submit"
                                                                                class="btn btn-primary px-4"
                                                                                value="Save Changes" />
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="" class="ms-3" data-bs-toggle="modal"
                                            data-bs-target="#exampleDangerModal-{{ $item->id }}">

                                            <i class='bx bxs-trash'></i>
                                            <!-- Modal -->
                                            <div class="modal fade" id="exampleDangerModal-{{ $item->id }}"
                                                tabindex="-1" style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content bg-danger">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-white">Sure ?</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Cancel
                                                            </button>
                                                            <form action="{{ route('announce.destroy', $item->id) }}"
                                                                id="delete_ann_{{ $item->id }}" method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button
                                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();"
                                                                class="btn btn-dark">Confirm
                                                            </button>
                                                            </form>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection
@endsection
