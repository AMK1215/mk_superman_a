<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DepositResource;
use App\Http\Resources\Api\V1\SeamlessTransactionResource;
use App\Http\Resources\Api\V1\WithdrawResource;
use App\Http\Resources\TransactionResource;
use App\Models\DepositRequest;
use App\Models\SeamlessTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionName;
use App\Services\WalletService;

class TransactionController extends Controller
{
    use HttpResponses;

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

        $transactions = $user->transactions()->with(['seamlessTransaction.product:id,name'])->whereBetween('created_at', [$from, $to])
            ->orderBy('id', 'DESC')
            ->paginate();

        // return $this->success($transactions);
        return $this->success(TransactionResource::collection($transactions));
    }

    public function depositRequestLog()
    {
        $transactions = DepositRequest::with('user')->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success(DepositResource::collection($transactions));
    }

    public function withDrawRequestLog()
    {
        $transactions = WithDrawRequest::with('user')->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success(WithdrawResource::collection($transactions));
    }

    public function transactionDetails(Request $request)
    {
        // need to wait provider daily transaction detail api (currently not accept from provider api )
        try {
        // Validate the request input
        $request->validate([
            'balance' => 'required|numeric',
        ]);

        $user = \App\Models\User::where('user_name', 'SPM000363')->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $wallet = \Bavix\Wallet\Models\Wallet::where('holder_type', \App\Models\User::class)
            ->where('holder_id', $user->id)
            ->first();

        if (!$wallet) {
            return response()->json(['error' => 'Daily transaction not found for system.'], 404);
        }

        app(WalletService::class)->deposit($user, $request->balance, TransactionName::JackPot);

        return response()->json(['success' => 'Api provider Daily Transaction.'], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
    }

}