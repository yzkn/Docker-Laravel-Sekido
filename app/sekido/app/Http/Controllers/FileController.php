<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function get_mimetype_easily($path)
    {
        switch (substr($path, strrpos($path, '.') + 1)) {
            case 'pdf':
                $mimetype = 'application/pdf';
                break;
            case 'gif':
                $mimetype = 'image/gif';
                break;
            case 'jpeg':
                $mimetype = 'image/jpeg';
                break;
            case 'jpg':
                $mimetype = 'image/jpeg';
                break;
            case 'png':
                $mimetype = 'image/png';
                break;
            case 'tif':
                $mimetype = 'image/tiff';
                break;
            case 'tiff':
                $mimetype = 'image/tiff';
                break;
            case 'mp3':
                $mimetype = 'audio/mpeg';
                break;
            case 'mpga':
                $mimetype = 'audio/mpeg';
                break;
            default:
                $mimetype = '';
        }
        return $mimetype;
    }

    public function covers($path)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$path: '.$path);

        $path = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('covers').'/'.basename($path));
        Log::debug('$path: '.$path);

        if(Storage::exists($path)){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', $this->get_mimetype_easily($path));
            return $response;
        }else{
            abort(404);
        }
    }

    public function documents($path)
    {
        Log::debug(get_class($this).' '.__FUNCTION__.'()');
        Log::debug('User: '.Auth::user());
        Log::debug('$path: '.$path);

        $path = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('documents').'/'.basename($path));
        Log::debug('$path: '.$path);

        if(Storage::exists($path)){
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', $this->get_mimetype_easily($path));
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

        if(Storage::exists($path)) {
            $contents = Storage::get($path);
            // Log::debug('$contents: '.$contents);
            $response = Response::make($contents, 200);
            $response->header('Content-Type', $this->get_mimetype_easily($path));
            return $response;
        }else{
            abort(404);
        }
    }
}
