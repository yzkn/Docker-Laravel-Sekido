<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function covers($path)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$path: '.$path);

        $path = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('covers').'/'.basename($path));
        Log::debug('$path: '.$path);

        // if(file_exists($path) && $img_type = exif_imagetype($path)){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', 'image/jpeg');
            return $response;
        // }else{
        //     abort(404);
        // }
    }

    public function documents($path)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$path: '.$path);

        $path = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('documents').'/'.basename($path));
        Log::debug('$path: '.$path);

        $magic = file_get_contents($path, false, null, 0, 12);

        if(file_exists($path) && (strpos($magic, "%PDF-1") === 0)){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', 'application/pdf');
            return $response;
        }else if(file_exists($path) && $img_type = exif_imagetype($path)){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', image_type_to_mime_type($img_type));
            return $response;
        }else{
            abort(404);
        }
    }

    public function musics($path)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$path: '.$path);

        $path = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('musics').'/'.basename($path));
        Log::debug('$path: '.$path);

        // $getID3 = new \getID3();

        // // local
        // // $tag = $getID3->analyze($path);
        // // s3
        // $inputStream = Storage::disk('s3')->getDriver()->getAdapter()->getPathPrefix().'/'.$path;
        // $destination = Storage::disk('local')->getDriver()->readStream('/tmp/'.$path);
        // Storage::disk('s3')->getDriver()->putStream($destination, $inputStream);
        // $tag = $getID3->analyze($destination);
        // //

        // Log::debug('$tag: '.print_r($tag, true));//[fileformat]

        // if(file_exists($path) && ('mp3' === $tag['fileformat'])){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', 'audio/mpeg');
            return $response;
        // }else{
        //     abort(404);
        // }
    }
}
