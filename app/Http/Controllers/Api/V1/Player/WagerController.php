<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SeamlessTransactionResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WagerController extends Controller
{
    use HttpResponses;
    // to do utc time
    public function index(Request $request)
    {
        $type = $request->get('type');

        [$from, $to] = match ($type) {
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            default => [now()->startOfDay(), now()],
        };

        $user = auth()->user();
        $combinedSubquery = DB::table('results')
            ->select(
                'user_id',
                DB::raw('MIN(created_at) as from_date'),
                DB::raw('MAX(created_at) as to_date'),
                DB::raw('COUNT(results.game_code) as total_count'),
                DB::raw('SUM(total_bet_amount) as total_bet_amount'),
                DB::raw('SUM(win_amount) as win_amount'),
                DB::raw('SUM(net_win) as net_win'),
                'products.provider_name'
            )
            ->join('game_lists', 'game_lists.game_id', '=', 'results.game_code')
            ->join('products', 'products.id', '=', 'game_lists.product_id')
            ->whereBetween('results.created_at', [$from, $to])
            ->groupBy('products.provider_name', 'user_id')
            ->unionAll(
                DB::table('bet_n_results')
                    ->select(
                        'user_id',
                        DB::raw('MIN(created_at) as from_date'),
                        DB::raw('MAX(created_at) as to_date'),
                        DB::raw('COUNT(bet_n_results.game_code) as total_count'),        
                        DB::raw('SUM(bet_amount) as total_bet_amount'),
                        DB::raw('SUM(win_amount) as win_amount'),
                        DB::raw('SUM(net_win) as net_win'),
                        'products.provider_name'
                    )
                    ->join('game_lists', 'game_lists.game_id', '=', 'bet_n_results.game_code')
                    ->join('products', 'products.id', '=', 'game_lists.product_id')
                    ->whereBetween('bet_n_results.created_at', [$from, $to])
                    ->groupBy('products.provider_name', 'user_id')
            );

        $transactions = DB::table('users as players')
            ->joinSub($combinedSubquery, 'combined', 'combined.user_id', '=', 'players.id')
            ->where('players.id', $user->id)
            ->orderBy('players.id', 'desc')
            ->get();

            return $this->success(SeamlessTransactionResource::collection($transactions));
    }
}