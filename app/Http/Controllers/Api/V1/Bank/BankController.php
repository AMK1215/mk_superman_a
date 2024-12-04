<?php

namespace App\Http\Controllers\Api\V1\Bank;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BankRequest;
use App\Http\Resources\Api\V1\BankResource;
use App\Models\Admin\Bank;
use App\Models\UserBank;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    use HttpResponses;

    public function banks()
    {
        $banks = Bank::agentPlayer()->get();
        return $this->success(BankResource::collection($banks), 'Banks retrieved successfully');
    }
}
