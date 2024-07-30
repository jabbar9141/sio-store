<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\MyHelpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    // use ImageHandlerTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ann = Announcement::paginate(20);
        return view('backend.announcement.default', ['ann' => $ann]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.announcement.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // validate
        $data = $request->validate([
            'title' => 'required',
            'body' => 'nullable',
            'links_to' => 'url',
            'image' => 'nullable|file|mimes:png,jpg',
        ]);
        try {
            // handling the image

            $data['image'] = MyHelpers::uploadImage($request->file('image'), 'uploads/images/announcements');
            // insert
            if (Announcement::create($data))
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement Created Successfully',
                ]);
            else
                return back()->with('error', 'Failed to add this announcement, try again.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Something went wrong, try again....');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $announcement_id)
    {
        // validation
        $data = $request->validate([
            'title' => 'required',
            'body' => 'nullable',
            'links_to' => 'url',
            'image' => 'nullable|file|mimes:png,jpg',
        ]);
        try {

            // get the current item ( which being updated )
            try {
                $ann = Announcement::findOrFail($announcement_id);
            } catch (ModelNotFoundException $exception) {
                return back()->with('error', 'Something went wrong, try again.');
            }

            // handling if the request has an image
            $newImage = $request->file('image');
            if ($newImage) {
                $data['image'] = MyHelpers::uploadImage($request->file('image'), 'uploads/images/announcements');
                MyHelpers::deleteImageFromStorage($ann->brand_image, 'uploads/images/announcements/');
            }

            // update
            if ($ann->update($data))
                return back()->with('success', 'announcements is updated successfully.');
            else
                return back()->with('error', 'Something went wrong, try again.');
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [$e]);
            return back()->with('error', 'Something went wrong, try again....');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {    
        try {
            $brand = Announcement::findOrFail($id);
            MyHelpers::deleteImageFromStorage($brand->image, 'uploads/images/announcements/');
            if ($brand->delete())
                return back()->with('success', 'Successfully removed.');
            else
                return back()->with('error', 'Failed to remove this brand.');
        } catch (ModelNotFoundException $exception) {
            return back()->with('error', 'Failed to remove this brand.');
        }
    }
}
