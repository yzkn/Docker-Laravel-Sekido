@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edit - {{ $playlist->id }}</div>
                <div class="card-body">
                    <form action="{{ url('playlist/'.$playlist->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-4 my-2">{{ __('Title') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', isset($playlist) ? $playlist->title : '') }}" placeholder="{{ __('Title') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Description') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="description" name="artist" value="{{ old('description', isset($playlist) ? $playlist->description : '') }}" placeholder="{{ __('Description') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Cover') }}</div>
                                <div class="col-sm-8 my-2">
                                    @isset($playlist)
                                        @isset($playlist->cover)
                                            @if('' !== $playlist->cover)
                                                <img src="{{route('home')}}{{ $playlist->cover }}" alt="" class="img-thumbnail"><br>
                                            @endif
                                        @endisset
                                    @endisset
                                    <input type="file" class="form-control" id="cover" name="cover" value="{{ old('cover', isset($playlist) ? $playlist->cover : '') }}" placeholder="{{ __('Cover') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Musics') }}</div>
                                <div class="col-sm-8 my-2">
                                    <select name="musics[]" class="form-control" id="musics" multiple>
                                        @foreach($musics as $music)
                                            <option value="{{ $music->id }}" @if(in_array($music->id, $playlist_musics) || in_array($music->id, old('musics') ?? [])) selected @endif>{{ $music->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-5 col-sm-8 offset-sm-4">
                                    <button type="submit" name="submit" class="btn btn-outline-primary">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
