<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * @property mixed $user
 */
class DisplayProductsRequest extends FormRequest
{
    /**
     * How many users to display in a table per single page
     *
     * @var int
     */
    private int $displayProductsPerPage = 30;

    /**
     * Array of methods supported for ordering
     *
     * @var array
     */
    private array $availableOrderMethods = [
        'newest',
        'oldest'
    ];
    /**
     * Default order
     *
     * @var string
     */
    private string $orderBy = 'newest';



    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product' => 'string|nullable'
        ];
    }

    public function persist(): void
    {
        $orderBy = $this->get('order_by');
        if ($orderBy !== null && in_array($orderBy, $this->availableOrderMethods)) {
            $this->orderBy = $orderBy;
        }

    }

    public function getProducts(): LengthAwarePaginator
    {
        $products = Product::with(['user','category']);
        if ($this->user !== null && $this->user !== '' ){
            $products->where('user_id',$this->user);
        }

        if(!empty($this -> product)){
            $products -> where('name', 'LIKE', '%' . $this->product . '%');
        }

        $products = $products->get();

        if ($this->orderBy == 'newest') {
            $products = $products->sortBy('created_at');
        }
        if ($this->orderBy == 'oldest') {
            $products = $products->sortByDesc('created_at');
        }

        $finalResult = $this->paginate($products, $this->displayProductsPerPage);
        $finalResult->setPath($this->fullUrl());
        return $finalResult;
    }

    /**
     * Paginates a collection
     *
     * @param $items
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    private function paginate($items, int $perPage = 15): LengthAwarePaginator
    {
        $options = [];
        $page = null ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
