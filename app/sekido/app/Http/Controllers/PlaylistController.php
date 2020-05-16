<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Music;
use App\Playlist;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());

        $playlists = Playlist::where('user_id', Auth::user()->id)->get();
        Log::debug('playlists: ' . $playlists);
        return view('playlist.index', ['playlists' => $playlists]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());

        $musics = Music::where('user_id', Auth::user()->id)->orderBy('title', 'asc')->get();
        Log::debug('musics: ' . $musics);

        return view('playlist.create', ['musics' => $musics]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$request: '.$request);

        $playlist = new Playlist;

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            Log::debug('cover: ' . print_r($cover, true));
            if ($cover->isValid([])) {
                Log::debug('cover: ' . print_r($cover, true));
                $stored = basename($cover->store('covers'));
                Log::debug('stored: ' . $stored);

                $path = $cover->path();
                Log::debug('path: ' . $path);
                $playlist->cover = '/c/' . $stored;
            }
        }

        $playlist->description = $request->description;
        $playlist->title = $request->title;
        $playlist->user_id = Auth::user()->id;
        $playlist->save();

        $musics = $request->musics ?? [];
        unset($request['musics']);
        if(count($musics)>0){
            Log::debug('id: ' . print_r($playlist->id, true));
            // Log::debug('musics: ' . print_r($musics, true));
            // Log::debug('playlist->musics: ' . print_r($playlist->musics, true));

            if(0 === count($musics)){
                $new_music = array();
            }else{
                $new_music = (array)$musics;
            }

            if(0 === count($playlist->musics)){
                $old_music = array();
            } else {
                foreach ($playlist->musics as $value) {
                    $old_music[] = $value->id;
                }
            }

            Log::debug('old_music: ' . print_r($old_music, true));
            Log::debug('new_music: ' . print_r($new_music, true));

            $add = array_diff($new_music, $old_music);
            $remove = array_diff($old_music, $new_music);
            Log::debug('add: ' . print_r($add, true));
            Log::debug('remove: ' . print_r($remove, true));

            $playlist->saveMusics($playlist->id, $add, $remove);
        }

        return redirect('playlist');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$id: '.$id);

        $playlist = Playlist::where('id', $id)->first();
        return view('playlist.show', ['playlist' => $playlist]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$id: '.$id);

        $musics = Music::where('user_id', Auth::user()->id)->orderBy('title', 'asc')->get();
        Log::debug('musics: ' . $musics);

        $playlist = Playlist::where('id', $id)->first();
        $playlist_musics = array();
        foreach ($playlist->musics as $value) {
            $playlist_musics[] = $value->id;
        }
        Log::debug('playlist_musics: ' . print_r($playlist_musics, true));

        return view('playlist.edit', ['playlist' => $playlist, 'musics' => $musics, 'playlist_musics' => $playlist_musics]);
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
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$request: '.$request);
        Log::debug('$id: '.$id);

        $playlist = Playlist::where('id', $id)->first();
        if($playlist){

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                Log::debug('cover: ' . print_r($cover, true));
                if ($cover->isValid([])) {
                    Log::debug('cover: ' . print_r($cover, true));
                    $stored = basename($cover->store('covers'));
                    Log::debug('stored: ' . $stored);

                    $path = $cover->path();
                    Log::debug('path: ' . $path);
                    $playlist->cover = '/c/' . $stored;
                }
            }

            $playlist->title = $request->title ?? '';
            $playlist->description = $request->description ?? '';


            $musics = $request->musics ?? [];
            unset($request['musics']);
            if(count($musics)>0){
                Log::debug('id: ' . print_r($id, true));
                // Log::debug('musics: ' . print_r($musics, true));
                // Log::debug('playlist->musics: ' . print_r($playlist->musics, true));

                if(0 === count($musics)){
                    $new_music = array();
                }else{
                    $new_music = (array)$musics;
                }

                if(0 === count($playlist->musics)){
                    $old_music = array();
                } else {
                    foreach ($playlist->musics as $value) {
                        $old_music[] = $value->id;
                    }
                }

                Log::debug('old_music: ' . print_r($old_music, true));
                Log::debug('new_music: ' . print_r($new_music, true));

                $add = array_diff($new_music, $old_music);
                $remove = array_diff($old_music, $new_music);
                Log::debug('add: ' . print_r($add, true));
                Log::debug('remove: ' . print_r($remove, true));

                $playlist->saveMusics($id, $add, $remove);
            }

        return view('playlist.show', ['playlist' => $playlist]);

        }else{
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['playlist' => '更新できませんでした']);
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
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$id: '.$id);

        $playlist = Playlist::where('id', $id)->first();
        $playlist->delete();
        return redirect('playlist');
    }
}
