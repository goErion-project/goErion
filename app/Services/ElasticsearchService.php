<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $hosts = config('database.elasticsearch.hosts', ['localhost:9200']);

        $builder = ClientBuilder::create()
            ->setHosts($hosts)
            ->setRetries(config('database.elasticsearch.retries', 3));

        if (config('database.elasticsearch.username') && config('database.elasticsearch.password')) {
            $builder->setBasicAuthentication(
                config('database.elasticsearch.username'),
                config('database.elasticsearch.password')
            );
        }

        $this->client = $builder->build();
    }

    // ... rest of your methods ...
}
