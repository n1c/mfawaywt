@extends('app')

@section('title') Filter posts | {{ config('app.name') }} @endsection

@section('content')
    <div class="row">
        <div class="col-sm-4 col-md-3">
            <form class="well" action="">
              <div class="form-group">
                <label>Start</label>
                <input type="date" name="start" class="form-control" placeholder="Start date" value="{{ $start->format('Y-m-d') }}">
              </div>

              <div class="form-group">
                <label>End</label>
                <input type="date" name="end" class="form-control" placeholder="End date" value="{{ $end->format('Y-m-d') }}">
              </div>

              <div class="form-group">
                <label>Vote limit</label>
                <input type="number" name="vote_limit" class="form-control" placeholder="Vote limit" value="{{ $voteLimit }}" min="0" step="1">
              </div>

              <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </form>
        </div>

        <div class="col-sm-8 col-md-9">
            @foreach ($comments as $c)
                @include('partials.comment_list_item', [ 'c' => $c ])
                <hr>
            @endforeach
        </div>
    </div>
@endsection
