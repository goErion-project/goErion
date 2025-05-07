<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Illuminate\Support\Collection;

class ElasticsearchEngine extends Engine
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST') . ':' . env('ELASTICSEARCH_PORT')])
            ->build();
    }

    public function update($models)
    {
        foreach ($models as $model) {
            $this->client->index([
                'index' => env('ELASTICSEARCH_INDEX'),
                'id' => $model->getKey(),
                'body' => $model->toSearchableArray(),
            ]);
        }
    }

    public function delete($models)
    {
        foreach ($models as $model) {
            $this->client->delete([
                'index' => env('ELASTICSEARCH_INDEX'),
                'id' => $model->getKey(),
            ]);
        }
    }

    public function search(Builder $builder)
    {
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

    public function map(Builder $builder, $results, $model)
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

    public function lazyMap(Builder $builder, $results, $model)
    {
        return $this->map($builder, $results, $model);
    }

    public function paginate(Builder $builder, $perPage, $page)
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

    public function flush($model)
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

    public function createIndex($name, array $options = [])
    {
        $this->client->indices()->create([
            'index' => $name,
            'body' => $options,
        ]);
    }

    public function deleteIndex($name)
    {
        $this->client->indices()->delete([
            'index' => $name,
        ]);
    }
}