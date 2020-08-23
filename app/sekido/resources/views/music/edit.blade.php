@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('css/audio.css') }}">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edit - {{ $music->id }}</div>
                <div class="card-body">
                    <form action="{{ url('music/'.$music->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-12 row justify-content-center my-5">
                                    <audio preload="auto" controlls src="{{ isset($music) ? (route('home').$music->path) : '' }}"></audio>
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Title') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', isset($music) ? $music->title : '') }}" placeholder="{{ __('Title') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Artist') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="artist" name="artist" value="{{ old('artist', isset($music) ? $music->artist : '') }}" placeholder="{{ __('Artist') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Playlist') }}</div>
                                <div class="col-sm-8 my-2">
                                    <select name="playlists[]" class="form-control" id="playlists" multiple>
                                        @foreach($playlists as $playlist)
                                            <option value="{{ $playlist->id }}" @if(in_array($playlist->id, $music_playlists) || in_array($playlist->id, old('playlists') ?? [])) selected @endif>{{ $playlist->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Album') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="album" name="album" value="{{ old('album', isset($music) ? $music->album : '') }}" placeholder="{{ __('Album') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Track number') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="track_num" name="track_num" value="{{ old('track_num', isset($music) ? $music->track_num : '') }}" placeholder="{{ __('Track number') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Bitrate') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="bitrate" name="bitrate" value="{{ old('bitrate', isset($music) ? $music->bitrate : '') }}" placeholder="{{ __('Bitrate') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Genre') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="genre" name="genre" list="genre_list" value="{{ old('genre', isset($music) ? $music->genre : '') }}" placeholder="{{ __('Genre') }}">
                                    <datalist id="genre_list">
                                        @foreach($genre_list as $index => $name)
                                            <option value="{{ $name }}" @if(old('genre') == $name) selected @endif>{{ $name}} </option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Original Artist') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="originalArtist" name="originalArtist" value="{{ old('originalArtist', isset($music) ? $music->originalArtist : '') }}" placeholder="{{ __('Original Artist') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Playtime') }}</div>
                                <div class="col-sm-6 my-2">
                                    <input type="text" class="form-control" id="playtime_seconds" name="playtime_seconds" value="{{ old('playtime_seconds', isset($music) ? $music->playtime_seconds : '') }}" placeholder="{{ __('Playtime') }}">
                                </div>
                                <div class="col-sm-2 my-2 d-flex align-items-end">{{ __('seconds') }}</div>
                                <div class="col-sm-4 my-2">{{ __('Related works') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="text" class="form-control" id="related_works" name="related_works" value="{{ old('related_works', isset($music) ? $music->related_works : '') }}" placeholder="{{ __('Related works') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Year') }}</div>
                                <div class="col-sm-8 my-2">
                                    <input type="number" class="form-control" id="year" name="year" value="{{ old('year', isset($music) ? $music->year : '') }}" placeholder="{{ __('Year') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Cover') }}</div>
                                <div class="col-sm-8 my-2">
                                    @isset($music)
                                        @isset($music->cover)
                                            @if('' !== $music->cover)
                                                <img src="{{route('home')}}{{ $music->cover }}" alt="" class="img-thumbnail"><br>
                                            @endif
                                        @endisset
                                    @endisset
                                    <input type="file" class="form-control" id="cover" name="cover" value="{{ old('cover', isset($music) ? $music->cover : '') }}" placeholder="{{ __('Cover') }}">
                                </div>
                                <div class="col-sm-4 my-2">{{ __('Document') }}</div>
                                <div class="col-sm-8 my-2">
                                    @isset($music)
                                        @isset($music->document)
                                            @if('' !== $music->document)
                                                <a href="{{route('home')}}{{ $music->document }}" target="_blank">
                                                    <a href="{{route('home')}}{{ $music->document }}" target="_blank">
                                                        {{ basename($music->document) }}
                                                    </a>
                                                </a>
                                            @endif
                                        @endisset
                                    @endisset
                                    <input type="file" class="form-control" id="document" name="document" value="{{ old('document', isset($music) ? $music->document : '') }}" placeholder="{{ __('Document') }}">
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

<script src="{{ asset('js/audio.js') }}"></script>
<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>
@endsection
