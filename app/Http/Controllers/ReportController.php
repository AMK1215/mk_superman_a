<?php

namespace App\Http\Controllers;

use App\Models\Admin\Product;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private $carbon;

    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;
    }

    public function index(Request $request)
    {
        $adminId = auth()->id();

        $results = $this->buildQuery($request, $adminId)->get();

        return view('report.index', compact('results'));
    }

    public function detail(Request $request, $playerId)
    {
        $details = $this->getPlayerDetails($playerId, $request);

        $productTypes = Product::where('is_active', 1)->get();

        return view('report.detail', compact('details', 'productTypes', 'playerId'));
    }

    private function buildQuery(Request $request, $adminId)
    {
        $transactionSubquery = DB::table('transactions')
        ->select(
            'payable_id',
            DB::raw("
                SUM(CASE WHEN name = 'bonus_local' and type = 'deposit' THEN amount ELSE 0 END) as total_bonus
            "),
            DB::raw("
                SUM(CASE WHEN name = 'debit_transfer' and type = 'withdraw' THEN amount ELSE 0 END) as total_withdraw
            "),
            DB::raw("
                SUM(CASE WHEN name = 'credit_transfer' and type = 'deposit' THEN amount ELSE 0 END) as total_deposit
            ")
        )
        ->groupBy('payable_id');

        $query = Result::select(
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.win_amount) as total_win_amount'),
            DB::raw('SUM(results.net_win) as total_net_win'),
            DB::raw('MAX(wallets.balance) as balance'),
            'transaction_totals.total_bonus',
            'transaction_totals.total_withdraw',
            'transaction_totals.total_deposit',
            'players.name as player_name',
            'players.user_name as user_name',
            'agents.name as agent_name',
            'players.id as user_id'
        )
            ->join('users as players', 'results.user_id', '=', 'players.id')
            ->join('users as agents', 'players.agent_id', '=', 'agents.id')
            ->join('wallets', 'wallets.holder_id', '=', 'players.id')
            ->leftJoinSub($transactionSubquery, 'transaction_totals', 'transaction_totals.payable_id', '=', 'results.user_id')
            ->when($request->player_id, fn($query) => $query->where('results.user_id', $request->player_id));
        $this->applyDateFilter($query, $request);
        $this->applyRoleFilter($query, $adminId);

        return $query->groupBy('players.name', 'agents.name', 'players.id', 'players.user_name');
    }

    private function applyDateFilter($query, Request $request)
    {
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('results.created_at', [
                Carbon::parse($request->start_date)->toDateTimeString(),
                Carbon::parse($request->end_date)->toDateTimeString(),
            ]);
        } else {
            $query->whereBetween('results.created_at', [
                $this->carbon->startOfMonth()->toDateTimeString(),
                $this->carbon->endOfMonth()->toDateTimeString(),
            ]);
        }
    }

    private function applyRoleFilter($query, $adminId)
    {
        if (Auth::user()->hasRole('Master')) {
            $query->where('agents.agent_id', $adminId);
        } elseif (Auth::user()->hasRole('Agent')) {
            $query->where('agents.id', $adminId);
        }
    }

    private function getPlayerDetails($playerId, $request)
    {
        $query = Result::where('user_id', $playerId)
            ->when($request->product_type_id, function ($query) use ($request) {
                $query->where('game_provide_name', $request->product_type_id);
            });

        $this->applyDateFilter($query, $request);

        return $query->orderBy('id', 'desc')->get();

    }

    private function getSubquery($table, $condition = '1=1')
    {
        return DB::raw("(SELECT payable_id, name , amount FROM $table WHERE $condition GROUP BY payable_id) AS $table");
    }
}
