@extends('app')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 text-center">
            <div class="page-header">
                <h1>{{ config('app.name') }}</h1>

                <ul class="list-unstyled list-inline">
                    @foreach ($subreddits as $sub)
                        <li>
                            <a href="{{ route('subreddit', $sub->slug) }}">{{ $sub->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    @foreach ($posts as $post)
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 text-center">
                <h2>
                    <small>/r/{{ $post->subreddit->slug }}</small>
                    <a href="{{ route('post', [ 'reddit_id' => $post->reddit_id ]) }}">
                        {{ $post->title }}
                    </a>
                </h2>
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
