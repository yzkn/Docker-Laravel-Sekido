<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bubbly-bg@1.0.0/dist/bubbly-bg.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bubbly.js') }}" defer></script>
    <script src="{{ asset('js/calendar.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/audioapp.css') }}" rel="stylesheet">
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand px-5" href="{{ url('/') }}">
                {{ config('app.name', 'Sekido') }}
            </a>
            <button class="navbar-toggler px-5" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse px-5" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('Music') }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="nav-link" href="{{ url('music') }}">{{ __('All musics') }}</a></li>
                                <li class="nav-item dropdown_music_list">
                                    <a class="nav-link" href="#">{{ __('List') }}</a>
                                    <ul class="nav flex-column dropdown_music_list_sub">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=album">{{ __('album') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=artist">{{ __('artist') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=genre">{{ __('genre') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=originalArtist">{{ __('originalArtist') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=related_works">{{ __('related_works') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=title">{{ __('title') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=track_num">{{ __('track_num') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('music/list') }}?column=year">{{ __('year') }}</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item"><a class="nav-link" href="{{ url('music/search') }}">{{ __('Search') }}</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ url('music/upload') }}">{{ __('Upload') }}</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('Playlist') }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ url('playlist') }}">{{ __('List') }}</a></li>
                                <li><a class="nav-link" href="{{ url('playlist/create') }}">{{ __('Create') }}</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </nav>

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="row col-md-11">
            <div class="col-md-6 offset-md-1 main">
                <main class="py-4">
                    @yield('content')
                </main>
            </div>
            @can('user-higher') {{-- ユーザー権限以上に表示される --}}
            <aside class="col-md-3 offset-md-1 my-5 p-4 sidebar bg-light rounded">
                <div class="p-4 my-1">
                    <h4 class="font-italic">概要</h4>
                    <p class="mb-1">
                        {{ Auth::user()->name }}さんとしてログインしています。
                    </p>
                    <p class="mb-2">
                        ログイン時刻: {{ Auth::user()->last_login_at }}
                    </p>
                    <p class="mb-0">
                        アップロード: {{ App\Music::where('user_id', '=', Auth::user()->id)->count() }}曲
                    </p>
                    <p class="mb-2">
                        プレイリスト: {{ App\Playlist::where('user_id', '=', Auth::user()->id)->count() }}件
                    </p>
                </div>
                @if ( strpos(explode('?', str_replace(url('/'),"",\Request::fullUrl()))[0], '/music/search') !== false )
                <div class="p-4 mb-3">
                    <form method="POST" action="{{ url('music/search') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4 my-2">{{ __('Title') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="title" name="title" value="{{(isset($request) && isset($request['title'])) ? $request['title'] : ''}}" placeholder="{{ __('Title') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Artist') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="artist" name="artist" value="{{(isset($request) && isset($request['artist'])) ? $request['artist'] : ''}}" placeholder="{{ __('Artist') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Album') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="album" name="album" value="{{(isset($request) && isset($request['album'])) ? $request['album'] : ''}}" placeholder="{{ __('Album') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Track number') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="number" class="form-control" id="track_num" name="track_num" value="{{(isset($request) && isset($request['track_num'])) ? $request['track_num'] : ''}}" placeholder="{{ __('Track number') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Genre') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="genre" name="genre" list="genre_list" value="{{(isset($request) && isset($request['genre'])) ? $request['genre'] : ''}}" placeholder="{{ __('Genre') }}">
                                <datalist id="genre_list">
                                    @foreach($genre_list as $index => $name)
                                    <option value="{{ $name }}">{{ $name}} </option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Original Artist') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="originalArtist" name="originalArtist" value="{{(isset($request) && isset($request['originalArtist'])) ? $request['originalArtist'] : ''}}" placeholder="{{ __('Original Artist') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Playtime') }}</div>
                            <div class="col-sm-6 my-2">
                                <input type="number" class="form-control" id="playtime_seconds_min" name="playtime_seconds_min" value="{{(isset($request) && isset($request['playtime_seconds_min'])) ? $request['playtime_seconds_min'] : ''}}" placeholder="{{ __('Playtime(min)') }}">
                            </div>
                            <div class="col-sm-2 my-2 d-flex align-items-end">{{ __('sec') }}</div>

                            <div class="col-sm-6 my-2 offset-sm-4">
                                <input type="number" class="form-control" id="playtime_seconds_max" name="playtime_seconds_max" value="{{(isset($request) && isset($request['playtime_seconds_max'])) ? $request['playtime_seconds_max'] : ''}}" placeholder="{{ __('Playtime(max)') }}">
                            </div>
                            <div class="col-sm-2 my-2 d-flex align-items-end">{{ __('sec') }}</div>

                            <div class="col-sm-4 my-2">{{ __('Related works') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="related_works" name="related_works" value="{{(isset($request) && isset($request['related_works'])) ? $request['related_works'] : ''}}" placeholder="{{ __('Related works') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Year') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="number" class="form-control" id="year" name="year" value="{{(isset($request) && isset($request['year'])) ? $request['year'] : ''}}" placeholder="{{ __('Year') }}">
                            </div>
                            <div class="col-sm-4 my-2">{{ __('Created at') }}</div>
                            <div class="col-sm-8 my-2">
                                <input type="text" class="form-control" id="created_at" name="created_at" value="{{(isset($request) && isset($request['created_at'])) ? $request['created_at'] : ''}}" placeholder="{{ (new DateTime())->format('Y-m') }}">
                            </div>
                            <div class="mt-5 col-sm-4 my-2">{{ __('Sort order') }}</div>
                            <div class="mt-5 col-sm-8 my-2">
                                <select id="sort_key" name="sort_key">
                                    @foreach($sort_list as $index => $name)
                                        <option value="{{ $name }}" @if(old('sort_list')==$name) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-3 col-sm-8 offset-sm-4">
                                <button type="submit" name="submit" class="btn btn-outline-primary">{{ __('Search') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
                @else
                <div class="p-4 mb-3">
                    <h4 class="font-italic">曲</h4>

                    <h5>カバーアート</h5>
                    <div class="cover-cloud mb-3">
                        @foreach (App\Music::select('cover')->where('user_id', Auth::user()->id)->groupBy('cover')->having('cover', '<>', '')->inRandomOrder()->get() as $key => $music)
                            <div class="cover-cloud-item" style="display:inline;">
                                <a href="#" onclick="event.preventDefault();document.getElementById('music-search-cover-form-{{ $key }}').submit();">
                                    <img src="{{route('home')}}{{ $music->cover }}" class="img-thumbnail cover-cloud-item-thumbnail">
                                </a>
                                <form id="music-search-cover-form-{{ $key }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="cover" value="{{ $music->cover }}">
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <h5>最近追加</h5>
                    <ul class="list-group mb-3">
                        @foreach (App\Music::where('user_id', Auth::user()->id)->latest()->limit(5)->get() as $music)
                        <li class="list-group-item"><a href="{{ url('music/'.$music->id) }}">{{ $music->artist }} / {{ $music->title }}</a></li>
                        @endforeach
                    </ul>

                    <h5>登録年月</h5>
                    <ul class="list-group mb-3">
                        @for ($i = 0; $i > -6; $i--)
                            <li class="list-group-item">
                                <a href="#" onclick="event.preventDefault();document.getElementById('music-search-createdat-form-{{ $i }}').submit();">
                                    {{ (new DateTime())->modify($i.' month')->format('Y年m月') }}
                                </a>
                                <form id="music-search-createdat-form-{{ $i }}" action="{{ url('music/search') }}" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="created_at" value="{{ (new DateTime())->modify($i.' month')->format('Y-m') }}">
                                </form>
                            </li>
                        @endfor
                    </ul>
                </div>
                <div class="p-4 mb-3">
                    <h4 class="font-italic">プレイリスト</h4>
                    <h5>最近追加</h5>
                    <ul class="list-group mb-3">
                        @foreach (App\Playlist::latest()->limit(3)->get() as $playlist)
                            <li class="list-group-item"><a href="{{ url('playlist/'.$playlist->id) }}">{{ $playlist->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="p-4 mb-3">
                    <h4 class="font-italic">今日は何の日？</h4>
                    <div class="row">
                        <div class="col-6 offset-3" id="month-calendar-current"></div>
                    </div>
                </div>

                <div class="p-4 mb-3">
                    <h4 class="font-italic">チャート</h4>
                    <div class="row">
                        <div class="embed-responsive embed-responsive-1by1">
                            <iframe class="embed-responsive-item" src="https://spotifycharts.com/regional/jp/daily/latest"></iframe>
                        </div>
                    </div>
                </div>

                @endif
            </aside>
            @endcan
        </div>
    </div>
</body>

</html>