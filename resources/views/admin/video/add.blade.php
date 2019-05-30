@extends('layouts.admin')

@section('content')
    @include('partials.errors') 
    <form action="{{ route('admin.videos.add') }}" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title"/>
        </div>
        <div class="form-group">
            <label for="url">URL</label>
            <input type="file" name="video"/>    
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" cols="40" rows="5"></textarea>  
        </div>
        <div class="form-group">
            @foreach ($tags as $tag)
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}">
                        {{ $tag->name }}
                    </label>                    
                </div>
            @endforeach            
        </div>
        {{ csrf_field() }}
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
@endsection