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
    use AuthorizedCheck;

    /**
     * Display a listing of the resource.
     */
    use ImageUpload;

    public function index()
    {
        $auth = Auth::user();
        $this->MasterAgentRoleCheck();
        $banners = $auth->hasPermission('master_access') ? Banner::query()->master()->latest()->get() : Banner::query()->agent()->latest()->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');
    
        // Validate the request
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === "single") ? 'required|exists:users,id' : 'nullable',
        ]);
    
        $type = $request->type ?? "single";
        $filename = $this->handleImageUpload($request->image, 'banners');
    
        if ($type === "single") {
            $agentId = $isMaster ? $request->agent_id : $user->id;
            $this->FeaturePermission($agentId);
    
            Banner::create([
                'image' => $filename,
                'agent_id' => $agentId,
            ]);
        } elseif ($type === "all") {
            foreach ($user->agents as $agent) {
                Banner::create([
                    'image' => $filename,
                    'agent_id' => $agent->id,
                ]);
            }
        }
    
        return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $this->MasterAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);

        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        $this->MasterAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);

        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $this->MasterAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
        ]);
        $this->handleImageDelete($banner->image, 'banners');
        $filename = $this->handleImageUpload($request->image, 'banners');
        $banner->update(['image' => $filename]);

        return redirect(route('admin.banners.index'))->with('success', 'Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $this->MasterAgentRoleCheck();
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($banner->agent_id);
        $this->handleImageDelete($banner->image, 'banners');
        $banner->delete();

        return redirect()->back()->with('success', 'Banner Deleted.');
    }
}
