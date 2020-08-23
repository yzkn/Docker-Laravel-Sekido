@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('css/audio.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/marquee.css') }}">
<div class="container">
    <div class="row justify-content-center">
        @can('user-higher') {{-- ユーザー権限以上に表示される --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Detail - {{ $playlist->id ?? '' }}</div>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-4 my-2">{{ __('Title') }}</div>
                            <div class="col-sm-8 my-2">{{ $playlist->title ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Description') }}</div>
                            <div class="col-sm-8 my-2">{{ $playlist->description ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Cover') }}</div>
                            <div class="col-sm-8 my-2">
                                @isset($playlist)
                                @isset($playlist->cover)
                                @if('' !== $playlist->cover)
                                <img src="{{route('home')}}{{ $playlist->cover }}" alt="" class="img-thumbnail">
                                @endif
                                @endisset
                                @endisset
                            </div>
                            <div class="mt-5 col-sm-8 offset-sm-4">
                                @isset($playlist)
                                @isset($playlist->id)
                                <a href="{{ url('playlist/'.$playlist->id.'/edit') }}" class="btn btn-outline-success">{{ __('Edit') }}</a>
                                <form action="{{ url('playlist/'. $playlist->id) }}" method="post" style="display:inline;" onSubmit="return window.confirm('削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" name="submit" class="btn btn-outline-danger">{{ __('Delete') }}</button>
                                </form>
                                @endisset
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="jumbotron my-5">
                <h1 class="display-4 my-3">Player</h1>
                <hr class="my-4">
                <div class="row mt-3 justify-content-center">
                    @isset ($playlist)
                    @isset ($playlist->musics)
                    @if (count($playlist->musics)>0)
                    <audio autoplay preload="auto"></audio>
                    @endif
                    @endisset
                    @endisset
                </div>
                <div class="row mt-1 col-sm-9 offset-sm-3">
                    <div>
                        <p id="audio-info"></p>
                    </div>
                    @isset ($playlist)
                    @isset ($playlist->musics)
                    @if (count($playlist->musics)>0)
                    <a id="twitter_share" href="#" target="_blank">
                        <img class="pl-icon mx-2" src="{{ asset('icon/Twitter_Logo_Blue.png') }}" alt="">
                    </a>
                    @endif
                    @endisset
                    @endisset
                </div>
            </div>

            <ol id="playlist" class="list-group my-5 col-md-12">
                @foreach ($playlist->musics as $key => $music)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="#" class="musicitem" data-src="{{ (route('home').$music->path) }}" id="{{ $music->id }}">
                        <img src="{{route('home')}}{{ $music->cover }}" class="img-thumbnail music-item-thumbnail" style="{{ $music->cover ? '' : 'visibility:hidden'}}">
                        {{$music->title}}
                        <span class="info">
                            <span class="artist">{{$music->artist}}</span> / <span class="album">{{$music->album}}</span> / <span class="title">{{$music->title}}</span>
                        </span>
                    </a>
                    <span>
                        <a role="button" href="#" class="btn btn-sm btn-outline-dark" onclick="event.preventDefault();document.getElementById('music-search-artist-form-{{ $key }}').submit();">
                            <small>{{$music->artist}}</small>
                        </a>
                        <form id="music-search-artist-form-{{ $key }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="artist" value="{{ $music->artist }}">
                        </form>
                        <a role="button" href="#" class="btn btn-sm btn-outline-dark" onclick="event.preventDefault();document.getElementById('music-search-album-form-{{ $key }}').submit();">
                            <small>{{$music->album}}</small>
                        </a>
                        <form id="music-search-album-form-{{ $key }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="album" value="{{ $music->album }}">
                        </form>
                        <a role="button" href="#" class="btn btn-sm btn-outline-dark" onclick="event.preventDefault();document.getElementById('music-search-title-form-{{ $key }}').submit();">
                            <small>{{$music->title}}</small>
                        </a>
                        <form id="music-search-title-form-{{ $key }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="title" value="{{ $music->title }}">
                        </form>

                        @if('' !== $music->related_works)
                            <a role="button" href="#" class="btn btn-sm btn-outline-success" onclick="event.preventDefault();document.getElementById('music-search-related_works-form-{{ $key }}').submit();">
                                <small>{{$music->related_works}}</small>
                            </a>
                            <form id="music-search-related_works-form-{{ $key }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="related_works" value="{{ $music->related_works }}">
                            </form>
                        @endif

                        <a href="{{ url('music/'.$music->id) }}" target="_blank">
                            <img class="icon mx-1" src="{{ asset('icon/tab.png') }}" alt="">
                        </a>
                    </span>
                </li>
                @endforeach
            </ol>
        </div>
        @endcan
    </div>
</div>

<script src="{{ asset('js/audio.js') }}"></script>
<script src="{{ asset('js/audioapp.js') }}"></script>
@endsection