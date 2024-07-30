@php
    use Illuminate\Support\Facades\Auth;
    $data = Auth::user();
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Shop Orders')
@section('content')
    <style>
        .rating .fa-star {
            color: #ccc;
            cursor: pointer;
            font-size: 2em;
            /* Set font size to 2em for larger stars */
        }

        .rating-e .fa-star {
            color: #ccc;
            cursor: pointer;
            font-size: 2em;
            /* Set font size to 2em for larger stars */
        }

        .rating .fa-star.checked {
            color: yellow;
        }
    </style>
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">User</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">

                    <li class="breadcrumb-item active" aria-current="page">Shop Orders</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @if (isset($orders) && count($orders) > 0)
                        @foreach ($orders as $order)
                            <div class="col-12">
                                <div style="border: 1px solid gray;" class="p-2">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h6>Order details</h6>
                                            <hr>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Order ID</td>
                                                    <td>{{ $order->order_id }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Order Status</td>
                                                    <td><span class="badge bg-secondary">{{ $order->status }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Order Date</td>
                                                    <td>{{ $order->created_at }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Shipping Option</td>
                                                    <td>{{ $order->shipping_method }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Payment Method</td>
                                                    <td>{{ $order->payment_method }}</td>
                                                </tr>
                                            </table>
                                            <h6>Payment Details</h6>
                                            @php
                                                $pay = $order->payment ?? null;
                                            @endphp
                                            @if (null != $pay)
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Reference</td>
                                                        <td>{{ substr($pay->ref, 0, 5) }}***{{ substr($pay->ref, strlen($pay->ref) - 10, 6) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Method</td>
                                                        <td>{{ $pay->payment_method }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Amount</td>
                                                        <td>&euro;{{ number_format($pay->amount / 100, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Method</td>
                                                        <td><span class="badge bg-secondary">{{ $pay->status }}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @else
                                                <i>No payment made yet</i>
                                            @endif
                                            <br>
                                            <h6>Shipping address</h6>
                                            <hr>
                                            @php
                                                $add = $order->shippingAddress ?? null;
                                            @endphp
                                            @if (null != $add)
                                                Name : {{ $add->firstname }} {{ $add->lastname }} <br>
                                                Email: {{ $add->email }}, Phone: {{ $add->phone }} <br>
                                                <br>
                                                Address: {{ $add->address1 }}, {{ $add->address2 }}.
                                                {{ $add->city }}
                                                {{ $add->zip }} {{ $add->state }}, {{ $add->country }}.
                                            @else
                                                <i>No address set yet</i>
                                            @endif

                                            <div class="d-flex justify-content-between">
                                                {{-- <a href="#"> <i class="fas fa-map"></i>Track</a> --}}

                                                <!-- cancel link with confirmation -->
                                                <a href="#" class="text-danger"
                                                    onclick="cancelOrder('{{ $order->id }}')">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                                <form id="cancel-order-{{ $order->id }}" action="#" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h6>Order Items</h6>
                                            <table class="table">
                                                @foreach ($order->items as $item)
                                                    <tr>
                                                        <td>
                                                            <img src="/uploads/images/product/{{ $item->item->product_thumbnail }}"
                                                                alt="product img {{ $item->item->product_name }}"
                                                                width="150px">
                                                        </td>
                                                        <td>
                                                            {{ $item->item->product_name }}
                                                            <br>
                                                            <br>
                                                            <span class="text-secondary">Unit Price :
                                                                &euro;{{ $item->item->product_price }}</span>
                                                            <br>
                                                            <span class="text-secondary">Qty:
                                                                {{ $item->qty }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Item Status: <span
                                                                class="badge bg-secondary">{{ $item->status }}</span></td>
                                                        <td>
                                                            @if ($item->tracking_id != null)
                                                                <a href="{{ env('SHIPPING_TRACKING_URL') }}/{{ $item->tracking_id }}"
                                                                    target="_blank"><i class="fas fa-map"></i>Track
                                                                    Item/ View Label</a>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    @if (auth()->user()->hasReviewedProduct($item->item->product_id))
                                                        @php
                                                            $review = auth()
                                                                ->user()
                                                                ->getProductReview($item->item->product_id);
                                                            $rating = $review->rating;
                                                        @endphp
                                                        <tr>
                                                            <th colspan="2">Your review - {{ $review->created_at }}
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                @php
                                                                    $max_stars = 5;
                                                                @endphp

                                                                @for ($i = 1; $i <= $max_stars; $i++)
                                                                    @if ($i <= $rating)
                                                                        <i class="fas fa-star text-warning"></i>
                                                                        <!-- Yellow star for rated stars -->
                                                                    @else
                                                                        <i class="fas fa-star text-secondary"></i>
                                                                        <!-- Gray star for unrated stars -->
                                                                    @endif
                                                                @endfor
                                                            </td>
                                                            <td>
                                                                {{ $review->comment }}
                                                                <div class="d-flex justify-content-between">
                                                                    <button type="button" class="btn btn-link"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editReviewModal{{ $item->id }}">
                                                                        <span class="fas fa-edit"></span>Edit Review
                                                                    </button>

                                                                    <!-- Delete link with confirmation -->
                                                                    <a href="#" class="text-danger"
                                                                        onclick="deleteReview('{{ $review->id }}')">
                                                                        <i class="fas fa-trash-alt"></i> Delete Review
                                                                    </a>
                                                                    <form id="delete-review-{{ $review->id }}"
                                                                        action="{{ route('user-product-review.destroy', $review->id) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </div>


                                                                <!-- Modal -->
                                                                <div class="modal fade"
                                                                    id="editReviewModal{{ $item->id }}" tabindex="-1"
                                                                    aria-labelledby="editReviewModal{{ $item->id }}Label"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="editReviewModal{{ $item->id }}Label">
                                                                                    Edit Review
                                                                                    [{{ $item->item->product_name }}]
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form
                                                                                    action="{{ route('user-product-review.update', $review->id) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    @method('PUT')
                                                                                    <div class="mb-3">
                                                                                        old rating
                                                                                        @php
                                                                                            $max_stars = 5;
                                                                                        @endphp

                                                                                        @for ($i = 1; $i <= $max_stars; $i++)
                                                                                            @if ($i <= $rating)
                                                                                                <i
                                                                                                    class="fas fa-star text-warning"></i>
                                                                                                <!-- Yellow star for rated stars -->
                                                                                            @else
                                                                                                <i
                                                                                                    class="fas fa-star text-secondary"></i>
                                                                                                <!-- Gray star for unrated stars -->
                                                                                            @endif
                                                                                        @endfor
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="rating-e"
                                                                                            class="form-label">New
                                                                                            Rating:</label>
                                                                                        <div class="rating-e"
                                                                                            id="rating-e">
                                                                                            <i class="fas fa-star"
                                                                                                data-value="1"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="2"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="3"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="4"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="5"></i>
                                                                                        </div>
                                                                                        <input type="hidden" name="rating"
                                                                                            id="rating-value-e">

                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="comment"
                                                                                            class="form-label">Comment
                                                                                            (optional)
                                                                                            :</label>
                                                                                        <textarea class="form-control" id="comment" name="comment" rows="3">{{ $review->comment }}</textarea>
                                                                                    </div>
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary">Update
                                                                                        Review</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="2">
                                                                <button type="button" class="btn btn-primary btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#reviewModal{{ $item->id }}">
                                                                    Review
                                                                </button>


                                                                <!-- Modal -->
                                                                <div class="modal fade"
                                                                    id="reviewModal{{ $item->id }}" tabindex="-1"
                                                                    aria-labelledby="reviewModal{{ $item->id }}Label"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="reviewModal{{ $item->id }}Label">
                                                                                    Review
                                                                                    [{{ $item->item->product_name }}]
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form
                                                                                    action="{{ route('user-product-review.store') }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <div class="mb-3">
                                                                                        <label for="rating"
                                                                                            class="form-label">Rating:</label>
                                                                                        <div class="rating"
                                                                                            id="rating">
                                                                                            <i class="fas fa-star"
                                                                                                data-value="1"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="2"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="3"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="4"></i>
                                                                                            <i class="fas fa-star"
                                                                                                data-value="5"></i>
                                                                                        </div>
                                                                                        <input type="hidden"
                                                                                            name="rating"
                                                                                            id="rating-value">

                                                                                    </div>
                                                                                    <input type="hidden"
                                                                                        name="product_id"
                                                                                        value="{{ $item->item->product_id }}">
                                                                                    <div class="mb-3">
                                                                                        <label for="comment"
                                                                                            class="form-label">Comment
                                                                                            (optional)
                                                                                            :</label>
                                                                                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                                                                                    </div>
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary">Submit
                                                                                        Review</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td colspan="2">

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{ $orders->links() }}
                    @else
                        <h6>No orders added yet, click <a href="/">here</a> to browse products</h6>
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
        function cancelOrder(id) {
            event.preventDefault();
            let y = confirm('Are you sure you want to cancel this order?');
            if (y) {
                // document.getElementById('cancel-order-'+id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.rating .fa-star').click(function() {
                const value = $(this).data('value');
                $('.rating .fa-star').css('color', '#ccc'); // Reset color of all stars
                $(this).prevAll('.fa-star').addBack().css('color',
                    'yellow'); // Color the clicked star and preceding stars
                $('#rating-value').val(value);
                // alert($('#rating-value').val())
            });
        });

        $(document).ready(function() {
            $('.rating-e .fa-star').click(function() {
                const value = $(this).data('value');
                $('.rating-e .fa-star').css('color', '#ccc'); // Reset color of all stars
                $(this).prevAll('.fa-star').addBack().css('color',
                    'yellow'); // Color the clicked star and preceding stars
                $('#rating-value-e').val(value);
                // alert($('#rating-value-e').val())
            });
        });
    </script>

    <script>
        function deleteReview(id) {
            event.preventDefault();
            let y = confirm('Are you sure you want to delete this review?');
            if (y) {
                document.getElementById('delete-review-' + id).submit();
            }
        }
    </script>
@endsection
