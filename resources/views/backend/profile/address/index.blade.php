@php
    use Illuminate\Support\Facades\Auth;
    $data = Auth::user();
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Shipping Addresses')
@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">User</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">

                    <li class="breadcrumb-item active" aria-current="page">Shipping Addresses</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @if (isset($addresses) && count($addresses) > 0)
                        @foreach ($addresses as $add)
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body shadow">
                                        <div class="col-12">
                                            Name : {{ $add->firstname }} {{ $add->lastname }} <br>
                                            Email: {{ $add->email }}, Phone: {{ $add->phone }} <br>
                                            <br>
                                            Address: {{ $add->address1 }}, {{ $add->address2 }}.
                                            {{ $add->city }}
                                            {{ $add->zip }} {{ $add->state }}, {{ $add->country }}.

                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('address.edit', $add->id) }}"> <i class="fas fa-edit"></i> Edit</a>

                                                <!-- Delete link with confirmation -->
                                                <a href="#" class="text-danger" onclick="deleteAdd('{{$add->id}}')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                                <form id="delete-address-{{ $add->id }}" action="{{ route('address.destroy', $add->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{$addresses->links()}}
                    @else
                        <h6>No addresses added yet, click <a href="{{ route('address.create') }}">here</a> to add one</h6>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@section('AjaxScript')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
@endsection

@section('js')
    <script>
        function deleteAdd(id){
            event.preventDefault();
            let y = confirm('Are you sure you want to delete this address?');
            if(y){
                document.getElementById('delete-address-'+id).submit();
            }
        }
    </script>
@endsection
