<div class="card rounded bg-gray-800 text-gray-300 fw-bold p-4 mb-3">
<h3 class="card rounded bg-gray-700 text-gray-300 fw-bold p-4 mb-3 text-center">Experience</h3>

<div class="progress" style="height: 30px">
    <div class="progress-bar @if($vendor->experience < 0) bg-danger @endif" role="progressbar"
         style="width: {{$vendor->nextLevelProgress()}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
        Level {{$vendor->getLevel()}} ({{$vendor->nextLevelProgress()}}%)
    </div>
</div>
<div class="row mt-4">
    <div class="col">
        <p><b>Current level: </b><span class="fs-5">{{$vendor->getLevel()}}</span></p>
        <p><b>Current experience points: </b><span class="fs-5">{{$vendor->experience}}</span></p>
        <p><b>Experience required for next level: </b><span class="fs-5">{{max($vendor->nextLevelXp()-$vendor->experience,0)}}</span></p>
        @if($vendor->experience < 0)
            <div class="card mb-3">
                <div class="card-header">Negative experience</div>
                <div class="card-body">
                    <p>You have negative experience, all your offers will be labeled with this tag:</p>
                    <p class="text-danger border border-danger rounded p-1 mt-2 text-center"><span class="fas fa-exclamation-circle"></span> Negative experience, trade with caution !</p>
                </div>
                <div class="card-footer text-muted">
                    If you think this is an error, please contact administrator
                </div>
            </div>

        @endif
    </div>
</div>
</div>
