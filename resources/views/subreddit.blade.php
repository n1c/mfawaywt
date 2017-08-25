@extends('app')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 text-center">
            <div class="page-header">
                <h1>{{ config('app.name') }}</h1>
            </div>
        </div>
    </div>

    @foreach ($posts as $post)
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 text-center">
                <a href="{{ route('post', [ 'reddit_id' => $post->reddit_id ]) }}">
                    <h2>{{ $post->title }}</h2>
                </a>
            </div>
        </div>

        <div class="row">
            @foreach($post->comments->take(4) as $comment)
                <div class="col-xs-3">
                    <a href="{{ route('comment', [ 'reddit_id' => $post->reddit_id, 'comment_id' => $comment->reddit_id ]) }}" class="thumbnail">
                        <img class="img-responsive lazy" src="" data-src="{{ $comment->firstimage()->getSrc('small') }}" alt="">
                    </a>
                </div>
            @endforeach
        </div>

        <hr>
    @endforeach

    <div class="row">
        <div class="col-sm-12 text-center">
            {!! $posts->links() !!}
        </div>
    </div>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 text-center">
            <hr>
            fin.
        </div>
    </div>
@endsection
