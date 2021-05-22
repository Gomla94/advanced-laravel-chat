@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header">
        Create New Chat Group
    </div>
    <div class="card-body">
        <form action="{{ route('chatGroup.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="">Group Name</label>
                <input type="text" name="name" class="form-control">
                @error('name')
                    <span style="color:red">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="users">Users</label>
                {{-- <select name="users[]" class="form-control" multiple id="users"> --}}
                    @foreach ($users as $user)
                        {{-- <option value="{{ $user->id }}">{{ $user->name }}</option> --}}
                        <div>
                            <input type="checkbox" name="users[]" value="{{$user->id}}"> {{$user->name}}
                        </div>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Create Group</button>
        </form>
    </div>
</div>
@endsection