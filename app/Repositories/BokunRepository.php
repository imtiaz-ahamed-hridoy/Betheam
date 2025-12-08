<?php

namespace App\Repositories;

use Illuminate\Support\Facades\{Http, Log};
use Illuminate\Http\Client\RequestException;
use Carbon\Carbon;

class BokunRepository
{
    protected string $baseUrl;
    protected string $accessKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl   = env('BOKUN_API_BASE_URL', 'https://api.bokuntest.com');
        $this->accessKey = env('BOKUN_ACCESS_KEY');
        $this->secretKey = env('BOKUN_SECRET_KEY');
    }

    /**
     * Generate Bokun signature
     */
    protected function generateSignature(string $method, string $pathWithQuery, string $date): string
    {
        $method = strtoupper($method);
        $stringToSign = $date . $this->accessKey . $method . $pathWithQuery;
        return base64_encode(hash_hmac('sha1', $stringToSign, $this->secretKey, true));
    }

    /**
     * Make a dynamic request to any Bokun endpoint
     *
     * @param string $method GET|POST|PUT|DELETE
     * @param string $endpoint /currency.json/findAll
     * @param array  $params Query or Body parameters
     */
    public function request(string $method, string $endpoint, array $params = []): array
    {
        $method = strtoupper($method);
        $date = Carbon::now('UTC')->format('Y-m-d H:i:s');

        // Build path with query for signature if GET/DELETE
        $pathWithQuery = $endpoint;
        if (in_array($method, ['GET', 'DELETE']) && !empty($params)) {
            $pathWithQuery .= '?' . http_build_query($params);
        }
        
        $signature = $this->generateSignature($method, $pathWithQuery, $date);

        $builder = Http::baseUrl($this->baseUrl)->withHeaders([
            'X-Bokun-Date'      => $date,
            'X-Bokun-AccessKey' => $this->accessKey,
            'X-Bokun-Signature' => $signature,
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/json',
        ]);

        try {
            $response = match ($method) {
                'GET'    => $builder->get($endpoint, $params),
                'DELETE' => $builder->delete($endpoint, $params),
                'POST'   => $builder->post($endpoint, $params),
                'PUT'    => $builder->put($endpoint, $params),
                default  => throw new \Exception("Unsupported HTTP method: $method"),
            };

            if ($response->failed()) {
                $response->throw();
            }

            Log::info('Bokun API Request', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'params'   => $params,
                'response' => $response->body(),
            ]);

            return $response->json();
        } catch (RequestException $e) {
            Log::error('Bokun API Error', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'params'   => $params,
                'response' => $e->response?->body(),
            ]);
            throw $e;
        }
    }
}
