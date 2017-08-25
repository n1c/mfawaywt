@extends('app')

@section('title') {{ $post->title }} | {{ config('app.name') }} @endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-8 col-lg-offset-2">
            <div class="page-header">
                <h1>
                    /r/{{ $post->subreddit->slug }}: {{ $post->title }} {{ $post->created_at->format('Y') }}
                </h1>
            </div>

            @foreach ($post->comments as $c)
                @include('partials.comment_list_item', [ 'c' => $c ])
                <hr>
            @endforeach
        </div>
    </div>
@endsection
