<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = Auth::user();
        return $auth->hasRole('Owner');
        $banners = Banner::latest()->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);
        // image
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('banner') . '.' . $ext; // Generate a unique filename
        $image->move(public_path('assets/img/banners/'), $filename); // Save the file
        Banner::create([
            'image' => $filename,
            'admin_id' => auth()->user()->id,
        ]);

        return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $checkOwnership = $banner->admin_id === auth()->user()->id;
        if ($checkOwnership) {
            return view('admin.banners.show', compact('banner'));
        } else {
            return redirect()->back()->with('error', 'You are not authorized to view this banner.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $checkOwnership = $banner->admin_id === auth()->user()->id;
        if ($checkOwnership) {
            return view('admin.banners.edit', compact('banner'));
        } else {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $request->validate([
            'image' => 'required',
        ]);
        $checkOwnership = $banner->admin_id === auth()->user()->id;
        if ($checkOwnership) {
            //remove banner from localstorage
            File::delete(public_path('assets/img/banners/' . $banner->image));

            // image
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $filename = uniqid('banner') . '.' . $ext; // Generate a unique filename
            $image->move(public_path('assets/img/banners/'), $filename); // Save the file

            $banner->update([
                'image' => $filename,
            ]);

            return redirect(route('admin.banners.index'))->with('success', 'Banner Image Updated.');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $checkOwnership = $banner->admin_id === auth()->user()->id;
        if ($checkOwnership) {
            //remove banner from localstorage
            File::delete(public_path('assets/img/banners/' . $banner->image));
            $banner->delete();
            return redirect()->back()->with('success', 'Banner Deleted.');
        } else {
            return redirect()->back()->with('error', 'You are not authorized to delete this banner.');
        }
    }
}
