@extends('app')

@section('title') Posts by {{ $user->name }} | {{ config('app.name') }} @endsection

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    Posts by <small>/u/</small>{{ $user->name }}
                </h1>
            </div>

            @foreach ($user->comments as $c)
                @include('partials.comment_list_item', [ 'c' => $c ])
                <hr>
            @endforeach
        </div>
    </div>
@endsection
