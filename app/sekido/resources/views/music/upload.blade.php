@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @can('admin-higher') {{-- 管理者権限以上に表示される --}}
            <div class="col-md-12">
                <div class="jumbotron my-5">
                    <h1 class="display-4 my-3">Upload</h1>
                    <hr class="my-4">
                    <div class="col mt-4">
                        <form method="POST" action="{{ url('music') }}" enctype="multipart/form-data" onSubmit="$('#submit').attr('disabled', true)">
                            @csrf
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-12 row justify-content-center my-5">
                                        <div class="input-group" title="音楽ファイルと一緒に画像ファイルを1つ指定することでカバーアートとして登録できます。">
                                            <label class="input-group-btn">
                                                <span class="btn btn-outline-primary">
                                                    Choose File<input type="file" style="display:none" name="audios[]" multiple>
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" readonly="">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 my-2">{{ __('Playlist') }}</div>
                                    <div class="col-sm-8 my-2">
                                        <select name="playlists[]" class="form-control" id="playlists" multiple>
                                            @foreach($playlists as $playlist)
                                                <option value="{{ $playlist->id }}">{{ $playlist->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4 my-2">{{ __('Related works') }}</div>
                                    <div class="col-sm-8 my-2">
                                        <input type="text" class="form-control" id="related_works" name="related_works" value="{{ old('related_works', isset($music) ? $music->related_works : '') }}" placeholder="{{ __('Related works') }}">
                                    </div>
                                    <div class="mt-5 col-sm-8 offset-sm-4">
                                        <button id="submit" name="submit" type="submit" class="btn btn-outline-primary">{{ __('Update') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</div>

<script>
    FileList.prototype.map = function(){
        return Array.prototype.map.call(this, ...arguments)
    }
    window.onload = function(){
        $(document).on('change', ':file', function() {
            var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = ((input.get(0).files.map(file => (file.name).replace(/\\/g, '/').replace(/.*\//, ''))).join(' , '));
            input.parent().parent().next(':text').val(label + ( numFiles < 2 ? '' : (' ほか 計' + numFiles + 'ファイル')));
        });
    }
</script>
@endsection
