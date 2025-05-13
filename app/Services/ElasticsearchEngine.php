<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Illuminate\Support\Collection;

class ElasticsearchEngine extends Engine
{
    protected Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST') . ':' . env('ELASTICSEARCH_PORT')])
            ->build();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function update($models): void
    {
        foreach ($models as $model) {
            $this->client->index([
                'index' => env('ELASTICSEARCH_INDEX'),
                'id' => $model->getKey(),
                'body' => $model->toSearchableArray(),
            ]);
        }
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function delete($models): void
    {
        foreach ($models as $model) {
            $this->client->delete([
                'index' => env('ELASTICSEARCH_INDEX'),
                'id' => $model->getKey(),
            ]);
        }
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function search(Builder $builder)
    {
       // If the query is empty, return all results
    if (empty($builder->query)) {
        $response = $this->client->search([
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);

        return $response['hits']['hits'];
    }

    $response = $this->client->search([
        'index' => env('ELASTICSEARCH_INDEX'),
        'body' => [
            'query' => [
                'match' => [
                    '_all' => $builder->query,
                ],
            ],
        ],
    ]);

    return $response['hits']['hits'];
    }

    public function map(Builder $builder, $results, $model): \Illuminate\Database\Eloquent\Collection|Collection
    {
        return collect($results)->map(function ($hit) use ($model) {
            $instance = $model->newInstance([], true);
            $instance->setRawAttributes($hit['_source'], true);
            return $instance;
        });
    }

    public function mapIds($results)
    {
        return collect($results)->pluck('_id')->values();
    }

    public function lazyMap(Builder $builder, $results, $model): \Illuminate\Database\Eloquent\Collection|Collection
    {
        return $this->map($builder, $results, $model);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function paginate(Builder $builder, $perPage, $page): array
    {
        $from = ($page - 1) * $perPage;

        $response = $this->client->search([
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'from' => $from,
                'size' => $perPage,
                'query' => [
                    'match' => [
                        '_all' => $builder->query,
                    ],
                ],
            ],
        ]);

        return [
            'results' => $this->map($builder, $response['hits']['hits'], $builder->model),
            'total' => $response['hits']['total']['value'],
        ];
    }

    public function getTotalCount($results)
    {
        return $results['hits']['total']['value'];
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function flush($model): void
    {
        $this->client->deleteByQuery([
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function createIndex($name, array $options = []): void
    {
        $this->client->indices()->create([
            'index' => $name,
            'body' => $options,
        ]);
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function deleteIndex($name): void
    {
        $this->client->indices()->delete([
            'index' => $name,
        ]);
    }
}
