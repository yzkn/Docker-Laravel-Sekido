<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Playlist extends Model
{
    protected $guarded = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function musics()
    {
        return $this->belongsToMany('App\Music');
    }

    public function saveMusics(int $id, array $add, array $remove)
    {
        $playlist = $this->find($id);
        if(count($remove)){
            $playlist->musics()->detach($remove);
        }
        if(count($add)){
            $playlist->musics()->attach($add);
        }

        $playlist = Playlist::where('id', $id)->with('musics')->first();
        return $playlist;
    }
}
