<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\{JsonResponse, Request};
use App\Http\Controllers\Controller;
use App\Services\BokunService;

class BokunController extends Controller
{
    protected BokunService $service;

    public function __construct(BokunService $service)
    {
        $this->service = $service;
    }

    /**
     * Dynamic Bokun API request handler
     *
     * Body JSON example:
     * {
     *   "method": "GET",
     *   "endpoint": "/currency.json/findAll",
     *   "params": {}
     * }
     */
    public function handle(Request $request): JsonResponse
    {
        $method = strtoupper($request->input('method'));
        $endpoint = $request->input('endpoint');
        $params = $request->input('params', []);

        if (!$endpoint) {
            return response()->json([
                'status' => 'error',
                'message' => 'Endpoint is required.'
            ], 422);
        }

        try {
            $data = $this->service->request($method, $endpoint, $params);

            return response()->json([
                'status'   => 'success',
                'method'   => $method,
                'endpoint' => $endpoint,
                'data'     => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        }
    }

    public function checkActiveProducts(Request $request)
    {
        try {
            // Call your service method. Pass an empty array as it takes no query params.
            $activeIds = $this->service->listProducts([]); 
            
            // Check the response content and type
            if (empty($activeIds)) {
                $message = "Success (200 OK), but no active product IDs found in your Bokun account.";
            } else {
                $message = "Successfully fetched " . count($activeIds) . " active product IDs.";
            }
            
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $activeIds,
                'count' => count($activeIds)
            ]);

        } catch (\Exception $e) {
            // Handle exceptions (like RequestException from your service)
            return response()->json([
                'status' => 'error', 
                'message' => 'API Error during check.', 
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
