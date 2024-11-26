<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;


class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'agent_id' => $user->hasRole('Master') 
                ? ['required', Rule::exists('users', 'id')->whereIn('id', $user->agents()->pluck('id')->toArray())] 
                : null,
        ]);
        $agentId = $user->hasRole('Master') ? $request->agent_id : $user->id;
        if ($user->hasRole('Master') && $agentId != $user->agents()->first()->id) {
            return redirect()->back()->with('error', 'You are not authorized to add a banner for this agent.');
        }
        $image = $request->file('image');
        $filename = uniqid('banner_') . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/img/banners/'), $filename);
        Banner::create([
            'image' => $filename,
            'agent_id' => $agentId,
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
        $user = Auth::user();
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'agent_id' => $user->hasRole('Master') 
                ? ['required', Rule::exists('users', 'id')->whereIn('id', $user->agents()->pluck('id')->toArray())] 
                : null,
        ]);
        $agentId = $user->hasRole('Master') ? $request->agent_id : $user->id;
        File::delete(public_path('assets/img/banners/' . $banner->image));
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('banner') . '.' . $ext; // Generate a unique filename
        $image->move(public_path('assets/img/banners/'), $filename); // Save the file

        $banner->update([
            'image' => $filename,
            'agent_id' => $agentId,
        ]);

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
