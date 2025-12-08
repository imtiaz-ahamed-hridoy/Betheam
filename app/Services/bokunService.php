<?php

namespace App\Services;

use App\Repositories\BokunRepository;

class BokunService
{
    protected BokunRepository $repository;

    public function __construct(BokunRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Unified request function for all Bokun endpoints
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $params
     */
    public function request(string $method, string $endpoint, array $params = []): array
    {
        return $this->repository->request($method, $endpoint, $params);
    }

    /**
     * Example helper: fetch product list
     */
    public function listProducts(array $params = []): array
    {
        return $this->request('GET', '/activity.json/active-ids', $params);
    }
}
