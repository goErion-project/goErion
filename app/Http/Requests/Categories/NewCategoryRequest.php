<?php

namespace App\Http\Requests\Categories;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $name
 * @property mixed $parent_id
 */
class NewCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:categories,id'
        ];
    }

    public function persist($id = null): void
    {
        if (is_null($id)) {
            $categoryInsert = new Category;
        } else{
            $categoryInsert = Category::query()->findOrFail($id);
        }
        $categoryInsert -> name = $this -> name;
        $categoryInsert -> parent_id = $this -> parent_id;
        $categoryInsert -> save();
    }
}
