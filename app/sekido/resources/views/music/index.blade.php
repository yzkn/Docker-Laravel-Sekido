@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('css/audio.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/marquee.css') }}">
<div class="container">
    <div class="row justify-content-center">
        @can('user-higher') {{-- ユーザー権限以上に表示される --}}
        <div class="col-md-12">
            <div class="jumbotron my-5">
                <img src="" class="img-thumbnail music-jumbotron-thumbnail" id="playing_thumbnail">
                <h1 class="display-4 my-3" id="playing_title"></h1>
                <hr class="my-4">
                <div class="row mt-3 col-sm-10 offset-sm-2">
                    <div class="marquee">
                        <p id="audio-info"></p>
                    </div>
                    <a id="twitter_share" href="#" target="_blank">
                        <img class="icon mx-2" src="{{ asset('icon/Twitter_Logo_Blue.png') }}" alt="">
                    </a>
                </div>
                <div class="row mt-1 col-sm-10 offset-sm-2">
                    @isset ($musics)
                    @if (count($musics)>0)
                    <audio autoplay preload="auto"></audio>
                    @endif
                    @endisset
                </div>
            </div>

            <ol id="playlist" class="list-group my-5 col-md-12">
                @foreach ($musics as $key => $music)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="#" class="musicitem" data-src="{{ (route('home').$music->path) }}" id="{{ $music->id }}">
                        <img src="{{route('home')}}{{ $music->cover }}" class="img-thumbnail music-item-thumbnail" style="{{ $music->cover ? '' : 'visibility:hidden'}}">
                        {{$music->title}}
                        <span class="info">
                            <span class="artist">{{$music->artist}}</span> / <span class="album">{{$music->album}}</span> / <span class="title">{{$music->title}}</span>
                        </span>
                    </a>
                    <span>
                        <!-- <button type="button" class="queue btn btn-sm btn-outline-warning">Add to queue</button> -->
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

<script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
<script src="{{ asset('js/audio.js') }}"></script>
<script src="{{ asset('js/audioapp.js') }}"></script>
@endsection