<?php
// $ php artisan make:model Music && php artisan make:controller UserController --resource

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Music;
use App\Playlist;

class MusicController extends Controller
{
    public function upload()
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());

        $playlists = Playlist::where('user_id', Auth::user()->id)->orderBy('title', 'asc')->get();
        Log::debug('playlists: ' . $playlists);

        return view('music.upload', ['playlists' => $playlists]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);

        $user = Auth::user();
        $token = auth('api')->login($user);
        Log::debug('token: ' . $token);

        $musics = Music::where('user_id', Auth::user()->id)->get();
        Log::debug('musics: ' . $musics);
        // return view('music.index', ['musics' => $musics]);
        return response()
            ->view('music.index', ['musics' => $musics])
            ->cookie('jwttoken', $token, 30);
    }

    public function search(Request $request)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);

        $getID3 = new \getID3();
        $genre_list = \getid3_id3v1::ArrayOfGenres();
        // Log::debug('$genre_list: ' . join(" ", $genre_list));
        $sort_list = [
            'album', 'artist', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num', 'playtime_seconds', '-album', '-artist', '-created_at', '-genre', '-originalArtist', '-related_works', '-title', '-year', '-track_num', '-playtime_seconds', 'artist title', '-artist title', 'artist album title', '-artist album title', 'artist album track_num', '-artist album track_num', 'random'
        ];

        $query = Music::query();
        $query->where('user_id', Auth::user()->id);
        // 'playtime_seconds_min', 'playtime_seconds_max'は別途
        foreach ($request->only(['album', 'artist', 'cover', 'document', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num']) as $key => $value) {
            if (($request->all($key))) {
                $query->where($key, 'like', '%' . $value . '%');
            }
        }

        if ($request->has('playtime_seconds_min') && ($request->playtime_seconds_min)) {
            Log::debug('playtime_seconds_min: ' . $request->playtime_seconds_min);
            $query->where('playtime_seconds', '>=', $request->playtime_seconds_min);
        }
        if ($request->has('playtime_seconds_max') && ($request->playtime_seconds_min)) {
            $query->where('playtime_seconds', '<=', $request->playtime_seconds_max);
        }

        if ($request->has('sort_key') && ($request->sort_key)) {
            if ('artist album title' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'asc')->orderBy('album', 'asc')->orderBy('title', 'asc');
            } else if ('-artist album title' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'desc')->orderBy('album', 'desc')->orderBy('title', 'desc');
            } else if ('artist album track_num' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'asc')->orderBy('album', 'asc')->orderBy('track_num', 'asc');
            } else if ('-artist album track_num' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'desc')->orderBy('album', 'desc')->orderBy('track_num', 'desc');
            } else if ('artist title' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'asc')->orderBy('title', 'asc');
            } else if ('-artist title' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy('artist', 'desc')->orderBy('title', 'desc');
            } else if (
                in_array(
                    $request->sort_key,
                    [
                        'album', 'artist', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num', 'playtime_seconds',
                        '-album', '-artist', '-created_at', '-genre', '-originalArtist', '-related_works', '-title', '-year', '-track_num', '-playtime_seconds'
                    ]
                )
            ) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->orderBy($request->sort_key, (strpos($request->sort_key, '-') === 0) ? 'desc' : 'asc');
            } else if ('random' === $request->sort_key) {
                Log::debug('sort_key: ' . $request->sort_key);
                $query->inRandomOrder(strtotime('today'));
            }
        }

        $musics = $query->get();

        Log::debug('request: ' . print_r($request->only(['album', 'artist', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num', 'playtime_seconds_min', 'playtime_seconds_max', 'sort_key']), true));

        return view('music.index', ['musics' => $musics, 'genre_list' => $genre_list, 'sort_list' => $sort_list, 'request' => $request->only(['album', 'artist', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num', 'playtime_seconds_min', 'playtime_seconds_max', 'sort_key'])]);
    }

    public function searchform(Request $request)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);

        $getID3 = new \getID3();
        $genre_list = \getid3_id3v1::ArrayOfGenres();
        $sort_list = [
            'album', 'artist', 'created_at', 'genre', 'originalArtist', 'related_works', 'title', 'year', 'track_num', 'playtime_seconds', '-album', '-artist', '-created_at', '-genre', '-originalArtist', '-related_works', '-title', '-year', '-track_num', '-playtime_seconds', 'artist title', '-artist title', 'artist album title', '-artist album title', 'random'
        ];

        return view('music.index', ['musics' => array(), 'genre_list' => $genre_list, 'sort_list' => $sort_list, 'request' => $request]);
    }

    public function list(Request $request)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);

        $column = 'artist';
        if (in_array(
            $request->column,
            [
                'album', 'artist', 'genre', 'originalArtist', 'related_works', 'title', 'track_num', 'year'
            ]
        )) {
            $column = $request->column;
        }

        if('local'===config('filesystems.default', 'pgsql')){
            $musics = Music::where('user_id', Auth::user()->id)->selectRaw($column . ', count(*) AS count, sum( to_number( playtime_seconds , \'99G999D9S\') ) AS playtime_sum')->groupBy($column)->having($column, '<>', '')->get();
        } else { // } else if('local'===config('filesystems.default', 'mysql')){
            $musics = Music::where('user_id', Auth::user()->id)->selectRaw($column . ', count(*) AS count, sum( cast( playtime_seconds as signed) ) AS playtime_sum')->groupBy($column)->having($column, '<>', '')->get();
        }

        Log::debug('musics: ' . $musics);
        return view('music.list', ['column' => $column, 'list_items' => $musics]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());

        return redirect('music');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);

        $FROM_ENC = 'ASCII,JIS,UTF-8,EUC-JP,SJIS';

        $validatedData = $request->validate([
            // 'title' => 'required|unique:musics|max:255',
            'audios.*' => 'mimes:mp3,mpga,jpeg,png',
        ]);

        if ($request->hasFile('audios')) {
            $image_path = '';
            foreach ($request->file('audios') as $index => $audio) {
                if ($audio->isValid()) {
                    if (strpos($audio->getMimeType(), 'image') === 0) {
                        Log::debug('audio: ' . print_r($audio, true));
                        $stored = basename($audio->store('covers'));
                        Log::debug('stored: ' . $stored);

                        $image_path = '/c/' . $stored;
                        Log::debug('image_path: ' . $image_path);
                        break;
                    }
                }
            }

            foreach ($request->file('audios') as $index => $audio) {
                if ($audio->isValid()) {
                    Log::debug('audio: ' . print_r($audio, true));
                    $stored = basename($audio->store('musics'));
                    Log::debug('stored: ' . $stored);

                    if (strpos($audio->getMimeType(), 'audio') === 0) {
                        Log::debug('path: ' . $audio->path());

                        $getID3 = new \getID3();
                        $tag = $getID3->analyze($audio->path());

                        $music = new Music;

                        $music->path = '/m/' . $stored;

                        $music->album = mb_convert_encoding($tag['id3v2']['comments']['album'][0], 'UTF-8', $FROM_ENC) ?? '';
                        $music->artist = mb_convert_encoding($tag['id3v2']['comments']['artist'][0], 'UTF-8', $FROM_ENC) ?? '';
                        $music->bitrate = $tag['bitrate'] ?? '';
                        $music->cover = $image_path ?? '';
                        $music->document = '';
                        $music->genre = mb_convert_encoding($tag['id3v2']['comments']['genre'][0], 'UTF-8', $FROM_ENC) ?? '';
                        $music->originalArtist = '';
                        $music->playtime_seconds = $tag['playtime_seconds'] ?? '';
                        $music->related_works = $request->related_works ?? '';
                        $music->title = mb_convert_encoding($tag['id3v2']['comments']['title'][0], 'UTF-8', $FROM_ENC) ?? '';
                        $music->track_num = $tag['id3v2']['comments']['track_number'][0] ?? '';
                        $music->year = $tag['id3v2']['comments']['recording_time'][0] ?? '';
                        $music->user_id = Auth::user()->id;
                        $music->save();

                        $id = $music->id; // 挿入されたmusicのID
                        $playlists = $request->playlists ?? [];
                        unset($request['playlists']);
                        if (count($playlists) > 0) {
                            Log::debug('id: ' . print_r($id, true));
                            // Log::debug('playlists: ' . print_r($playlists, true));
                            // Log::debug('music->playlists: ' . print_r($music->playlists, true));

                            if (0 === count($playlists)) {
                                $new_playlist = array();
                            } else {
                                $new_playlist = (array) $playlists;
                            }

                            if (0 === count($music->playlists)) {
                                $old_playlist = array();
                            } else {
                                foreach ($music->playlists as $value) {
                                    $old_playlist[] = $value->id;
                                }
                            }

                            Log::debug('old_playlist: ' . print_r($old_playlist, true));
                            Log::debug('new_playlist: ' . print_r($new_playlist, true));

                            $add = array_diff($new_playlist, $old_playlist);
                            $remove = array_diff($old_playlist, $new_playlist);
                            Log::debug('add: ' . print_r($add, true));
                            Log::debug('remove: ' . print_r($remove, true));

                            $music->savePlaylists($id, $add, $remove);
                        }
                    }
                }
            }
        }

        return redirect('music');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$id: ' . $id);

        $music = Music::where('user_id', Auth::user()->id)->where('id', $id)->first();
        return view('music.show', ['music' => $music]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$id: ' . $id);

        $getID3 = new \getID3();
        $genre_list = \getid3_id3v1::ArrayOfGenres();

        $playlists = Playlist::where('user_id', Auth::user()->id)->orderBy('title', 'asc')->get();
        Log::debug('playlists: ' . $playlists);

        $music = Music::where('user_id', Auth::user()->id)->where('id', $id)->first();
        $music_playlists = array();
        foreach ($music->playlists as $value) {
            $music_playlists[] = $value->id;
        }
        Log::debug('music_playlists: ' . print_r($music_playlists, true));
        return view('music.edit', ['music' => $music, 'genre_list' => $genre_list, 'playlists' => $playlists, 'music_playlists' => $music_playlists]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$request: ' . $request);
        Log::debug('$id: ' . $id);

        $validatedData = $request->validate([
            // 'title' => 'required|unique:musics|max:255',
            'cover' => 'mimes:jpeg,png|dimensions:min_width=1,min_height=1,max_width=500,max_height=500',
            'document' => 'mimes:txt,pdf',
        ]);

        $music = Music::where('user_id', Auth::user()->id)->where('id', $id)->first();
        if ($music) {
            // Log::debug('music: ' . print_r($music, true));
            Log::debug('music: -');
            $older_music_cover = $music->cover;

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                Log::debug('cover: ' . print_r($cover, true));
                if ($cover->isValid([])) {
                    Log::debug('cover: ' . print_r($cover, true));
                    $stored = basename($cover->store('covers'));
                    Log::debug('stored: ' . $stored);

                    $path = $cover->path();
                    Log::debug('path: ' . $path);
                    $music->cover = '/c/' . $stored;
                }
            }

            if ($request->hasFile('document')) {
                $document = $request->file('document');
                Log::debug('document: ' . print_r($document, true));
                if ($document->isValid([])) {
                    Log::debug('document: ' . print_r($document, true));
                    $stored = basename($document->store('documents'));
                    Log::debug('stored: ' . $stored);

                    $path = $document->path();
                    Log::debug('$path: ' . $path);
                    $older_document_path = $music->document;
                    Log::debug('$older_document_path: ' . $older_document_path);
                    $music->document = '/d/' . $stored;
                    Log::debug('$music->document: ' . $music->document);

                    $targetFile = uniqid();
                    $inputStream = Storage::getDriver()->readStream('documents/' . $stored);
                    Log::debug('$targetFile: '.$targetFile);
                    Log::debug('$inputStream: '.$inputStream);
                    Storage::disk('local')->getDriver()->writeStream($targetFile, $inputStream);

                    if(Storage::disk('local')->exists($targetFile)){
                        $targetFilecontents = Storage::disk('local')->get($targetFile);
                        // Log::debug('$targetFilecontents: '.$targetFilecontents);
                        $magick_input_file = storage_path('app').DIRECTORY_SEPARATOR.$targetFile;
                        $magick_output_file = $magick_input_file . '.png';
                        Log::debug('$magick_input_file: '.$magick_input_file);
                        Log::debug('$magick_output_file: '.$magick_output_file);

                        $convert_command = 'convert'; // Linux
                        if (DIRECTORY_SEPARATOR == '\\') {
                            $convert_command = 'magick'; // Windows
                        }
                        $shell_cmd = $convert_command.' -density 400 "' . $magick_input_file . '[0]" "' . $magick_output_file .  '"';
                        Log::debug('shell_cmd: ' . $shell_cmd);
                        $output = shell_exec($shell_cmd);
                        Log::debug('output: ' . print_r($output, true));

                        Log::debug('output::');
                        $inputStream2 = Storage::disk('local')->getDriver()->readStream($targetFile.'.png');
                        Log::debug('$inputStream2: '.$targetFile.'.png');
                        $targetFilePath2 = 'documents/' . $stored . '.png';
                        Log::debug('$targetFilePath2: '.$targetFilePath2);
                        Storage::getDriver()->writeStream($targetFilePath2, $inputStream2);
                        Log::debug("Storage::disk('local')->getDriver()->writeStream($targetFilePath2, $inputStream2)");

                        Storage::disk('local')->delete($targetFile);
                        Storage::disk('local')->delete($targetFile.'.png');
                    }

                    if (Storage::exists($magick_output_file)) {
                        if (null === $music->cover || '' === $music->cover || (strrpos($older_music_cover, '.pdf.png') === strlen($older_music_cover) - strlen('.pdf.png'))) {
                            $music->cover = $music->document . '.png';
                        }
                    }
                }
            }

            $music->title = $request->title ?? '';
            $music->artist = $request->artist ?? '';
            $music->album = $request->album ?? '';
            $music->track_num = $request->track_num ?? '';
            $music->bitrate = $request->bitrate ?? '';
            $music->genre = $request->genre ?? '';
            $music->originalArtist = $request->originalArtist ?? '';
            $music->playtime_seconds = $request->playtime_seconds ?? '';
            $music->related_works = $request->related_works ?? '';
            $music->year = $request->year ?? '';
            $music->save();

            $playlists = $request->playlists ?? [];
            unset($request['playlists']);
            if (count($playlists) > 0) {
                Log::debug('id: ' . print_r($id, true));
                // Log::debug('playlists: ' . print_r($playlists, true));
                // Log::debug('music->playlists: ' . print_r($music->playlists, true));

                if (0 === count($playlists)) {
                    $new_playlist = array();
                } else {
                    $new_playlist = (array) $playlists;
                }

                if (0 === count($music->playlists)) {
                    $old_playlist = array();
                } else {
                    foreach ($music->playlists as $value) {
                        $old_playlist[] = $value->id;
                    }
                }

                Log::debug('old_playlist: ' . print_r($old_playlist, true));
                Log::debug('new_playlist: ' . print_r($new_playlist, true));

                $add = array_diff($new_playlist, $old_playlist);
                $remove = array_diff($old_playlist, $new_playlist);
                Log::debug('add: ' . print_r($add, true));
                Log::debug('remove: ' . print_r($remove, true));

                $music->savePlaylists($id, $add, $remove);
            }

            return redirect('music/' . $music->id)->with('success', '更新しました。');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['music' => '更新できませんでした']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::debug(get_class($this) . ' ' . __FUNCTION__ . '()');
        Log::debug('User: ' . Auth::user());
        Log::debug('$id: ' . $id);

        $music = Music::where('user_id', Auth::user()->id)->where('id', $id)->first();
        $music->delete();
        return redirect('music');
    }
}
