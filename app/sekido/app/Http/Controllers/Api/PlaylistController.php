<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Music;
use App\Playlist;

class PlaylistController extends Controller
{
    public function add($playlist_id, $music_id)
    {
        Log::debug('add');
        $playlist = Playlist::where('id', $playlist_id)->first();
        Log::debug('playlist: ' . $playlist);
        $music = Music::where('user_id', Auth::user()->id)->where('id', $music_id)->first();
        Log::debug('musics: ' . $music);

        if($playlist !== null && $music !== null ){
            $new_music = [$music_id];

            if(0 === count($playlist->musics)){
                $old_music = array();
            } else {
                foreach ($playlist->musics as $value) {
                    $old_music[] = $value->id;
                }
            }

            $add = array_diff($new_music, $old_music);
            $remove = array_diff($old_music, $new_music);
            Log::debug('add: ' . print_r($add, true));
            Log::debug('remove: ' . print_r($remove, true));

            return response()->json(
                $playlist->saveMusics($playlist_id, $add, [])
            );
        } else {
            return response()->json([
                'errors' => 'No playlist or music was found.'
            ], 404);
        }
    }

    public function remove($playlist_id, $music_id)
    {
        Log::debug('add');
        $playlist = Playlist::where('id', $playlist_id)->first();
        Log::debug('playlist: ' . $playlist);
        $music = Music::where('user_id', Auth::user()->id)->where('id', $music_id)->first();
        Log::debug('musics: ' . $music);

        if($playlist !== null && $music !== null ){
            $new_music = [$music_id];

            if(0 === count($playlist->musics)){
                $old_music = array();
            } else {
                foreach ($playlist->musics as $value) {
                    $old_music[] = $value->id;
                }
            }

            $add = array_diff($new_music, $old_music);
            $remove = array_diff($old_music, $new_music);
            Log::debug('add: ' . print_r($add, true));
            Log::debug('remove: ' . print_r($remove, true));

            return response()->json(
                $playlist->saveMusics($playlist_id, [], $remove)
            );
        } else {
            return response()->json([
                'errors' => 'No playlist or music was found.'
            ], 404);
        }
    }
}
