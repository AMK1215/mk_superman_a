<?php

namespace App\Http\Controllers\Admin\BannerAds;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerAds;
use App\Traits\AuthorizedCheck;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerAdsController extends Controller
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
        $banners = $auth->hasPermission('master_access') ? BannerAds::query()->master()->latest()->get() : BannerAds::query()->agent()->latest()->get();

        return view('admin.banner_ads.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();

        return view('admin.banner_ads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $masterCheck = $user->hasRole('Master');
        $request->validate([
            'image' => 'required|image|max:2048', // Ensure it's an image with a size limit
            'agent_id' => $masterCheck ? 'required|exists:users,id' : 'nullable',
        ]);
        $agentId = $masterCheck ? $request->agent_id : $user->id;
        $this->FeaturePermission($agentId);
        $filename = $this->handleImageUpload($request->image, 'banners_ads');
        BannerAds::create([
            'image' => $filename,
            'agent_id' => $masterCheck ? $request->agent_id : $user->id,
        ]);

        return redirect(route('admin.adsbanners.index'))->with('success', 'New Ads Banner Image Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);

        return view('admin.banner_ads.show', compact('adsbanner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);

        return view('admin.banner_ads.edit', compact('adsbanner'));
    }

    public function update(Request $request, BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);
        $this->handleImageDelete($adsbanner->image, 'banners');
        $filename = $this->handleImageUpload($request->image, 'banners_ads');
        $adsbanner->update(['image' => $filename]);

        return redirect(route('admin.adsbanners.index'))->with('success', 'Ads Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerAds $adsbanner)
    {
        $this->MasterAgentRoleCheck();
        if (! $adsbanner) {
            return redirect()->back()->with('error', 'Ads Banner Not Found');
        }
        $this->FeaturePermission($adsbanner->agent_id);
        $this->handleImageDelete($adsbanner->image, 'banners_ads');
        $adsbanner->delete();

        return redirect()->back()->with('success', 'Ads Banner Deleted.');
    }
}
