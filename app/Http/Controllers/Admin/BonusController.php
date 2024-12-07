<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\BonusType;
use App\Models\User;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index()
    {
        $data = Bonus::all();

        return view('admin.bonus.index', compact('data'));
    }

    public function create(Request $request)
    {
        $types = BonusType::all();

        return view('admin.bonus.create', compact('types'));
    }
    public function show($id) {
        
    }
    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}

    public function search(Request $request)
    {
        dd('here');
        $player = User::where('user_name', $request->user_name)->first();
         dd($player);
        if ($player) {
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $player->name,
                    'user_name' => $player->username,
                    'phone' => $player->phone,
                    'balance' => $player->balanceFloat,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Player not found',
        ]);
    }
}
