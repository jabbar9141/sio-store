<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\AdminInfoRequest;
use App\Models\User;
use App\MyHelpers;
use App\Notifications\VendorActivated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\city;
use App\Models\CityShippingCost;
use App\Models\Country;
use App\Models\product\ProductModel;
use App\Models\product\ProductOffersModel;
use App\Models\ShippingCost;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopOrderPayment;
use App\Models\VendorShop;
use Exception;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    private const PRODUCT_AVAILABLE_OFFERS = [
        'hot_deal',
        'featured_product',
        'special_offer',
        'special_deal'
    ];

    /**
     * Update the info of the admin
     * @param AdminInfoRequest $request
     */
    public function updateInfo(AdminInfoRequest $request)
    {
        // validation
        $data = $request->validated();

        // update info in db
        $userId = Auth::id();
        try {
            if (User::findOrFail($userId)->update($data))
                return response(['msg' => "Your Info is updated successfully"], 200);
        } catch (ModelNotFoundException $exception) {
            toastr()->error('Failed to save changes, try again.');
            return redirect()->route('admin-profile');
        }
    }

    public function userRemove(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $user = User::findOrFail($id);
                $vendor = $user->vendor_shop;
                if ($vendor) {
                    $my_request = new Request([
                        'id' => $vendor->vendor_id,
                    ]);
                    $this->vendorRemove($my_request);
                    // $products = $vendor->products;
                    // if (count($products) > 0) {
                    //     foreach ($products as $product) {
                    //         $product->offers()->delete();
                    //         $product->variations()->delete();
                    //         $product->images()->delete();
                    //         $product->reviews()->delete();

                    //         $product->delete();
                    //     }
                    // }
                    // $vendor->delete();
                }
                if ($user->photo) {
                    MyHelpers::deleteImageFromStorage($user->photo, 'uploads/images/profile/');
                }
                $user->cart()->delete();
                Address::where('user_id', $user->id)->delete();
                if ($user->delete())
                    return response()->json(
                        [
                            'msg' => "User deleted successfully",
                            'success' => true,
                        ]
                    );
                // // return redirect()->route('admin-vendor-list')->with('success', 'Successfully removed.');
                else
                    return response()->json([
                        'msg' => "Something went wrong",
                        'success' => false,
                    ]);
            });

            // return redirect('admin-vendor-list')->with('error', 'Failed to remove this user.');
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'msg' => "Something went wrong",
                'success' => false,
            ]);
            // return redirect('admin-vendor-list')->with('error', 'Failed to remove this user.');
        }
    }

    public function vendorRemove(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $vendor = VendorShop::find((int)$request->id);
                if ($vendor) {
                    $products = $vendor->products;
                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            $product->offers()->delete();
                            $product->variations()->delete();
                            $product->images()->delete();
                            $product->reviews()->delete();
                            $product->delete();
                        }
                    }
                    $vendor->delete();
                    return response()->json([
                        'msg' => "Vendor deleted successfully",
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'msg' => "Vendor Not Found",
                        'success' => false,
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => "Error",
                'success' => false,
            ]);
            return redirect()->back()->withErrors($th->getMessage());
        }
    }

    public function vendorActivate(Request $request)
    {
        $vendor_id = $request->vendor_id;

        // check whether activate or de-activate
        if ($request->current_status == "1") {
            return $this->vendorDeActivate($vendor_id);
        }

        try {
            $vendor = User::findOrFail($vendor_id);
            $vendor->update(['status' => 1]);

            // notify the vendor
            Notification::send($vendor, new VendorActivated());

            return response(['msg' => 'Vendor now is activated.'], 200);
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('admin-vendor-list')->with('error', 'Failed to activate this vendor, try again');
        }
    }
    public function vendorDeActivate(int $vendor_id)
    {

        try {
            User::findOrFail($vendor_id)->update(['status' => 0]);
            return response(['msg' => 'Vendor now is disabled.'], 200);
        } catch (ModelNotFoundException $exception) {
            return redirect()->route('admin-vendor-list')->with('error', 'Failed to activate this vendor, try again');
        }
    }

    public function userListPage()
    {
        return view('backend.admin.all_users');
    }

    public function userList()
    {
        $items = User::where('role', '!=', 'admin')->get();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('action', function ($item) {
                $str = '';
                $url = route('admin-activate-user', $item->id);
                if ($item->status != 1) {
                    $str .= "<a class = 'btn btn-sm btn-primary' href='$url' onclick = 'return confirm(\" Are You sure?\")'>Activate<a>";
                } else {
                    $str .= "<a class = 'btn btn-sm btn-danger' href='$url' onclick = 'return confirm(\" Are You sure?\")'>Deactivate<a>";
                }

                $str .= "<hr>";

                $url = route('admin-make-vendor', $item->id);
                if ($item->role != 'vendor') {
                    $str .= "<a class = 'btn btn-sm btn-primary' href='$url' onclick = 'return confirm(\" Are You sure?\")'>Make Vendor<a>";
                } else {
                    $str .= "<a class = 'btn btn-sm btn-danger' href='$url' onclick = 'return confirm(\" Are You sure?\")'>Remove as Vendor<a>";
                }

                $str .= "<hr>";
                $url = route('admin-user-remove', $item->id);
                $str .= "<a class = 'btn btn-sm btn-danger' href='javascript:void(0)' onclick = 'deleteUser(" . $item->id . ")'>Delete<a>";


                return $str;
            })



            ->addColumn('details', function ($item) {
                $str = '';
                if (!empty($item->photo)) {
                    $img_url = url('uploads/images/profile/' . $item->photo);
                } else {
                    $img_url = url('uploads/images/user_default_image.png');
                }
                $str .= '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#status_' . $item->id . '_Modal">
                        See Profile
                    </button>

                    <div class="modal fade" id="status_' . $item->id . '_Modal" tabindex="-1" aria-labelledby="status_' . $item->id . '_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">User details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class = "table table-bordered">
                                        <tr>
                                            <th>Name</th>
                                            <td>' . $item->name . '</td>
                                            <th>Photo</th>
                                            <td><img src="' . $img_url . '" height= "100px"></td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td>' . $item->username . '</td>
                                            <th>Phone</th>
                                            <td>' . $item->phone_number . '</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>' . $item->address . '</td>
                                            <th>Social Id</th>
                                            <td>' . $item->social_id . ' (' . $item->social_type . ') </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                  ';
                return $str;
            })

            ->addColumn('status', function ($item) {
                if ($item->status == 1) {
                    return "<span class='badge bg-success'>Active</span>";
                } else {
                    return "<span class='badge bg-secondary'>Inactive</span>";
                }
            })

            ->rawColumns(['status', 'details', 'action'])
            ->make(true);
    }

    public function userActivate($user_id)
    {
        try {
            $user = User::where('id', $user_id)->first();

            if ($user) {
                $user->status = !$user->status;
                $user->save();
                return back()->with('success', 'User ststus Updated');
            } else {
                return back()->with('error', 'User not found');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to ppdate user status.');
        }
    }

    public function productListPage()
    {
        return view('backend.admin.all_products');
    }

    public function productList()
    {
        $items = ProductModel::where('product_status', 1)
            ->orderBy('admin_approved', 'asc')
            ->get();
        $brands = BrandModel::all();
        $categories = CategoryModel::all();

        $brands_options = '';
        foreach ($brands as $item) {
            $brands_options .= '<option value="' . $item->brand_id . '">' . $item->brand_name . '</option>';
        }

        $categories_options = '';
        foreach ($categories as $item) {
            $categories_options .= '<option value="' . $item->category_id . '">' . $item->category_name . '</option>';
        }

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('action', function ($item) {
                $url = route('admin-activate-product', $item->product_id);
                return $item->admin_approved != 1
                    ? "<a class='btn btn-sm btn-primary' href='$url'>Approve</a>"
                    : "<a class='btn btn-sm btn-danger' href='$url'>Disapprove</a>";
            })
            ->editColumn('product_thumbnail', function ($item) {
                $img_url = $item->product_thumbnail
                    ? url('/uploads/images/product/' . $item->product_thumbnail)
                    : url('/uploads/images/user_default_image.png');
                return "<img src='$img_url' width='50px'>";
            })
            ->editColumn('edit', function ($item) {
                $url = route('vendor-product-edit', $item->product_id);
                return "<a class='btn btn-sm btn-primary' href='$url'>Edit</a>";
            })
            ->addColumn('details', function ($item) {
                return '<button type="button" class="btn btn-primary btn-sm btn-see-details" data-product-id="' . $item->product_id . '">
                            See Details
                        </button>';
            })
            ->editColumn('product_status', function ($item) {
                return $item->product_status == 1
                    ? "<span class='badge bg-success'>Published</span>"
                    : "<span class='badge bg-secondary'>Unpublished</span>";
            })
            ->editColumn('admin_approved', function ($item) {
                return $item->admin_approved == 1
                    ? "<span class='badge bg-success'>Approved</span>"
                    : "<span class='badge bg-secondary'>Unapproved</span>";
            })
            ->rawColumns(['action', 'product_thumbnail', 'details', 'product_status', 'admin_approved', 'edit'])
            ->make(true);
    }

    public function getProductDetails($id)
    {
        $product = ProductModel::with(['vendor', 'origin', 'category', 'brand', 'images', 'offers'])->find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $brands = BrandModel::all();
        $categories = CategoryModel::all();
        $brands_options = '';
        foreach ($brands as $item) {
            $brands_options .= '<option value="' . $item->brand_id . '">' . $item->brand_name . '</option>';
        }

        $categories_options = '';
        foreach ($categories as $item) {
            $categories_options .= '<option value="' . $item->category_id . '">' . $item->category_name . '</option>';
        }

        $image_markup = '<div class="row">';
        foreach ($product->images as $image) {
            $image_markup .= "<div class='col-sm-6'>
                <img src='/uploads/images/product/{$image->product_image}' style='width:80%; margin: 10px;'>
            </div>";
        }
        $image_markup .= "</div>";

        $words = explode(' ', $product->product_long_description);
        $chunks = array_chunk($words, 30);
        $product_long_description = '';
        foreach ($chunks as $chunk) {
            $product_long_description .= implode(' ', $chunk) . '<br>';
        }

        return view('backend.product.partials.product_details_admin', compact('product', 'brands_options', 'categories_options', 'image_markup', 'product_long_description'))->render();
    }

    public function productActivate($product_id)
    {
        try {
            $product = ProductModel::where('product_id', $product_id)->first();

            foreach ($product->variations as $key => $item) {
                if (is_null($item->whole_sale_price) || is_null($item->weight)) {
                    return back()->with('error', 'Enter Product Wholesale Price to Approve');
                }
            }

            if ($product) {
                $product->admin_approved = !$product->admin_approved;
                $product->save();
                return back()->with('success', 'product status Updated');
            } else {
                return back()->with('error', 'product not found');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to update product status.');
        }
    }

    public function modifyProduct(Request $request, $product_id)
    {
        $request->validate([
            'brand_id' => 'nullable',
            'category_id' => 'nullable'
        ]);
        try {
            $product = ProductModel::where('product_id', $product_id)->first();

            if ($product) {
                if ($request->brand_id && $request->brand_id != '') {
                    $product->brand_id = $request->brand_id;
                }

                if ($request->category_id && $request->category_id != '') {
                    $product->category_id = $request->category_id;
                }
                $product->save();

                $offers['offer_product_id'] = $product_id;
                foreach (self::PRODUCT_AVAILABLE_OFFERS as $offerName) {
                    $offers[$offerName] = ($request->get($offerName)) != null ? 1 : 0;
                }
                $e = ProductOffersModel::where('offer_product_id', $product_id)->count();
                if ($e > 0) {
                    ProductOffersModel::firstOrFail()
                        ->where('offer_product_id', $product_id)->update($offers);
                } else {
                    ProductOffersModel::insert($offers);
                }
                return back()->with('success', 'product details Updated');
            } else {
                return back()->with('error', 'product not found');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to update product details.');
        }
    }

    public function allOrders()
    {
        return view('backend.admin.all_orders');
    }

    public function allOrdersData()
    {
        $items = ShopOrderItem::get();

        // dd($items);

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('order_id', function ($item) {
                return ($item->order ? ($item->order->order_id) : 'N/A');
            })
            ->addColumn('customer', function ($item) {
                return ($item->order ? ($item->order->user->name . ' (' . $item->order->user->email . ')') : 'N/A');
            })
            ->addColumn('vendor', function ($item) {
                return (($item->item ? ($item->item->vendor->shop_name . ' (' . $item->item->vendor->user->name . ')') : 'N/A'));
            })
            ->addColumn('price', function ($item) {
                return (($item->item ? ($item->item->product_price) : 'N/A'));
            })
            ->addColumn('order_id', function ($item) {
                return (($item->item ? ($item->order->order_id) : 'N/A'));
            })
            ->addColumn('status', function ($item) {
                return $item->status;
            })
            ->addColumn('action', function ($item) {
                $url = route('admin-showAllOrders', $item->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function country()
    {
        return view('backend.admin.all_countries');
    }

    public function allCountriesData()
    {
        $items = Country::select('countries.id', 'countries.name', 'countries.iso2')
            ->leftJoin('shipping_costs', 'countries.iso2', '=', 'shipping_costs.country_iso_2')
            ->selectRaw('MIN(shipping_costs.cost) as min_cost, MAX(shipping_costs.cost) as max_cost')
            ->groupBy('countries.id')
            ->get();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->name ?? 'N/A');
            })
            ->addColumn('iso2', function ($item) {
                return (($item->iso2 ?? 'N/A'));
            })
            ->addColumn('shipping_cost', function ($item) {
                return ($item->min_cost ?? 0) . ' - ' . ($item->max_cost ?? 0);
            })            
            ->addColumn('action', function ($item) {
                $url = route('admin-country-details', $item->id);
                $weightUrl = route('admin-country-weight-details', $item->id);

                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye me-2"></i>City</a> 
                        <a href="' . $weightUrl . '" class="btn btn-primary btn-sm" ><i class="fa fa-eye me-1"></i> weight</a>';
            })
            ->rawColumns(['action', 'shipping_cost'])
            ->make(true);
    }

    public function countryDetails($id)
    {
        $country = Country::find($id);

        // dd($country->shippingCosts[0]->weight);

        $data = [
            'country' => $country,
            'cities' => $country->cities ?? [],
            // 'shippingCosts' => $country->shippingCost
        ];
        return view('backend/admin/country-details', $data);
    }

    public function countryWeightDetails($id)
    {
        $country = Country::find($id);
        $shippingCosts = ShippingCost::where('country_iso_2', $country->iso2)->get();
        $data = [
            'country' => $country,
            'shippingCosts' => $shippingCosts ?? [],
        ];
        return view('backend/admin/country-weight-details', $data);
    }

    public function cityList($country_id)
    {
        $items = city::where('country_id', $country_id)->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->name ?? 'N/A');
            })
            ->addColumn('shipping_percentage', function ($item) {
                $shippingCost = CityShippingCost::where('city_id', $item->id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->percentage) && $shippingCost->percentage > 0) {
                    return $shippingCost->percentage . ' %';
                }
                return 'N/A';
            })
            ->addColumn('shipping_cost', function ($item) {
                $shippingCost = CityShippingCost::where('city_id', $item->id)->where('country_id', $item->country_id)->first();
                if (isset($shippingCost->percentage) && $shippingCost->percentage > 0) {
                    $min = ShippingCost::where('country_iso_2', $item->country->iso2)->whereNotNull('cost')->orderBy('id', "asc")->first();
                    $max = ShippingCost::where('country_iso_2', $item->country->iso2)->whereNotNull('cost')->orderBy('id', 'desc')->first();

                    $min_cost = number_format(($shippingCost->percentage * $min->cost ?? 1) / 100, 2);
                    $max_cost = number_format(($shippingCost->percentage * $max->cost ?? 1) / 100, 2);
                    return $min_cost . ' - ' . $max_cost;
                }
                return 'N/A';
            })
            ->addColumn('weight', function ($item) {
                $min = ShippingCost::where('country_iso_2', $item->country->iso2)->whereNotNull('cost')->orderBy('id', "asc")->first();
                $max = ShippingCost::where('country_iso_2', $item->country->iso2)->whereNotNull('cost')->orderBy('id', 'desc')->first();
                return ($min->weight ?? 0) . ' - ' . ($max->weight ?? 0);
            })
            ->addColumn('action', function ($item) {

                return '<a href="javascript:void(0)" onclick="addShippingCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    public function weightList($country_id)
    {
        $country = Country::find($country_id);
        $items = ShippingCost::where('country_iso_2', $country->iso2)->get();

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('name', function ($item) {
                return ($item->country_name ?? 'N/A');
            })
            ->addColumn('cost', function ($item) {
                return ($item->cost ?? 'N/A');
            })
            ->addColumn('weight', function ($item) {
                return ($item->weight ?? 'N/A');
            })
            ->addColumn('action', function ($item) {

                return '<a href="javascript:void(0)" onclick="addWeightCost(' . $item->id . ')" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> Edit Cost</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    public function cityCost($city_id)
    {
        $city = city::find($city_id);
        if ($city) {
            $shippingCost = CityShippingCost::where('city_id', $city_id)->where('country_id', $city->country_id)->first();

            return response()->json([
                'success' => true,
                'city_name' => $shippingCost->city->name ?? null,
                'shipping_percentage' => $shippingCost->percentage ?? 0
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ]);
        }
    }

    public function weightCost($shipping_cost_id)
    {
        $shippingCost = ShippingCost::find($shipping_cost_id);
        if ($shippingCost) {
            $shippingCost = ShippingCost::where('id', $shipping_cost_id)->first();
            return response()->json([
                'success' => true,
                'shippingCost' => $shippingCost->cost ?? 0,
                'shippingId' => $shippingCost->id ?? 0

            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Shiping Address not found'
            ]);
        }
    }



    public function saveCost(Request $request, $id)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
            // 'cities' => 'required',
            // 'weight' => 'required|min:0',
        ]);
        return DB::transaction(function () use ($id, $request) {
            $country = Country::find($id);

            if ($request->has('multiple_cities')) {
                $city_ids = $request->cities;
                if (in_array('all', $city_ids)) {
                    $cities = City::where('country_id', $id)->pluck('id')->toArray();
                } else {
                    $cities = City::whereIn('id', $city_ids)->where('country_id', $id)->pluck('id')->toArray();
                }

                foreach ($cities as $city_id) {
                    $shippingCost = new CityShippingCost();
                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                    ]);
                }

                return back()->with(['success' => 'Data Saved Successfully !']);
            } else {
                $city_id = (int)$request->city_id;
                if ($request->city_id) {
                    $shippingCost = new CityShippingCost();

                    $shippingCost->updateOrCreate([
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                    ], [
                        'country_id' => $country->id,
                        'city_id' => $city_id,
                        'percentage' => $request->percentage,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Data Saved Successfully'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Something went wrong'
                    ]);
                    return redirect()->back()->withErrors('If Selecting individual cities, please unselect All Cities options');
                }
            }
        });
    }

    public function updateCost(Request $request)
    {

        try {
            $request->validate([
                'cost' => 'required|numeric|min:0',
            ]);
            ShippingCost::where('id', $request->shipping_id)->update([
                'cost' => $request->cost,
            ]);

            return back()->with(['success' => 'Cost updated Successfully !']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors('Something went wrong in updating cost');
        }
    }

    public function showAllOrders($order_item_id)
    {
        $it = ShopOrderItem::where('id', $order_item_id)->first();

        return view('backend.admin.admin_orders_show', ['item' => $it]);
    }

    public function allPayments(Request $request)
    {
        if ($request->start_date == null || $request->end_date == null) {
            return view('backend.admin.all_payments');
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $entries = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->get();

            $stats = [];

            $stats['total_amt'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->sum('amount');
            $stats['total_success'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Done')->sum('amount');
            $stats['total_pending'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Pending')->sum('amount');
            $stats['total_cancelled'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Cancelled')->sum('amount');

            $stats['total_count'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();
            $stats['count_success'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Done')->count();
            $stats['count_pending'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Pending')->count();
            $stats['count_cancelled'] = ShopOrderPayment::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->where('status', 'Cancelled')->count();


            $stats['total_customers'] = ShopOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->distinct('user_id')->count();
            $stats['total_orders'] = ShopOrder::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();
            $stats['total_items'] = ShopOrderItem::whereBetween('created_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"])->count();

            // dd($entries, $stats);
            return view('backend.admin.all_payments', ['entries' => $entries, 'stats' => $stats]);
        }
    }

    public function userMakeVendor(Request $request, $user_id)
    {
        try {
            $user = User::find($user_id);

            if ($user) {
                if ($user->role == 'user') {
                    $user->role = 'vendor';
                    $user->save();

                    $shop = VendorShop::where('user_id', $user_id)->count();

                    if ($shop == 0) {
                        $shop = new VendorShop;
                        $shop->user_id = $user_id;
                        $shop->shop_name = $user->name;
                        $shop->shop_description = "Store created by :" . $user->name;
                        $shop->save();
                    }
                } else {
                    $user->role = 'user';
                    $user->save();
                }

                return back()->with(['success' => 'User Vendor status modified']);
            } else {
                return back()->with(['error' => 'User Not found']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Failed to modify user vendor status, please try again later.');
        }
    }

    public function dealProducts(Request $req)
    {
        if ($req->isMethod('get')) {
            return view('backend.admin.today-deal-products');
        }
    }

    public function addDealProducts($product_id)
    {
        try {
            $product = ProductModel::where('product_id', $product_id)->first();

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            $newStatus = $product->today_deal == '0' ? '1' : '0';
            $product->update(['today_deal' => $newStatus]);

            $message = $newStatus == '1' ? 'Product added to Today\'s Deal successfully!' : 'Product removed from Today\'s Deal successfully!';
            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to update product status', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update product status.');
        }
    }

    public function updatePassword(Request $request)
    {
        // validation
        $rules = [
            'password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'different:password'],
            'confirm_password' => ['required', 'same:new_password']
        ];
        $data = $request->validate($rules);


        // updating the password
        User::find(Auth::id())->update([
            'password' => Hash::make($data['new_password'])
        ]);
        return response(['msg' => 'Updated Successfully'], 200);
    }
}
