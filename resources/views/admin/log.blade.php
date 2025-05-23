@extends('layouts.admin')

@section('admin-content')
    <div class="row">
        <div class="col">
            <h4 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">
                Activity log of all administrators
            </h4>
            <hr>
            <p class="small text-muted">
                New logs are loaded  @if($cacheMinutes == 0) instantly @else every {{$cacheMinutes}} {{\Illuminate\Support\Str::plural('minute',$cacheMinutes)}} @endif. You can change this option in configuration.
            </p>
        </div>
    </div>
    <div class="card rounded mb-4 p-4">
        <table class="table">
            <thead>
            <tr>
                <th>User</th>
                <th>Type</th>
                <th>Description</th>
                <th>Performed on</th>
                <th>Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td><a href="{{route('admin.users.view',$log->user->id)}}">{{$log->user->username}}</a></td>
                    <td>{{$log->type}}</td>
                    <td>{{$log->description}}</td>
                    <td><a href="{{$log->performedOn()['link']}}">{{$log->performedOn()['text']}}</a></td>
                    <td>{{$log->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="text-center">
                {{$logs->links()}}
            </div>
        </div>
    </div>


@endsection
