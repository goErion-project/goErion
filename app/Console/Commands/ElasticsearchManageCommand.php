<?php

// app/Console/Commands/ElasticsearchManageCommand.php
namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticsearchManageCommand extends Command
{
    protected $signature = 'elasticsearch:manage {action} {model?}';
    protected $description = 'Manage Elasticsearch indices';

    public function handle(): void
    {
        $action = $this->argument('action');
        $model = $this->argument('model');

        $service = app(ElasticsearchService::class);

        switch ($action) {
            case 'create-index':
                $this->createIndex($service, $model);
                break;
            case 'delete-index':
                $this->deleteIndex($service, $model);
                break;
            case 'reindex':
                $this->reindex($service, $model);
                break;
            default:
                $this->error('Invalid action. Available: create-index, delete-index, reindex');
        }
    }

    protected function createIndex(ElasticsearchService $service, ?string $model): void
    {
        if ($model) {
            $class = "App\\Models\\$model";
            $model = new $class();

            $settings = [
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'asciifolding']
                        ]
                    ]
                ]
            ];

            if ($service->createIndex($model->getSearchIndex(), [], $settings)) {
                $this->info("Index {$model->getSearchIndex()} created successfully");
            } else {
                $this->error("Failed to create index {$model->getSearchIndex()}");
            }
        }
    }

    protected function deleteIndex(ElasticsearchService $service, ?string $model): void
    {
        if ($model) {
            $class = "App\\Models\\$model";
            $model = new $class();

            try {
                $service->getClient()->indices()->delete(['index' => $model->getSearchIndex()]);
                $this->info("Index {$model->getSearchIndex()} deleted successfully");
            } catch (\Exception $e) {
                $this->error("Failed to delete index {$model->getSearchIndex()}: {$e->getMessage()}");
            }
        }
    }

    protected function reindex(ElasticsearchService $service, ?string $model)
    {
        if ($model) {
            $class = "App\\Models\\$model";
            $model = new $class();

            $this->call('elasticsearch:manage', ['action' => 'delete-index', 'model' => $model]);
            $this->call('elasticsearch:manage', ['action' => 'create-index', 'model' => $model]);

            $class::chunk(100, function ($models) {
                $models->each->addToSearchIndex();
                $this->info("Indexed {$models->count()} records");
            });

            $this->info("Reindexing of {$model->getSearchIndex()} completed");
        }
    }
}
