@extends('app')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    Your comments
                </h1>
            </div>

            @foreach (Auth::user()->comments as $c)
                <div class="media" id="media_wrap_{{ $c->id }}">
                    <div class="media-body">
                        @if ($c->is_enabled)
                            <button class="btn btn-danger btn-sm btn-disable pull-right" data-id="{{ $c->id }}">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        @else
                            <button class="btn btn-primary btn-sm btn-enable pull-right" data-id="{{ $c->id }}">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        @endif

                        <h4 class="media-heading">
                            <a href="{{ $c->post->url }}{{ $c->reddit_id }}" target="_blank">
                                {{ $c->score }}
                            </a>
                            <span class="glyphicon glyphicon-arrow-up"></span>
                            <small>in</small>
                            <a href="{{ $c->post->url }}" target="_blank">{{ $c->post->title }}</a>
                        </h4>

                        {!! $c->body_html !!}

                        <ul class="list-unstyled list-inline">
                            @foreach ($c->images as $i)
                                <li>
                                    <a href="{{ $i->src }}" target="_blank">
                                        <img class="lazy" src="" data-src="{{ $i->getSrc('small') }}" width="50">
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <hr>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function () {
            $('.btn-disable').bind('click', function () {
                var $that = $(this);
                var id = $that.data('id');
                $('#media_wrap_' + id).fadeOut();
                $.post('/api/v1/comment/' + id + '/delete');
            });

            $('.btn-enable').bind('click', function () {
                var $that = $(this);
                var id = $that.data('id');

                // Swap the icon
                $that
                    .removeClass('btn-primary')
                    .addClass('btn-success')
                    ;

                $that.find('.glyphicon-plus')
                    .first()
                    .removeClass('glyphicon-plus')
                    .addClass('glyphicon-ok')
                    ;

                $.post('/api/v1/comment/' + id + '/enable');
            });
        });
    </script>
@endpush
