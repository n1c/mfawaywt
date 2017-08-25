@extends('app')

@section('title') Comment {{ $comment->reddit_id }} by {{ $comment->user->name }} | {{ config('app.name') }} @endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-8 col-lg-offset-2">
            <div class="page-header">
                <h1>
                    <a href="/p/{{ $comment->post->reddit_id }}">
                        {{ $comment->post->title }} {{ $comment->post->created_at->format('Y') }}
                    </a>

                    <small class="pull-right">
                        <a href="{{ $comment->post->url }}{{ $comment->reddit_id }}"><span class="glyphicon glyphicon-link"></span></a>
                    </small>
                </h1>
            </div>

            @include('partials.comment_list_item', [ 'c' => $comment, 'options' => [ 'showExternal' => false, ] ])
        </div>
    </div>
@endsection
