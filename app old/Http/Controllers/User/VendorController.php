<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\VendorInfoRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rules\Password;

class VendorController extends Controller
{
    /**
     * To handle the request of updating info of a vendor
     * @param VendorInfoRequest $request
     */
    public function updateInfo(VendorInfoRequest $request)
    {
        // validation
        try {
            $data = $request->validated();

            // preparing some needed data
            $userId = Auth::id();
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'phone_number' => $data['phone_number'],
                'address' => $data['address'],
                'currency_id' => $request->currency_id,
            ];

            $shopData = [
                'shop_description' => $data['shop_description'],
                'shop_name' => $data['shop_name']
            ];

            $vendor_id = DB::table('vendor_shop')
                ->where('user_id', '=', $userId)
                ->get(['vendor_id'])[0];

            $this->updateUserData($userId, $userData);
            $this->updateShopData((int)$vendor_id->vendor_id, $shopData);

            return response(['msg' => "Your Info is updated successfully"], 200);
        } catch (\Throwable $th) {
            toastr()->error('Failed to save changes, try again.');
            return redirect()->route('vendor-profile');
        }
    }

    /**
     * @param int $userId
     * @param array $data
     * @return bool
     */
    private function updateUserData(int $userId, array $data): bool
    {
        return User::findOrFail($userId)->update($data);
    }

    /**
     * @param int $vendorId
     * @param array $data
     * @return bool
     */
    private function updateShopData(int $vendorId, array $data): bool
    {
        return DB::table('vendor_shop')->where('vendor_id', '=', $vendorId)->update($data);
    }

    /**
     * @param int $userId
     * To return the id of the current user's shop
     */
    public static function getVendorId(int $userId)
    {

        return DB::table('vendor_shop')->where('user_id', $userId)
            ->select('vendor_id')->value('vendor_id');
    }

    public function vendorOrders()
    {
        $items = ShopOrderItem::whereHas('item', function ($query) {
            $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
        })->get();

        $total_items = count($items);
        $total_value = 0;
        $total_profit = 0;
        $total_cost = 0;
        foreach ($items as $item) {
            $total_value = $total_value + ($item->price * $item->qty);
            $total_cost = ($total_cost + ($item->item->cost_price * $item->qty));
        }

        $total_profit = $total_value - $total_cost;


        return view('backend.vendor.vendor_orders')->with([
            'items_count' => $total_items,
            'total_income' => $total_value,
            'total_cost_price' => $total_cost,
            'total_profit' => round($total_profit, 2)
        ]);
    }

    public function vendorOrdersData()
    {
        $items = ShopOrderItem::whereHas('item', function ($query) {
            $query->where('vendor_id', Auth::user()->vendor_shop->vendor_id);
        })->get();

        // dd($items);

        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('order_id', function ($item) {
                $str =  $item->order->order_id;
                $str .= "<br>";
                $str .= "Item Name: " . $item->item->product_name;
                return $str;
            })
            ->addColumn('customer', function ($item) {
                return $item->order->user->name . '<br> (' . $item->order->user->email . ')';
            })
            ->addColumn('price', function ($item) {
                return MyHelpers::fromEuro(auth()->user()->currency_id,$item->price) . " x " . $item->qty .' = '. MyHelpers::fromEuroView(auth()->user()->currency_id,$item->price*$item->qty);
            })
            ->addColumn('status', function ($item) {
                return $item->status;
            })
            ->addColumn('action', function ($item) {
                $url = route('vendor-showVendorOrders', $item->id);
                return '<a href="' . $url . '" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> View</a>';
            })
            ->rawColumns(['action', 'order_id', 'customer', 'price'])
            ->make(true);
    }

    public function showVendorOrders($order_item_id)
    {
        $it = ShopOrderItem::where('id', $order_item_id)->first();

        return view('backend.vendor.vendor_orders_show', ['item' => $it]);
    }

    /**
     * To update the image of an authenticated user ( admin, vendor, or any user )
     * @param Request $request
     */
    public function updateImage(Request $request)
    {

        $image = $request->file('image');
        if ($image) {
            // validate the new image
            $allowedExtensions = 'gif,jpg,jpeg,png,svg,webp,ico';
            $data = $request->validate(
                [
                    'image' => ['nullable', 'image', 'mimes:' . $allowedExtensions, 'max:4096']
                ],
                [
                    'image.image' => 'The file must be an image.'
                ]
            );

            // upload it
            $data['photo'] = MyHelpers::uploadImage($image, 'uploads/images/profile');

            // dd($data['photo']);
            // update image in db
            try {
                $user = User::findOrFail(Auth::id())->update($data);
                if ($user) {

                    if (Auth::user()->photo) {
                        // remove the old image
                        MyHelpers::deleteImageFromStorage(Auth::user()->photo, 'uploads/images/profile/');
                    }
                    return response(['msg' => 'Your image is updated successfully'], 200);
                }
            } catch (ModelNotFoundException $exception) {
                toastr()->error('failed to update the new image');
                return redirect()->back();
            }
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
