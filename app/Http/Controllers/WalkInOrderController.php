<?php

namespace App\Http\Controllers;

use App\Models\product\ProductModel;
use App\Models\ProductVariation;
use App\Models\ShopOrder;
use App\Models\WalkInOrder;
use App\Models\WalkInOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Yajra\DataTables\Facades\DataTables;

class WalkInOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        if (($request->start_date == null || $request->end_date == null) && $request->slip_serial_no == null) {
            return view('backend.vendor.all_walk_in_orders');
        } elseif ($request->slip_serial_no != null) {
            $searchInput = rtrim($request->slip_serial_no, '0');

            // Fetch entries with a slip_serial_no that partially matches the adjusted search input
            $entries = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) LIKE ?", ['%' . $searchInput . '%'])->get();

            $stats = [];
            $stats['total_amt'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->sum('total_paid') ?? 0;
            $stats['total_success'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Done')->sum('total_paid') ?? 0;
            $stats['total_pending'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Pending')->sum('total_paid') ?? 0;
            $stats['total_cancelled'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Cancelled')->sum('total_paid') ?? 0;

            $stats['total_count'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->count() ?? 0;
            $stats['count_success'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Done')->count() ?? 0;
            $stats['count_pending'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Pending')->count() ?? 0;
            $stats['count_cancelled'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->where('status', 'Cancelled')->count() ?? 0;

            $stats['total_customers'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->distinct('customer_name')->count('customer_name') ?? 0;
            $stats['total_orders'] = WalkInOrder::whereRaw("TRIM(TRAILING '0' FROM slip_serial_no) = ?", [$searchInput])->count() ?? 0;
            $stats['total_items'] = 0;

            if ($entries->isNotEmpty()) {
                $total = 0;

                foreach ($entries as $entry) {
                    $total += $entry->items->count() ?? 0;
                }

                $stats['total_items'] = $total;
            }




            return view('backend.vendor.all_walk_in_orders', ['entries' => $entries, 'stats' => $stats]);
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $entries = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->get();

            $stats = [];

            $stats['total_amt'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->sum('total_paid');
            $stats['total_success'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Done')->sum('total_paid');
            $stats['total_pending'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Pending')->sum('total_paid');
            $stats['total_cancelled'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Cancelled')->sum('total_paid');

            $stats['total_count'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();
            $stats['count_success'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Done')->count();
            $stats['count_pending'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Pending')->count();
            $stats['count_cancelled'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Cancelled')->count();


            $stats['total_customers'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->distinct('customer_name')->count();
            $stats['total_orders'] = WalkInOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();
            $stats['total_items'] = WalkInOrderItem::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();

            // dd($entries, $stats);
            return view('backend.vendor.all_walk_in_orders', ['entries' => $entries, 'stats' => $stats]);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = ShopOrder::whereHas('items', function ($query) {
            $query->whereHas('item', function ($query) {
                $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
            });
        })->get();
        $products = ProductModel::where('product_status', 1)->where('vendor_id', Auth::user()->vendor_shop->vendor_id)
            ->whereHas('variations', function ($query) {
                $query->where('product_quantity', '>', 0)->where('whole_sale_price', '>', 0);
            })
            ->orderBy('product_name', 'ASC')->paginate(20);


        return view('backend.vendor.walk-in-order')->with([
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = ProductModel::whereHas('variations', function ($query) {
            $query->where('product_quantity', '>', 0)->where('whole_sale_price', '>', 0);
        })->with(['images'])->where(function ($q) use ($query) {
            $q->where('product_name', 'LIKE', "%{$query}%")
                ->orWhere('product_code', 'LIKE', "%{$query}%");
        })
            ->where('vendor_id', Auth::user()->vendor_shop->vendor_id)
            ->where('product_status', 1)

            ->limit(20)
            ->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'vat_no' => 'required',
            'address' => 'required',
            'shipping_address' => 'required',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric',
            'products' => 'required|array',
            'products.*' => 'required|numeric',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric',
            'payment_method' => 'required',
            'total_paid' => 'required',

        ]);

        try {
            DB::beginTransaction();

            $order = new WalkInOrder;
            $order->customer_name = $request->customer_name;
            $order->vat_no = $request->vat_no;
            $order->address = $request->address;
            $order->shipping_address = $request->shipping_address;
            $order->order_id = UuidV4::uuid4();
            $order->payment_method = $request->payment_method;
            $order->total_paid = $request->total_paid;
            $order->status = 'Done';
            $order->vendor_id = Auth::user()->vendor_shop->vendor_id;

            $order->save();

            for ($i = 0; $i < count($request->products); $i++) {
                $it = new WalkInOrderItem;
                $it->product_id = $request->products[$i];
                $it->walk_in_order_id = $order->id;
                $it->qty = $request->qty[$i];
                $it->price = $request->prices[$i];
                $it->total_price = (float) $request->prices[$i] * (int) $request->qty[$i];
                $it->product_variation_id = $request->variations[$i];
                $it->status = 'Done';
                $it->save();

                $p = ProductModel::find($request->products[$i]);

                $p->product_quantity = ($p->product_quantity - $request->qty[$i]);
                $p->save();
                if ($p) {
                    $variation =  ProductVariation::where('id', $request->variations[$i])->where('product_id', $p->product_id)->first();
                    $variation->update([
                        'product_quantity' => (int) $variation->product_quantity - (int) $request->qty[$i]
                    ]);
                }
            }
            $totalPrice = WalkInOrderItem::where('walk_in_order_id', $order->id)->sum('total_price');
            $order->total_paid = $totalPrice;
            $order->slip_serial_no = 'SL_' . number_format((float)$totalPrice, 2, ".", "");
            $order->save();
            DB::commit();
            $r = WalkInOrder::with(['items', 'items.product'])->where('id', $order->id)->first();
            $view = view('backend.vendor.receipt-view', [
                'type' => 'receipt_view',
                'order' => $r,
                'vendor' => Auth::user()->vendor_shop,
                'user' => Auth::user()
            ])->render();

            $printView = view('backend.vendor.receipt-view', [
                'type' => 'print',
                'order' => $r,
                'vendor' => Auth::user()->vendor_shop,
                'user' => Auth::user()
            ])->render();

            $slip_view = view('backend.vendor.receipt-view', [
                'type' => 'slip_view',
                'order' => $r,
                'vendor' => Auth::user()->vendor_shop,
                'user' => Auth::user()
            ])->render();

            return response()->json([
                'view' => $view,
                'print_view' => $printView,
                'slip_view' => $slip_view,
                'order' => $r,
                'vendor' => Auth::user()->vendor_shop,
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), [$e]);
            return response()->json(['error', 'Failed to add walk-in order'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WalkInOrder  $walkInOrder
     * @return \Illuminate\Http\Response
     */
    public function show(WalkInOrder $walkInOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WalkInOrder  $walkInOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(WalkInOrder $walkInOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WalkInOrder  $walkInOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WalkInOrder $walkInOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WalkInOrder  $walkInOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(WalkInOrder $walkInOrder)
    {
        //
    }
}
