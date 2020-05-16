@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                @auth
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in as
                    <span>
                        @can('system-only') {{-- システム管理者権限のみに表示される --}}
                        the System Administrator
                        @elsecan('admin-higher') {{-- 管理者権限以上に表示される --}}
                        the Administrator
                        @elsecan('user-higher') {{-- 一般権限以上に表示される --}}
                        a user
                        @endcan
                    </span>.
                @else
                You are not logged in. <a href="{{ route('login') }}">{{ __('Login') }}</a>
                @endauth
                </div>
                    <div class="card-body">
                        <a href="{{ url('/music') }}">{{ __('Music') }}</a>
                    </div>
                    @can('admin-higher') {{-- 管理者権限以上に表示される --}}
                        <div class="card-body">
                            <a href="{{ url('music/upload') }}">{{ __('Upload') }}</a>
                        </div>
                    @endcan
            </div>
        </div>
    </div>
</div>
@endsection
