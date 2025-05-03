<?php

namespace App\Http\Requests\Purchase;

use App\Events\Purchase\NewFeedback;
use App\Exceptions\RequestException;
use App\Models\Feedback;
use App\Models\Purchase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * @property mixed $quality_rate
 * @property mixed $shipping_rate
 * @property mixed $communication_rate
 * @property mixed $comment
 * @property mixed $type
 */
class LeaveFeedbackRequest extends FormRequest
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
            'quality_rate' => 'numeric|min:1|max:5',
            'shipping_rate' => 'numeric|min:1|max:5',
            'communication_rate' => 'numeric|min:1|max:5',
            'comment' => 'required|string',
        ];
    }

    /**
     * Persisting this request
     *
     * @param Purchase $purchase
     * @throws RequestException
     * @throws \Throwable
     */
    public function persist(Purchase $purchase): void
    {
        if(!$purchase -> isBuyer())
            throw new RequestException('You can\'t post feedback for this purchase');
        if($purchase -> hasFeedback())
            throw new RequestException('This purchase already has feedback!');

        try{
            DB::beginTransaction();

            $newFeedback = new Feedback;
            $newFeedback -> setProduct($purchase -> offer -> product);
            $newFeedback -> setVendor($purchase -> vendor);
            $newFeedback -> setBuyer($purchase->buyer);
            $newFeedback -> quality_rate = $this -> quality_rate;
            $newFeedback -> shipping_rate = $this -> shipping_rate;
            $newFeedback -> communication_rate = $this -> communication_rate;
            $newFeedback -> comment = $this -> comment;
            $newFeedback -> type = $this -> type;
            $newFeedback -> product_name = $purchase->offer->product->name;
            $newFeedback -> product_value = $purchase->value_sum;
            $newFeedback -> save();
            $purchase -> setFeedback($newFeedback);
            $purchase -> save();

            DB::commit();

            event(new NewFeedback($newFeedback));
        }
        catch (\Exception $e){

            DB::rollBack();

            throw new RequestException('Error happened, please try again!');
        }

    }
}
