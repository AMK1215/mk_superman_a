<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPaymentRequest;
use App\Models\Admin\Bank;
use App\Models\PaymentType;
use App\Models\UserPayment;
use App\Traits\AuthorizedCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;
    public function index()
    {
        $auth = auth()->user();
        $this->MasterAgentRoleCheck();
        $banks = $auth->hasPermission('master_access') ? Bank::query()->master()->latest()->get() : Bank::query()->agent()->latest()->get();
        return $banks[0]->paymentType->img_url;
        return view('admin.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();
        $payment_types = PaymentType::all();
        return view('admin.banks.create', compact('payment_types'));
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
            'account_name' => 'required',
            'account_number' => 'required|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
            'agent_id' => $masterCheck ? 'required|exists:users,id' : 'nullable',
        ]);
        $agentId = $masterCheck ? $request->agent_id : $user->id;
        $this->FeaturePermission($agentId);
        Bank::create([
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'payment_type_id' => $request->payment_type_id,
            'agent_id' => $masterCheck ? $request->agent_id : $user->id,
        ]);
        return redirect(route('admin.banks.index'))->with('success', 'New userPayment Added.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $userPayment = UserPayment::where('id', $id)->where('user_id', Auth::id())->first();

        $paymentType = PaymentType::all();

        return view('admin.banks.edit', compact('userPayment', 'paymentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserPaymentRequest $request, $id)
    {

        $param = array_merge($request->validated());

        $userPayment = UserPayment::where('id', $id)->where('user_id', Auth::id())->first();

        $userPayment->update($param);

        return redirect(route('admin.banks.index'))->with('success', 'Bank Image Updated.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userPayment = UserPayment::where('id', $id)->where('user_id', Auth::id())->first();
        $userPayment->delete();

        return redirect()->back()->with('success', 'Payment Type Deleted.');
    }
}
