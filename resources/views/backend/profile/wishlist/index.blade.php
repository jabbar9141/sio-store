@php
    use Illuminate\Support\Facades\Auth;
    $data = Auth::user();
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Wishlist')
@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">User</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">

                    <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @if (isset($wishlist) && count($wishlist) > 0)
                        @foreach ($wishlist as $list)
                            <div class="col-sm-6">
                                <div class="card h-100">
                                    <div class="card-body shadow">
                                        <table class="table">
                                            <tr>
                                                <td>
                                                    <img src="/uploads/images/product/{{ $list->product->product_thumbnail }}"
                                                        alt="product img {{ $list->product->product_name }}" width="100%">
                                                </td>
                                                <td>
                                                    <a href="{{ route('store.showProduct', $list->product->product_slug) }}" class="text-decoration-none">
                                                    {{ $list->product->product_name }}
                                                </a>
                                                    <br>
                                                    <br>
                                                    <span class="text-secondary">Unit Price :
                                                        &euro;{{ $list->product->product_price }}
                                                    </span>
                                                    &nbsp; &nbsp;&nbsp;
                                                    <span class="text-secondary">
                                                        Added : {{ ($list->created_at) ? $list->created_at->diffForHumans() : 'N/A' }}
                                                    </span>
                                                    <br>
                                                </td>

                                            </tr>

                                            <tr>
                                                <td>

                                                    @php
                                                        $max_stars = 5;
                                                        $rating_avg = $list->product->getProductReviewsAvg() ?? 0;
                                                        $rating_avg_rounded = round($rating_avg * 2) / 2; // Round the rating average to the nearest half
                                                    @endphp

                                                    <div class="text-primary mr-2">
                                                        @for ($i = 1; $i <= $max_stars; $i++)
                                                            @if ($i <= $rating_avg_rounded)
                                                                <i class="fas fa-star"></i>
                                                                <!-- Full star for whole numbers and half stars -->
                                                            @elseif ($i - 0.5 == $rating_avg_rounded)
                                                                <i class="fas fa-star-half-alt"></i> <!-- Half star -->
                                                            @else
                                                                <i class="fas fa-star text-secondary"></i>
                                                                <!-- Gray star for unrated stars -->
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="2">

                                                </td>
                                            </tr>
                                        </table>

                                        <div class="d-flex justify-content-between">
                                            <!-- Delete link with confirmation -->
                                            <a href="#" class="text-danger"
                                                onclick="deleteItem('{{ $list->id }}')">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </a>
                                            <form id="delete-item-{{ $list->id }}"
                                                action="{{ route('wishlist.destroy', $list->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No items in wishlist</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{ $wishlist->links() }}
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
        function deleteItem(id) {
            event.preventDefault();
            let y = confirm('Are you sure you want to remove this item form your wishlist?');
            if (y) {
                document.getElementById('delete-item-' + id).submit();
            }
        }
    </script>
@endsection
