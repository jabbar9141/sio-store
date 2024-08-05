@extends('backend.layouts.app')
@section('PageTitle', 'Country List')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Country</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="dashboard"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Country Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb -->

    <div class="card">
        <div class="card-body">
            <h4>Country: {{ $country->name }}</h4>

            <form action="{{ route('admin-shipping-cost', ['id' => $country->id]) }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="cities">City</label>
                        <select multiple name="cities" id="cities" class="form-control select2" required>
                            <option value="all" selected>All Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="cost">Shipping Cost</label>
                        <input type="number" name="cost" id="shipping_cost" class="form-control" min="0" step="0.01"
                            @if (isset($country->shippingCosts[0]->cost)) value="{{ $country->shippingCosts[0]->cost }}" @endif required>
                    </div>
                    <div class="col-4">
                        <label for="cost">Weight</label>
                        <input type="number" name="weight" id="weight" class="form-control" min="0"
                            @if (isset($country->shippingCosts[0]->weight)) value="{{ $country->shippingCosts[0]->weight }}" @endif required>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>

        </div>
    </div>

@endsection
@section('js')
    <script>
        $('.select2').select2();
    </script>
@endsection
