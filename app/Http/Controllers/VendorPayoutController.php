<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorPayout;
use App\MyHelpers;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;


class VendorPayoutController extends Controller
{
    public function vendorRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        try {
            $vendor = Auth::id();

            $r = new VendorPayout;
            $r->user_id = $vendor;
            $r->requested_amount = MyHelpers::toEuro(Auth::user()->currency_id, $request->amount);
            $r->save();
            return back()->with('success', 'Request submitted');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to place request, please try again later."]);
        }
    }

    public function vendorRequestUpdate(Request $request, $payout_id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'status' => 'required',
            'response_text' => 'nullable'
        ]);

        try {
            $r = VendorPayout::where('id', $payout_id)->first();
            $r->response_amount = MyHelpers::toEuro(Auth::user()->currency_id, $request->amount);
            $r->response_text = $request->response_text;
            $r->response_time = now();
            $r->status = $request->status;
            $r->save();

            Notification::send([User::find($r->user_id)], new GenericNotification('Payout request ' . $r->status, 'Hello', 'Your payout request has been updated and the new status is :' . $r->status));

            return back()->with('success', 'Request Updated');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with(['error' => "Failed to update request, please try again later."]);
        }
    }

    public function vendorRequests()
    {
        return view('backend.vendor.vendor_payout_requests');
    }

    public function vendorRequestsList()
    {
        $items = VendorPayout::where('user_id', Auth::id())->get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('request', function ($item) {
                $str = '';
                $str .= "<b>Date : </b>" . $item->created_at;
                $str .= "<br>";
                $str .= "<b>Amount : </b>" . MyHelpers::fromEuro(Auth::user()->currency_id, $item->requested_amount) . '(' . $item->currency . ')';
                $str .= "<br>";
                $str .= "<b>Request note : </b>" . $item->request_note;
                return $str;
            })

            ->addColumn('response', function ($item) {
                $str = '';
                $str .= "<b>Date : </b>" . $item->response_time;
                $str .= "<br>";
                $str .= "<b>Approved Amount : </b>" . MyHelpers::fromEuro(Auth::user()->currency_id, $item->response_amount) . '(' . $item->currency . ')';
                $str .= "<br>";
                $str .= "<b>Response note : </b>" . $item->response_text;
                return $str;
            })

            ->addColumn('status', function ($item) {
                return $item->status;
            })

            ->rawColumns(['status', 'response', 'request'])
            ->make(true);
    }

    public function vendorRequestsAdmin()
    {
        return view('backend.admin.vendor_payout_requests');
    }

    public function vendorRequestsAdminList()
    {
        $items = VendorPayout::get();
        return DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('request', function ($item) {
                $vendor_id = User::find($item->user_id)->vendor_shop->vendor_id;
                $vendor_stats = MyHelpers::vendorIncomeStats($vendor_id, $item->user_id);
                $str = '';
                $str .= "<b>Date : </b>" . $item->created_at;
                $str .= "<br>";
                $str .= "<b>Amount : </b>" . $item->requested_amount . '(' . $item->currency . ')';
                $str .= "<br>";
                $str .= "<b>Request note : </b>" . $item->request_note;
                $str .= "<hr>";
                $str .= "<b>Total Revenue : </b>" . $vendor_stats['total_revenue'] . '(' . $item->currency . ')';
                $str .= "<br>";
                $str .= "<b>Total Cash out : </b>" . $vendor_stats['total_cash_out'] . '(' . $item->currency . ')';
                $str .= "<br>";
                return $str;
            })

            ->addColumn('response', function ($item) {
                $str = '';
                $str .= "<b>Date : </b>" . $item->response_time;
                $str .= "<br>";
                $str .= "<b>Approved Amount : </b>" . $item->response_amount . '(' . $item->currency . ')';
                $str .= "<br>";
                $str .= "<b>Response note : </b>" . $item->response_text;
                return $str;
            })

            ->addColumn('status', function ($item) {

                $str = '';

                $str .= $item->status . '<br><br>';
                $url = route('payout-update', $item->id);

                $str .= '<button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#status_' . $item->id . '_Modal">
                        Change Status
                    </button>

                    <div class="modal fade" id="status_' . $item->id . '_Modal" tabindex="-1" aria-labelledby="status_' . $item->id . '_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Modify Payout Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="' . $url . '" method="post">
                                        ' . csrf_field() . '
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="status">Amount(' . $item->currency . ')</label>
                                            <input name="amount" required class= "form-control" type= "number" step = "any" min="0.1" value="' . $item->requested_amount . '" max="' . $item->requested_amount . '">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="Note">Note(Optional)</label>
                                            <textarea class="form-control" name="response_text" id="Note"></textarea>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Update</button>
                                    </form>

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

            ->rawColumns(['status', 'response', 'request'])
            ->make(true);
    }
}
