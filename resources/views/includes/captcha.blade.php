<div class="row mb-3">
    <div class="text-center">
        <img src="{{ $captcha }}" alt="">
    </div>
    <label for="captcha" class="form-label">{{ __('Captcha *') }}</label>
    <div class="col-md-12">
        <input class="form-control mt-3 @if($errors->has('captcha')) is-invalid @endif" type="text" name="captcha" placeholder="Enter Captcha">
        @error('captcha',$errors)
        <p class="text-danger">{{$errors->first('captcha')}}</p>
        @enderror
    </div>
</div>
