<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BannerText;
use App\Traits\AuthorizedCheck;
use Illuminate\Http\Request;

class BannerTextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;
    public function index()
    {
        $auth = auth()->user();
        if($auth->hasPermission('master_access')){
            $texts = BannerText::query()->master()->latest()->get();
        }else if($auth->hasPermission('agent_access')){
            $texts = BannerText::query()->agent()->latest()->get();
        }else{
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }
        return view('admin.banner_text.index', compact('texts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();
        return view('admin.banner_text.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);
        BannerText::create([
            'text' => $request->text,
        ]);

        return redirect(route('admin.text.index'))->with('success', 'New Text Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerText $text)
    {
        return view('admin.banner_text.show', compact('text'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerText $text)
    {
        return view('admin.banner_text.edit', compact('text'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BannerText $text)
    {
        $request->validate([
            'text' => 'required',
        ]);
        $text->update([
            'text' => $request->text,
        ]);

        return redirect(route('admin.text.index'))->with('success', 'Marquee Text Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerText $text)
    {
        $text->delete();

        return redirect()->back()->with('success', 'Marquee Text Deleted Successfully.');
    }
}
