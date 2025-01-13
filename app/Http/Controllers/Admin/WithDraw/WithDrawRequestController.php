<?php

namespace App\Http\Controllers\Admin\WithDraw;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithDrawRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $agentIds = [$user->id];
        $agents = [];
        if ($user->hasRole('Master')) {
            $agentIds = $this->getAgentIds($request, $user);
            $agents = $user->children()->get();
        }
        $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d H:m:i');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d H:m:i');

        $withdraws = WithDrawRequest::with('paymentType')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [
                    $startDate,
                    $endDate,
                ]);
            })
            ->when($request->player_id, function ($query) use ($request) {
                $query->whereHas('user', function ($subQuery) use ($request) {
                    $subQuery->where('user_name', $request->player_id);
                });
            })
            ->when($request->agent_id, function ($query) use ($request) {
               $query->where('agent_id', $request->agent_id);
            })
            ->when($request->payment_type_id, function ($query) use ($request) {
                $query->where('payment_type_id', $request->payment_type_id);
             })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->whereIn('agent_id', $agentIds)
            ->latest()
            ->get();
        
        $paymentTypes = PaymentType::all();

        return view('admin.withdraw_request.index', compact('withdraws', 'paymentTypes', 'agents'));
    }

    public function statusChangeIndex(Request $request, WithDrawRequest $withdraw)
    {
        $request->validate([
            'status' => 'required|in:0,1',
            'amount' => 'required|numeric|min:0',
            'player' => 'required|exists:users,id',
        ]);

        try {
            $agent = Auth::user();
            $player = User::find($request->player);

            if ($agent->hasRole('Master')) {
                $agent = User::where('id', $player->agent_id)->first();
            }

            if ($request->status == 1 && $player->balanceFloat < $request->amount) {

                return redirect()->back()->with('error', 'Insufficient Balance!');
            }

            $withdraw->update([
                'status' => $request->status,
            ]);

            if ($request->status == 1) {
                app(WalletService::class)->transfer($player, $agent, $request->amount, TransactionName::DebitTransfer, ['agent_id' => Auth::id()]);
            }

            return redirect()->back()->with('success', 'Withdraw status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusChangeReject(Request $request, WithDrawRequest $withdraw)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            $withdraw->update([
                'status' => $request->status,
            ]);

            return redirect()->back()->with('success', 'Withdraw status updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function getAgentIds($request, $user)
    {
        if ($request->agent_id) {
            return User::where('id', $request->agent_id)->pluck('id')->toArray();
        }

        return User::where('agent_id', $user->id)->pluck('id')->toArray();
    }
}
