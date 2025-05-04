<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property mixed $parent
 * @property mixed $id
 * @property mixed $products
 * @property mixed $children
 */
class Category extends Model
{
    use Uuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public static function roots(): \Illuminate\Support\Collection
    {
        return self::query()->whereNull('parent_id')->get();
    }

    public static function nameOrdered(): \Illuminate\Support\Collection
    {
        return self::query()->orderBy('name')->get();
    }

    public function parent(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function parents(): \Illuminate\Support\Collection
    {
        $ancestorsCollection = collect();
        $currentParent = $this->parent;
        while ($currentParent !== null) {
            $ancestorsCollection->push($currentParent);
            $currentParent = $currentParent->parent;
        }
        return $ancestorsCollection->reverse();
    }

    public function getChildrenAttribute(): Collection
    {
        return self::query()->where('parent_id',$this->id)->get();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id')
            ->where('active', true);
    }

    public function isAncestorOf($childCategory): bool
    {
        if (is_null($childCategory))
            return false;
        //starting from the parent of the child category
        $tempCategory = $childCategory;

        //while is not root
        while ($tempCategory)
        {
            //true, if tempCategory equals this category
            if ($tempCategory->id == $this->id)
                return true;
            $tempCategory = $tempCategory->parent;
        }
        //otherwise $this is not ancestor
        return false;
    }

    public function getNumProductsAttribute(): int
    {
        $numProducts = count($this->products);

        $otherCategories = Category::query()->where('id','<>', $this->id)->get();
        foreach ($otherCategories as $categ)
        {
            if ($this->isAncestorOf($categ))
                $numProducts += count($categ->products);
        }
        return $numProducts;
    }

    public function allChildren()
    {
        //get all children
        $children = $this->children;
        //foreach child category call recursively
        foreach ($this->children as $childCategory)
        {
            $children = $children->merge($childCategory->allChildren());
        }
        return $children;
    }

    public function allChildrenIds()
    {
        return $this->allChildren()->pluck('name')->toArray();
    }

    public function childProducts(): LengthAwarePaginator
    {
        $allAcceptedCategoriesIds = array_merge([$this->id], $this->allChildrenIds());
        return Product::query()->where('active', true)
            ->whereIn('category_id', $allAcceptedCategoriesIds)->orderByDesc('created_at')
            ->paginate(config('marketplace.products_per_page'));
    }
}
