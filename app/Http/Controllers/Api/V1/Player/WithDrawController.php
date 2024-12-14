<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawRequest;
use App\Http\Resources\Api\V1\WithdrawResource;
use App\Models\WithDrawRequest as ModelsWithDrawRequest;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Support\Facades\Auth;

class WithDrawController extends Controller
{
    use HttpResponses;

    public function withdraw(WithdrawRequest $request)
    {
        $inputs = $request->validated();
        $player = Auth::user();

<<<<<<< HEAD
        if ($player->balanceFloat < $inputs['amount']) {
            return $this->error('', 'Insufficient balance', 401);
=======
            if ($player->balanceFloat < $inputs['amount']) {
                return $this->error('', ['amount' => 'Insufficient balance'], 422);
            }

            $withdraw = ModelsWithDrawRequest::create(array_merge(
                $inputs,
                [
                    'user_id' => $player->id,
                    'agent_id' => $player->agent_id,
                ]
            ));

            return $this->success(new WithdrawResource($withdraw), 'Withdraw Request Success');
        } catch (Exception $e) {
            return $this->error('', $e->getMessage(), 422);
>>>>>>> f6a1cd8 (a)
        }

        $withdraw = ModelsWithDrawRequest::create(array_merge(
            $inputs,
            [
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
            ]
        ));

        return $this->success(new WithdrawResource($withdraw), 'Withdraw Request Success');
    }
}
