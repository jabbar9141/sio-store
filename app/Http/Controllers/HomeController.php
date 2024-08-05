<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\product\ProductModel;
use App\Models\product\ProductOffersModel;
use App\Models\ProductReview;
use App\Models\ShopOrder;
use App\Models\GetInTouch;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderPayment;
use App\Models\VendorPayout;
use App\Models\VendorShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Review;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactUsMail;
use App\Mail\ContactWithVendorMail;
use App\Models\Currency;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\WalkInOrder;
use App\Models\WalkInOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currency_id =  session('currency_id', Currency::where('status', true)->first()?->id ?? 0);
        $categories = CategoryModel::orderBy('category_id', 'asc')->inRandomOrder()->limit(30)->get();
        $announcements = Announcement::latest()->get();
        $brands = BrandModel::orderBy('brand_id', 'asc')->inRandomOrder()->limit(30)->get();
        $featured = ProductOffersModel::with(['products', 'products.images'])
            ->whereHas('products', function ($query) {
                $query->where('admin_approved', 1)->where('product_status', 1)->whereHas('variations', function ($q) {
                    $q->where('product_quantity', '>', 0);
                });
            })
            ->orderBy('offer_id', 'DESC')
            ->limit(20)
            ->get();

        // Ensuring recent products are approved and active
        $recent = ProductModel::where('product_status', 1)
            ->whereHas('variations', function ($q) {
                $q->where('product_quantity', '>', 0);
            })
            ->where('admin_approved', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // Ensuring today's deals are approved and active
        $today_deals = ProductModel::where('product_status', 1)
            ->where('admin_approved', 1)
            ->whereHas('variations', function ($q) {
                $q->where('product_quantity', '>', 0);
            })
            ->where('today_deal', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        $vendors = VendorShop::whereHas('user', function ($query) {
            $query->where('status', 1);
            $query->where('role', 'vendor');
        })->inRandomOrder()->limit(20)->get();
        return view('user.home', compact('categories', 'featured', 'recent', 'vendors', 'brands', 'today_deals', 'announcements', 'currency_id'));
    }

    public function about()
    {
        return view('user.about');
    }

    public function contact()
    {
        return view('user.contact');
    }

    public function help()
    {
        return view('user.help');
    }

    public function faq()
    {
        return view('user.faq');
    }

    public function dashboard()
    {
        if (Auth::user()) {
            if (Auth::user()->role == 'vendor') {
                $total_orders = ShopOrderItem::whereHas('item', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->pluck('order_id')->unique()->count();

                $total_orders += WalkInOrderItem::whereHas('product', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->pluck('walk_in_order_id')->unique()->count();

                $orders_list = ShopOrderItem::whereHas('item', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->limit(10)->get();

                $users_total = ShopOrder::whereHas('items', function ($query) {
                    $query->whereHas('item', function ($query) {
                        $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                    });
                })->pluck('user_id')->unique()->count();

                $users_total += WalkInOrder::whereHas('items', function ($query) {
                    $query->whereHas('product', function ($query) {
                        $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                    });
                })->count();

                $total_revenue = ShopOrderItem::whereHas('item', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->sum('total_price');

                $total_shop_revenue = $total_revenue;

                $total_revenue += WalkInOrderItem::whereHas('product', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->sum('total_price');

                $today = Carbon::today();

                // Get order IDs for non-cancelled, non-refunded, and non-failed orders created today
                $orderIds = ShopOrder::whereNotIn('status', ['Cancelled', 'Refunded', 'Failed'])
                    ->whereDate('created_at', $today)
                    ->pluck('id')
                    ->toArray();

                // Get items for today's orders and matching vendor
                $items = ShopOrderItem::whereHas('item', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id)->where('admin_approved', true)->where('product_status', true);
                })
                    ->whereIn('order_id', $orderIds)
                    ->get();

                // Get order IDs for non-cancelled walk-in orders created today
                $orderWalkIds = WalkInOrder::where('status', '!=', 'Cancelled')
                    ->whereDate('created_at', $today)
                    ->pluck('id')
                    ->toArray();

                // Get items for today's walk-in orders and matching vendor
                $walkin_items = WalkInOrderItem::whereHas('product', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id)->where('admin_approved', true)->where('product_status', true);
                })
                    ->whereIn('walk_in_order_id', $orderWalkIds)
                    ->get();

                // Initialize revenue and cost price
                $total_revenue_daily = 0;
                $total_cost_price_daily = 0;

                // Calculate revenue and cost price for shop orders
                foreach ($items as $item) {
                    $total_revenue_daily += $item->total_price;
                    $total_cost_price_daily += ($item->variation->whole_sale_price ?? 0) * $item->qty;
                }

                // Calculate revenue and cost price for walk-in orders
                foreach ($walkin_items as $item) {
                    $total_revenue_daily += $item->total_price;
                    $total_cost_price_daily += ($item->productVariation->whole_sale_price ?? 0) * $item->qty;
                }

                // Calculate daily profit
                $total_daily_profit = $total_revenue_daily - $total_cost_price_daily;

                $product_ids = ProductModel::where('admin_approved', true)->where('product_status', true)->where('vendor_id', Auth::user()->vendor_shop->vendor_id)->pluck('product_id')->toArray(); //->sum('product_price');
                $total_cost_price_inventory = ProductModel::whereIn('product_id', $product_ids)->sum('total_variation_whole_sale_price');
                $total_revenue_inventory = ProductModel::whereIn('product_id', $product_ids)->sum('total_variation_price');

                // Calculate the sum of quantity * whole_sale_price
                $total_profit_inventory = $total_revenue_inventory - $total_cost_price_inventory;

                $latest_reviews = ProductReview::whereHas('product', function ($query) {
                    $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
                })->whereDate('created_at', Carbon::today())->orderBy('created_at')->paginate(2);

                $cash_out = VendorPayout::where('user_id', Auth::id())->where('status', 'Approved')->sum('response_amount');
                $cash_out_pending = VendorPayout::where('user_id', Auth::id())->where('status', 'Pending')->sum('requested_amount');

                return view('backend.profile.vendor_dashboard', [
                    'total_orders' => $total_orders,
                    'total_revenue' => $total_shop_revenue,
                    'latest_reviews' => $latest_reviews,
                    'users_total' => $users_total,
                    'cash_out' => $cash_out,
                    'cash_out_pending' => $cash_out_pending,
                    'orders_list' => $orders_list,
                    'total_sales_pos_online' => $total_revenue,
                    'total_daily_profit' => $total_daily_profit,
                    'total_inventory_cost' => $total_cost_price_inventory,
                    'total_inventory_profit' => $total_profit_inventory,
                    'total_shop_revenue' => $total_shop_revenue
                ]);
            } elseif (Auth::user()->role == 'user') {
                $orders_total = ShopOrder::where('user_id', Auth::id())->pluck('order_id')->unique()->count();

                $users_total = ShopOrderItem::whereHas('order', function ($query) {
                    $query->where('user_id', Auth::id());
                })->pluck('id')->unique()->count();

                $total_revenue = ShopOrderItem::whereHas('order', function ($query) {
                    $query->where('user_id', Auth::id());
                })->sum('total_price');

                $total_revenue += ShopOrder::where('user_id', Auth::id())->sum('shipping_cost');

                $latest_reviews = ProductReview::where('user_id', Auth::id())->orderBy('created_at')->paginate(2);

                return view('backend.profile.user_dashboard', [
                    'total_orders' => $orders_total,
                    'total_revenue' => $total_revenue,
                    'latest_reviews' => $latest_reviews,
                    'users_total' => $users_total,
                ]);
            } elseif (Auth::user()->role == 'admin') {
                $total_orders = ShopOrderItem::pluck('order_id')->unique()->count();
                $total_orders += WalkInOrderItem::pluck('walk_in_order_id')->unique()->count();
                $orders_list = ShopOrderItem::limit(10)->get();
                $users_total = ShopOrder::pluck('user_id')->unique()->count();
                $users_total += WalkInOrder::count();
                $total_revenue = ShopOrderItem::sum('price');
                $total_shop_revenue = $total_revenue;
                $total_revenue += WalkInOrderItem::sum('total_price');
                $today = Carbon::today();
                // Get order IDs for non-cancelled, non-refunded, and non-failed orders created today
                $orderIds = ShopOrder::whereNotIn('status', ['Cancelled', 'Refunded', 'Failed'])
                    ->whereDate('created_at', $today)
                    ->pluck('id')
                    ->toArray();

                // Get items for today's orders and matching vendor
                $items = ShopOrderItem::whereHas('item', function ($query) {
                    $query->where('admin_approved', true)->where('product_status', true);
                })
                    ->whereIn('order_id', $orderIds)
                    ->get();

                // Get order IDs for non-cancelled walk-in orders created today
                $orderWalkIds = WalkInOrder::where('status', '!=', 'Cancelled')
                    ->whereDate('created_at', $today)
                    ->pluck('id')
                    ->toArray();

                // Get items for today's walk-in orders and matching vendor
                $walkin_items = WalkInOrderItem::whereHas('product', function ($query) {
                    $query->where('admin_approved', true)->where('product_status', true);
                })
                    ->whereIn('walk_in_order_id', $orderWalkIds)
                    ->get();

                // Initialize revenue and cost price
                $total_revenue_daily = 0;
                $total_cost_price_daily = 0;

                // Calculate revenue and cost price for shop orders
                foreach ($items as $item) {
                    $total_revenue_daily += $item->total_price;
                    $total_cost_price_daily += ($item->variation->whole_sale_price ?? 0) * $item->qty;
                }

                // Calculate revenue and cost price for walk-in orders
                foreach ($walkin_items as $item) {
                    $total_revenue_daily += $item->total_price;
                    $total_cost_price_daily += ($item->productVariation->whole_sale_price ?? 0) * $item->qty;
                }

                // Calculate daily profit
                $total_daily_profit = $total_revenue_daily - $total_cost_price_daily;

                $product_ids = ProductModel::where('admin_approved', true)->where('product_status', true)->pluck('product_id')->toArray(); //->sum('product_price');
                $total_cost_price_inventory = ProductModel::whereIn('product_id', $product_ids)->sum('total_variation_whole_sale_price');
                $total_revenue_inventory = ProductModel::whereIn('product_id', $product_ids)->sum('total_variation_price');

                // Calculate the sum of quantity * whole_sale_price
                $total_profit_inventory = $total_revenue_inventory - $total_cost_price_inventory;

                $latest_reviews = ProductReview::whereDate('created_at', Carbon::today())->orderBy('created_at')->paginate(2);

                $cash_out = VendorPayout::where('user_id', Auth::id())->where('status', 'Approved')->sum('response_amount');
                $cash_out_pending = VendorPayout::where('user_id', Auth::id())->where('status', 'Pending')->sum('requested_amount');

                return view('backend.profile.admin_dashboard', [
                    'total_orders' => $total_orders,
                    'total_revenue' => $total_shop_revenue,
                    'latest_reviews' => $latest_reviews,
                    'users_total' => $users_total,
                    'cash_out' => $cash_out,
                    'cash_out_pending' => $cash_out_pending,
                    'orders_list' => $orders_list,
                    'total_sales_pos_online' => $total_revenue,
                    'total_daily_profit' => $total_daily_profit,
                    'total_inventory_cost' => $total_cost_price_inventory,
                    'total_inventory_profit' => $total_profit_inventory,
                    'total_shop_revenue' => $total_shop_revenue
                ]);
            }
        } else {
            redirect()->route('login');
        }
    }

    // post currency
    public function postCurrency(Request $request, $currency)
    {
        session()->put('session_currency', $currency);
        if ($currency == 'USD') {
            session()->put('session_symbol', '$');
        } elseif ($currency == 'EUR') {
            session()->put('session_symbol', '€');
        } elseif ($currency == 'NGN') {
            session()->put('session_symbol', '₦');
        } elseif ($currency == 'GHS') {
            session()->put('session_symbol', '₵');
        } elseif ($currency == 'GBP') {
            session()->put('session_symbol', '£');
        } elseif ($currency == 'XAF') {
            session()->put('session_symbol', 'XAF');
        }
        echo 1;
    }

    public function countryList(Request $request)
    {
    }

    public function getInTouch(Request $request)
    {


        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'message' => 'required',
        ]);

        $touch = new GetInTouch();
        $touch->first_name = $request->first_name;
        $touch->last_name = $request->last_name;
        $touch->email = $request->last_name;
        $touch->phone_number = $request->phone_number;
        $touch->message = $request->message;
        $touch->save();
        Mail::to('support@siostore.eu')->send(new ContactUsMail($touch));
        return back()->with('success', 'Successfully Submitted');
    }


    public function subscriber(Request $req)
    {
        // dd($req->all());

        try {
            $req->validate([
                'email' => 'required|email',
            ]);

            FacadesDB::table('subscribers')->insert([
                'email' => $req->email
            ]);
            Log::info('Subscriber added successfully', ['email' => $req->email]);


            return back()->with('success', 'Subscriber Add Successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Warning : ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Warning : ' . $e->getMessage());
        }
    }

    public function contactWithVendor(Request $request)
    {
        $product = ProductModel::find($request->product_id);
        $vendor = VendorShop::find($product->vendor_id);
        $user = User::find($vendor->user_id);
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'message' => 'required',
        ]);

        $touch = new GetInTouch();
        $touch->first_name = $request->first_name;
        $touch->last_name = $request->last_name;
        $touch->email = $request->email;
        $touch->phone_number = $request->phone_number;
        $touch->message = $request->message;
        $touch->type = 'contant_with_vendor';
        $touch->save();
        Mail::to($user->email)->send(new ContactWithVendorMail($touch));

        return response()->json([
            'success' => true,
            'msg' => 'Contact with vendor mail was sent successfully',
        ]);
    }
}
