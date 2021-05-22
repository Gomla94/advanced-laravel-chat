@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header">
        Chat Groups
    </div>
    <div class="card-body">
        <ul class="list-group">
            @foreach ($groups as $group)
                <li class="list-group-item">
                    {{$group->name}}
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection