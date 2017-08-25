<?php $options = $options ?? []; ?>
<div class="row vertical-align">
    <div class="col-sm-6" style="min-height: 200px;">
        @if ($c->firstImage()->src)
            <a href="{{ $c->firstImage()->src }}" target="_blank">
              <img class="img-responsive lazy" src="" data-src="{{ $c->firstimage()->getSrc('normal') }}" alt="">
            </a>
        @else
            <img class="img-responsive" src="http://placehold.it/125x125?text=?">
        @endif
    </div>

    <div class="col-sm-6">
        <h4>
            @if (array_get($options, 'showExternal', true))
                <a href="{{ $c->post->url }}{{ $c->reddit_id }}" target="_blank" class="pull-right">
                    <span class="glyphicon glyphicon-link"></span>
                </a>
            @endif

            <a href="{{ route('comment', [ 'reddit_id' => $c->post->reddit_id, 'comment_id' => $c->reddit_id ]) }}">
                {{ $c->score }}
            </a>
            <span class="glyphicon glyphicon-arrow-up"></span>
            <small>by /u/</small><a href="{{ route('user', [ 'name' => $c->user->name ]) }}">{{ $c->user->name }}</a>
        </h4>

        {!! $c->body_html !!}

        @if (count($c->images) > 1)
            <ul class="list-unstyled list-inline">
                @for ($i = 1; $i < count($c->images); $i++)
                    <li>
                        <a href="{{ $c->images[$i]->src }}" target="_blank">
                            <img class="lazy" src="" data-src="{{ $c->images[$i]->getSrc('small') }}" width="120">
                        </a>
                    </li>
                @endfor
            </ul>
        @endif
    </div>
</div>
