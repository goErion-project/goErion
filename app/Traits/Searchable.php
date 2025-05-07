<?php

// app/Traits/Searchable.php
namespace App\Traits;

use App\Services\ElasticsearchService;
use Exception;

/**
 * @method getTable()
 * @method static deleted(\Closure $param)
 * @method static updated(\Closure $param)
 * @method static created(\Closure $param)
 * @method getKey()
 */
trait Searchable
{
    public static function bootSearchable(): void
    {
        static::created(function ($model) {
            if ($model->shouldBeSearchable()) {
                $model->addToSearchIndex();
            }
        });

        static::updated(function ($model) {
            if ($model->shouldBeSearchable()) {
                $model->addToSearchIndex();
            } else {
                $model->removeFromSearchIndex();
            }
        });

        static::deleted(function ($model) {
            $model->removeFromSearchIndex();
        });
    }

    public function addToSearchIndex(): bool
    {
        try {
            return app(ElasticsearchService::class)->indexDocument(
                $this->getSearchIndex(),
                $this->toSearchableArray(),
                $this->getSearchKey()
            );
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function removeFromSearchIndex(): bool
    {
        try {
            return app(ElasticsearchService::class)->deleteDocument(
                $this->getSearchIndex(),
                $this->getSearchKey()
            );
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function getSearchIndex(): string
    {
        return $this->getTable();
    }

    public function getSearchKey()
    {
        return $this->getKey();
    }

    public function shouldBeSearchable(): bool
    {
        return true;
    }

    abstract public function toSearchableArray(): array;
}
