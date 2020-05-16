@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @can('user-higher') {{-- ユーザー権限以上に表示される --}}
            <div class="col-md-12">
                <div class="jumbotron my-5">
                    <h1 class="display-4 my-3">Playlist</h1>
                    <hr class="my-4">
                    <p class="lead text-right mt-4">
                        <a href="{{ url('playlist/create') }}" class="btn btn-outline-info">{{ __('Create') }}</a>
                    </p>
                </div>

                <ol id="playlist" class="list-group my-5 col-md-12">
                    @foreach ($playlists as $playlist)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <a href="{{ url('playlist/'. $playlist->id) }}">{{$playlist->title}}</a>
                                    <span class="badge badge-info badge-pill ml-1 mr-5">{{ count($playlist->musics) }}</span>
                                </span>
                                <span>
                                <a href="{{ url('playlist/'.$playlist->id.'/edit') }}" class="btn btn-outline-success">{{ __('Edit') }}</a>
                                <form action="{{ url('playlist/'. $playlist->id) }}" method="post" style="display:inline;" onSubmit="return window.confirm('削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" name="submit" class="btn btn-outline-danger">{{ __('Delete') }}</button>
                                </form>
                                </span>
                            </div>
                            <div>
                                {{$playlist->description}}
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endcan
@endsection
