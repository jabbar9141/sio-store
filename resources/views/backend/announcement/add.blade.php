@php use Illuminate\Support\Facades\Auth; @endphp
@php
$role = Auth::user()->role;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Add new Announcement')
@section('content')

    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Brand</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route($role . '-profile')}}"><i class="bx
                    bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Add new Announcement</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->
    <div class="card">
        <div class="card-body">
            <h4 class="d-flex align-items-center mb-3">Add Announcement</h4>
            <br>
            <form action="" method="POST" enctype="multipart/form-data" id="anuncementForm">
                @method('POST')
                @csrf
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Title</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input name="title" type="text" class="form-control"
                               required autofocus/>
                        <small style="color: #e20000" class="error" id="title-error"></small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Link</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input name="links_to" type="url" class="form-control"
                               required autofocus/>
                        <small style="color: #e20000" class="error" id="links_to-error"></small>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Body</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <textarea name="body" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <h6 class="mb-0">Image</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                        <input name="image" id="image" class="form-control" type="file" >
                        <small style="color: #e20000" class="error" id="image-error"></small>

                        <div>
                            <img class="card-img-top"
                                 style="max-width: 250px; margin-top: 20px" id="show_image">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9 text-secondary">
                        <input type="submit" class="btn btn-primary px-4" value="Save Changes"
                        />
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection


@section('AjaxScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#brand_image').change(function(e){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#show_image').attr('src',e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });


        $('#anuncementForm').on('submit', function(event) {
                event.preventDefault();
                // Remove errors if the conditions are true
                $('#anuncementForm *').filter(':input.is-invalid').each(function() {
                    this.classList.remove('is-invalid');
                });
                $('#anuncementForm *').filter('.error').each(function() {
                    this.innerHTML = '';
                });

                const formData = new FormData(this);
         
                $.ajax({
                    url: "{{ route('announce.store') }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        // Remove errors if the conditions are true
                        $('#product_form *').filter(':input.is-invalid').each(function() {
                            this.classList.remove('is-invalid');
                        });
                        $('#product_form *').filter('.error').each(function() {
                            this.innerHTML = '';
                        });
                        Swal.fire({
                            icon: 'success',
                            title: "Announcement Created Successfully",
                            showDenyButton: false,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        var res = $.parseJSON(response.responseText);
                        $.each(res.errors, function(key, err) {
                            $('#' + key + '-error').text(err[0]);
                            $('#' + key).addClass('is-invalid');
                        });
                    }
                });
            });
    </script>
@endsection
