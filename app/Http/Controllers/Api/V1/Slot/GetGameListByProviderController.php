<?php

namespace App\Http\Controllers\Api\V1\Slot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GetGameListByProviderService;
use Illuminate\Support\Facades\Log;

class GetGameListByProviderController extends Controller
{
    protected $getGameListByProviderService;

    public function __construct(GetGameListByProviderService $getGameListByProviderService)
    {
        $this->getGameListByProviderService = $getGameListByProviderService;
    }

    public function fetchGameListByProvider(Request $request)
    {
        Log::info('Incoming Request to GetGameListByProvider', $request->all());

        // Validate incoming request
        $request->validate([
            'ProviderCode' => 'required|string|max:50',
        ]);

        // Retrieve provider code from request
        $providerCode = $request->input('ProviderCode');

        // Call the service
        $response = $this->getGameListByProviderService->getGameListByProvider($providerCode);

        // Log the response structure for debugging
        Log::info('API Response Structure', ['response' => $response]);

        // Check if the response is successful
        if (isset($response['status']) && $response['status'] == 200) {
            return response()->json([
                'success' => true,
                'data' => $response['GameList'] ?? [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['Description'] ?? 'An error occurred',
        ], 400);
    }
}