<?php

namespace App\Http\Controllers\Api\V1\Slot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GetGameProviderService;
use Illuminate\Support\Facades\Log;


class GetGameProviderController extends Controller
{
    protected $getGameProviderService;

    public function __construct(GetGameProviderService $getGameProviderService)
    {
        $this->getGameProviderService = $getGameProviderService;
    }

    public function fetchGameProviders(Request $request)
    {
         Log::info('Incoming Request to GetGameProvider', $request->all());
        $response = $this->getGameProviderService->getGameProvider();

        if (isset($response['ErrorCode']) && $response['ErrorCode'] == 0) {
            return response()->json([
                'success' => true,
                'data' => $response['Data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['ErrorMessage'] ?? 'An error occurred',
        ], 400);
    }
}