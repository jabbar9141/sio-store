@php
    use Illuminate\Support\Facades\Auth;
    $status = Auth::user()->status;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'Order Item')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Order Item</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Show Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    @if (!$status)
        <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
            <div class="d-flex align-items-center">
                <div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 text-white">Your account is still not activated</h6>
                    <div class="text-white">Wait for admin to activate your account</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Order Item[{{ $item->order->order_id }}]</h4>
            </div>
            <div class="col-sm-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" align="center">Order Data</td>
                                </tr>
                                <tr>
                                    <td>Order ID</td>
                                    <td>{{ $item->order->order_id }}</td>
                                </tr>
                                <tr>
                                    <td>Item Status</td>
                                    <td><span class="badge bg-secondary">{{ $item->status }}</span></td>
                                </tr>
                                <tr>
                                    <td>Order Date</td>
                                    <td>{{ $item->order->created_at }}</td>
                                </tr>
                                <tr>
                                    <td>Item</td>
                                    <td><a
                                            href="{{ route('store.showProduct', $item->item->product_slug) }}">{{ $item->item->product_name }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Item-image</td>
                                    <td>
                                        <img src="{{ url('uploads/images/product/' . $item->item->product_thumbnail) }}"
                                            alt="Img">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Variant</td>
                                    <td>{{ $item->variant }}</td>
                                </tr>
                                <tr>
                                    <td>Quantity Ordered</td>
                                    <td>{{ $item->qty }}</td>
                                </tr>
                                <tr>
                                    <td>Quantity Currently In stock</td>
                                    <td>{{ $item->item->product_quantity }}</td>
                                </tr>
                                <tr>
                                    <th colspan="2">Customer Data</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $item->order->user->email }}</td>
                                </tr>
                                <tr>
                                    <th colspan="2">Reciever Data</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>
                                        @if (isset($item->order->shippingAddress))
                                            {{ $item->order->shippingAddress->address1 }},{{ $item->order->shippingAddress->address2 }}
                                            {{ $item->order->shippingAddress->zip }}
                                            {{ $item->order->shippingAddress->city }},
                                            {{ $item->order->shippingAddress->state }}
                                            {{ $item->order->shippingAddress->country }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact</td>
                                    <td>
                                        @if (isset($item->order->shippingAddress))
                                            P: {{ $item->order->shippingAddress->phone }}
                                            <br>
                                            E: {{ $item->order->shippingAddress->email }}
                                            <br>
                                            N: {{ $item->order->shippingAddress->firstname }}
                                            {{ $item->order->shippingAddress->lastname }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Modify Order Item Status.</h4>
                        <br>
                        <form action="{{ route('store.order.update.item.status', $item->id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="order_status">Update Status</label>
                                <select name="order_status" id="order_status" class="form-control">
                                    <option value="">---select action--</option>
                                    @if ($item->status !== 'Cancelled' && $item->status !== 'Shipped')
                                        @if ($item->status == 'Pending')
                                            <option value="Processing">Accept Order</option>
                                            <option value="Cancelled">Cancel Order</option>
                                        @elseif($item->status == 'Processing')
                                            <option value="Shipped">Get Shipping Label</option>
                                            <option value="Cancelled">Cancel Order</option>
                                            <option value="Processing">Re-Processing</option>
                                        @else
                                            <option value="Processing">Re-Processing</option>
                                            <option value="Cancelled">Cancel Order</option>
                                        @endif
                                    @endif

                                </select>
                                <br>
                                <button class="btn btn-primary"
                                    onclick="return confirm('Are you sure you wish to update the status of this order?')">
                                    Proceed
                                </button>
                            </div>
                        </form>
                        <hr>
                        @if ($item->status == 'Processing' && $item->tracking_id == null)
                            <form action="{{ route('store.create.label.item', $item->id) }}" method="post">
                                @csrf
                                <h5>Generate Shipping Label</h5>
                                <div class="form-group">
                                    <label for="pickup_date">Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control" id="pickup_date">
                                </div>
                                <br>
                                <button class="btn btn-primary">Proceed</button>
                            </form>
                        @elseif($item->status != 'Pending')
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ env('SHIPPING_TRACKING_URL') }}/{{ $item->tracking_id }}"
                                        target="_blank">Track Package/ View Label</a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

@endsection
