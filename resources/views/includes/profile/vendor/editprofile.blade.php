<div class="card rounded bg-gray-800 text-gray-300 fw-bold p-4 mb-4">
<h3 class="card rounded bg-gray-700 text-gray-300 fw-bold p-4 mb-3 text-center">Edit profile</h3>
<form action="{{route('profile.vendor.update.post')}}" method="post" class="mb-3">
    @csrf
    <div class="row">
        <div class="col-md-6 card rounded bg-gray-700 text-gray-300 fw-bold p-4 mb-3">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="" cols="30" rows="6" class="form-control mb-2 mt-2" style="resize: none;">{{$vendor->about}}</textarea>
                <span class="form-text text-gray-300">Displayed on your vendor profile page. Limited to 120 characters</span>
            </div>
        </div>
        <div class="col-md-5 card rounded bg-gray-700 text-gray-300 fw-bold p-4 mb-3 ms-2">
            <div class="form-group">
                <label for="profilebg">Profile background</label>
                <select name="profilebg" id="profilebg" class="form-control">
                    @foreach(config('vendor.profile_bgs') as $key => $class)
                        <option value="{{$key}}" @if($vendor->getProfileBg() == $class) selected @endif>{{ucfirst($key)}}</option>
                    @endforeach
                </select>
                <div class="card mt-1">
                    <div class="card text-center fw-bold bg-gray-300">
                        Current background
                    </div>
                    <div class="card-body h-100 rounded{{$vendor->getProfileBg()}}">
                        <span style="opacity: 0;">test</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary mt-2">Update</button>
        </div>
    </div>
</form>
</div>
