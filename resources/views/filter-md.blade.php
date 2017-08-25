<?php $count = 1; ?>

@foreach ($comments as $c)
{{ $count }}. [Post]({{ $c->post->url }}{{ $c->reddit_id }}) by *{{ $c->user->name }}* (+{{ $c->score }})

<?php $icount = 1; ?>
@foreach ($c->images as $i)
    [Image {{ $icount++ }}]({{ $i->src }})
@endforeach

<?php $count++; ?>
@endforeach
