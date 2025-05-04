<?php

namespace App\Http\Requests\Bitmessage;

use App\Exceptions\RequestException;
use App\Marketplace\Bitmessage\Bitmessage;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @property mixed $address
 */
class SendConfirmationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'address.required' => 'Valid Bitmessage address is required'
        ];
    }

    /**
     * Requires bitmessage instance and send a confirmation message to the user
     *
     * @param Bitmessage $bitmessage
     * @param $marketAddress
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RequestException
     */
    public function persist(Bitmessage $bitmessage,$marketAddress): void
    {
        if (session()->has('bitmessage_confirmation')){

            $time = session()->get('bitmessage_confirmation')['time'];
            $validTime = config('bitmessage.confirmation_msg_frequency');

            if ($time->diffInSeconds(Carbon::now()) < $validTime){
                throw new RequestException("You can request new code every {$validTime} ".Str::plural('second',$validTime));

            }
        }

        $confirmationCode = strtoupper(Str::random(8));
        $subject = config('app.name').' Bitmessage Address Verification #'.strtoupper(Str::random(5));
        $validTime = config('bitmessage.confirmation_valid_time');
        $minute = Str::plural('minute',$validTime);
        $message = "Confirmation code: {$confirmationCode} . Code will be valid for {$validTime} {$minute}";
        try{
            $bitmessage->sendMessage($this->address,$marketAddress,$subject,$message);
            session()->flash('success','Confirmation code sent');
            if (session()->has('bitmessage_confirmation')){
                session()->forget('bitmessage_confirmation');
            }
            $data = [
                'address' => $this->address,
                'code' => $confirmationCode,
                'time' => Carbon::now()
            ];
            session()->put('bitmessage_confirmation',$data);
        } catch (\Exception $e){
            throw new RequestException('Could not send message to the provided address');
        }
    }
}
