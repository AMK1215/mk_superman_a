<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\GameProviderResource;
use App\Models\Admin\GameType;
use App\Models\Admin\GameTypeProduct;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = Product::with('gameTypes')->get();
        $gameTypes = GameType::with('products')->get();
        $providers = [];
        foreach ($gameTypes as $gameType) {
            foreach ($gameType->products as $product) {
                $providers[] = $product;
                $providers[$product->id]['game_type'] = $gameType->name;
            }
        }
        // return $providers;
        return GameProviderResource::collection($providers);

        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gameTypes = GameType::all();

        return view('admin.product.create', compact('gameTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('game_type').'.'.$ext;
        $image->move(public_path('assets/img/game_logo/'), $filename);

        $product = Product::create([
            'name' => $request->name,
            'code' => $request->code,
            'order' => $request->order,
        ]);

        $product->gameTypes()->attach($request->game_type_id, [
            'image' => $filename, 'rate' => $request->rate]);

        return redirect()->back()->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::find($id);

        return view('admin.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);

        $product->update([
            'name' => $request->name,
            'code' => $request->code,
            'order' => $request->order,
        ]);

        return redirect()->back()->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully');

    }
}
