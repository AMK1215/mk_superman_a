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
            'agent_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where(function ($query) use ($user) {
                        if ($user->hasRole('Master')) {
                            // If Master, ensure agent_id is within their permissions
                            $query->whereIn('id', $user->agents()->pluck('id')->toArray());
                        } else {
                            // If Agent, ensure agent_id matches their own ID
                            $query->where('id', $user->id);
                        }
                    }),
            ],
        ]);
        $isAuthorized = $user->hasRole('Master') 
        ? in_array($request->agent_id, $user->agents()->pluck('id')->toArray()) 
        : $user->id;
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }else{
            $agentId = $user->hasRole('Master') ? $request->agent_id : $user->id;
            $image = $request->file('image');
            $filename = uniqid('banner_') . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/banners/'), $filename);
            Banner::create([
                'image' => $filename,
                'agent_id' => $agentId,
            ]);
            return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        if (!$banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $user = Auth::user();
        $isAuthorized = $user->hasRole('Master') 
            ? in_array($banner->agent_id, $user->agents()->pluck('id')->toArray()) 
            : $banner->agent_id === $user->id;
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        $user = Auth::user();
        $isAuthorized = $user->hasRole('Master') 
            ? in_array($banner->agent_id, $user->agents()->pluck('id')->toArray()) 
            : $banner->agent_id === $user->id;
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }
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
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
        ]);
        $isAuthorized = $user->hasRole('Master') 
            ? in_array($banner->agent_id, $user->agents()->pluck('id')->toArray()) 
            : $banner->agent_id === $user->id;
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }else{
            $agentId = $user->hasRole('Master') ? $request->agent_id : $user->id;
            File::delete(public_path('assets/img/banners/' . $banner->image));
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
        $user = Auth::user();
        $isAuthorized = $user->hasRole('Master') 
            ? in_array($banner->agent_id, $user->agents()->pluck('id')->toArray()) 
            : $banner->agent_id === $user->id;
        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to edit this banner.');
        }
        File::delete(public_path('assets/img/banners/' . $banner->image));
        $banner->delete();
        return redirect()->back()->with('success', 'Banner Deleted.');
    }
}
