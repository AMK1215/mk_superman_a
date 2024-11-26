<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;


class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ImageUpload;
    use AuthorizedCheck;
    public function index()
    {
        $auth = Auth::user();

        if ($auth->hasPermission("master_access")) {
            $banners = Banner::query()->master()->latest()->get();
        } elseif ($auth->hasPermission("agent_access")) {
            $banners = Banner::query()->agent()->latest()->get();
        } else {
            return redirect()->back()->with('error', 'You are not authorized to view this page.');
        }

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
        $user = Auth::user();
        $masterCheck = $user->hasRole('Master');
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'agent_id' => $masterCheck ? 'required|exists:users.id' : 'nullable',
        ]);
        $this->BannerPermission($request->agent_id);
        $filename = $this->handleImageUpload($request->image, "banners");
        Banner::create([
            'image' => $filename,
            'agent_id' => $masterCheck ? $request->agent_id : $user->id,
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
        $this->BannerPermission($banner->agent_id);
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->BannerPermission($banner->agent_id);
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $user = Auth::user();
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->BannerPermission($banner->agent_id);
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
        ]);
        $this->handleImageDelete($banner->image, "banners");
        $filename = $this->handleImageUpload($request->image, "banners");
        $banner->update(['image' => $filename]);
        return redirect(route('admin.banners.index'))->with('success', 'Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->BannerPermission($banner->agent_id);
        $this->handleImageDelete($banner->image, "banners");
        $banner->delete();
        return redirect()->back()->with('success', 'Banner Deleted.');
    }
}
