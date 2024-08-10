@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\product\ProductModel;
    $status = Auth::user()->status;
@endphp
@extends('backend.layouts.app')
@section('PageTitle', 'All Walk-in Orders')


@section('content')
    <!--breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Orders</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Walk-In Orders List</li>
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
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <form action="" class="form-inline" method="get">
                            <div class="row">
                                <small>Select date range and click proceed to get transactions</small>
                                <br>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="start_date" placeholder="start date"
                                        value="{{ request()->start_date ?? '' }}" required>
                                </div>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control" name="end_date" placeholder="end date"
                                        value="{{ request()->end_date ?? '' }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary" id="get_filter_btn">Proceed</button>
                                    <button id="print-btn" class="btn btn-primary">Print</button>
                                    <button id="pdf-btn" class="btn btn-danger">PDF</button>
                                    <button id="csv-btn" class="btn btn-success">CSV</button>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="col-md-3">
                        <form action="" class="form-inline" method="get">
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="slip_serial_no" placeholder="order id"
                                        value="{{ request()->slip_serial_no ?? '' }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <hr>
                @if (isset($stats))
                    <table class="table table-bordered">
                        <tr>
                            <th>Start Date</th>
                            <td>{{ request()->start_date ?? 'N/A' }}</td>
                            <th>End Date</th>
                            <td>{{ request()->end_date ?? 'N/A' }}</td>
                            <th>Status filter</th>
                            <td>{{ request()->status ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $stats['total_amt']) }}</td>
                            <th>Total Successful</th>
                            <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $stats['total_success']) }}
                            </td>
                            <th>Total Pending</th>
                            <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $stats['total_pending']) }}
                            </td>
                            <th>Total Cancelled</th>
                            <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $stats['total_cancelled']) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Count</th>
                            <td>{{ $stats['total_count'] }}</td>
                            <th>Count Successful</th>
                            <td>{{ $stats['count_success'] }}</td>
                            <th>Count Pending</th>
                            <td>{{ $stats['count_pending'] }}</td>
                            <th>Count Cancelled</th>
                            <td>{{ $stats['count_cancelled'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Customers</th>
                            <td>{{ $stats['total_customers'] }}</td>
                            <th>Total orders</th>
                            <td>{{ $stats['total_orders'] }}</td>
                            <th>Total items</th>
                            <td>{{ $stats['total_items'] }}</td>
                        </tr>
                    </table>
                @endif
                <hr>
                @if (isset($entries))

                    <div class="d-flex justify-content-between mb-3">
                        <h5>Transaction List</h5>
                        {{-- <div>
                            <button id="print-btn" class="btn btn-primary">Print</button>
                            <button id="pdf-btn" class="btn btn-danger">PDF</button>
                            <button id="csv-btn" class="btn btn-success">CSV</button>
                        </div> --}}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped transaction-table" id="transaction-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Products</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Amount</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    @php
                                        $product_ids = $entry->items->pluck('product_id')->toArray();
                                        $productNames = ProductModel::whereIn('product_id', $product_ids)
                                            ->pluck('product_name')
                                            ->toArray();

                                        $productQuantity = implode(', ', $entry->items->pluck('qty')->toArray());

                                    @endphp
                                    <tr>
                                        <td>{{ $entry->slip_serial_no }}</td>
                                        <td>{{ $entry->customer_name }}</td>
                                        <td>
                                            @foreach ($productNames as $productName)
                                                {{ $productName }}<br>
                                            @endforeach
                                        </td>
                                        <td>{{ $productQuantity }}</td>
                                        <td>{{ $entry->created_at }}</td>
                                        <td>
                                            @if ($entry->status == 'Done')
                                                <span class="badge bg-success">Done</span>
                                            @elseif ($entry->status == 'Pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $entry->payment_method }}</td>
                                        <td>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $entry->total_paid) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th class="text-center">Total</th>
                                    <th>{{ \App\MyHelpers::fromEuroView(auth()->user()->currency_id, $entries->sum('total_paid')) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <i>No records</i>
                @endif
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        document.getElementById('print-btn').addEventListener('click', function() {
            var printContents = document.getElementById('transaction-table').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;

            csvFile = new Blob([csv], {
                type: 'text/csv'
            });
            downloadLink = document.createElement('a');
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll('#transaction-table tr');

            for (var i = 0; i < rows.length; i++) {
                var row = [],
                    cols = rows[i].querySelectorAll('th, td');

                for (var j = 0; j < cols.length; j++) {
                    let cellContent = cols[j].innerText.replace(/\n/g, ', ');
                    row.push('"' + cellContent + '"'); // Enclose in quotes to handle commas and newlines
                }

                csv.push(row.join(','));
            }

            // Adding total row separately to ensure it aligns with the amount column
            let totalRow = document.querySelector('#transaction-table tfoot tr');
            if (totalRow) {
                var totalCols = totalRow.querySelectorAll('th');
                var total = [];

                for (var j = 0; j < totalCols.length; j++) {
                    let cellContent = totalCols[j].innerText.replace(/\n/g, ', ');
                    total.push('"' + cellContent + '"');
                }

            }

            downloadCSV(csv.join('\n'), filename);
        }

        document.getElementById('csv-btn').addEventListener('click', function() {
            if (document.querySelector('input[name="start_date"]').value && document.querySelector(
                    'input[name="end_date"]').value) {
                exportTableToCSV('SIOSTORE_POS.csv');
            }
        });

        document.getElementById('pdf-btn').addEventListener('click', function() {
            var printContents = document.getElementById('transaction-table').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });
    </script>

@endsection
