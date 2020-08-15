@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('css/audio.css') }}">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Detail - {{ $music->id ?? '' }}</div>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 row justify-content-center my-5">
                                <audio preload="auto" controlls src="{{ isset($music) ? ((route('home').$music->path) ?? '') : '' }}"></audio>
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Title') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->title ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Artist') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->artist ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Playlist') }}</div>
                            <div class="col-sm-8 my-2">
                                <ul class="list-group">
                                    @foreach($music->playlists as $playlist)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ url('playlist/'.$playlist->id) }}">
                                                {{ $playlist->title }}
                                                <span class="badge badge-info badge-pill">{{ count($playlist->musics) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Album') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->album ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Track number') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->track_num ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Bitrate') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->bitrate ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Genre') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->genre ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Original Artist') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->originalArtist ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Playtime') }}</div>
                            <div class="col-sm-8 my-2">
                                {{ $music->playtime_seconds ? intdiv($music->playtime_seconds, 60) : '' }}:{{ $music->playtime_seconds ? ($music->playtime_seconds % 60) : '' }}
                                <small>
                                    ({{ $music->playtime_seconds ?? '' }} sec)
                                </small>
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Related works') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->related_works ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Year') }}</div>
                            <div class="col-sm-8 my-2">{{ $music->year ?? '' }}</div>
                            <div class="col-sm-4 my-2">{{ __('Cover') }}</div>
                            <div class="col-sm-8 my-2">
                                @isset($music)
                                    @isset($music->cover)
                                        @if('' !== $music->cover)
                                            <img src="{{route('home')}}{{ $music->cover }}" alt="" class="img-thumbnail">
                                        @endif
                                    @endisset
                                @endisset
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Document') }}</div>
                            <div class="col-sm-8 my-2">
                                @isset($music)
                                    @isset($music->document)
                                        @if('' !== $music->document)
                                            <a href="{{route('home')}}{{ $music->document }}" target="_blank">
                                                {{ basename($music->document) }}
                                                <br>
                                                <img class="img-thumbnail document-thumbnail" src="{{ $music->document . '.png' }}" />
                                            </a>
                                        @endif
                                    @endisset
                                @endisset
                            </div>
                            <div class="mt-5 col-sm-8 offset-sm-4">
                                @isset($music)
                                    @isset($music->id)
                                        <a href="{{ url('music/'.$music->id.'/edit') }}" class="btn btn-outline-success">{{ __('Edit') }}</a>
                                        <form action="{{ url('music/'. $music->id) }}" method="post" style="display:inline;" onSubmit="return window.confirm('削除しますか？')">
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
    </div>
</div>

<script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
<script src="{{ asset('js/audio.js') }}"></script>
<script>
    audiojs.events.ready(function() {
        var as = audiojs.createAll();
    });
</script>
@endsection
