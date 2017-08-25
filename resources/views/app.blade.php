<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta id="csrf_token" name="_token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <link href="{{ smix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '{{ config('services.google.analytics') }}', 'auto');
        ga('send', 'pageview');
    </script>

    <div class="container-fluid" id="app">
        <div class="row mt">
            <div class="col-xs-12">
                <ul class="list-unstyled list-inline pull-right">
                    <li><a href="{{ route('welcome') }}"><span class="glyphicon glyphicon-home"></span></a></li>
                    <li><a href="{{ route('filter') }}"><span class="glyphicon glyphicon-filter"></span></a></li>

                    @if (Auth::guest())
                        <li><a href="{{ route('connect') }}"><span class="glyphicon glyphicon-user"></span></a></li>
                    @else
                        <li><a href="{{ route('dashboard') }}"><span class="glyphicon glyphicon-cog"></span></a></li>
                    @endif
                </ul>
            </div>
        </div>

        @include('component.newsletter-subscribe')

        @yield('content')
    </div>

    <script type="text/javascript" src="{{ smix('js/app.js') }}"></script>

    <script>
        $('img.lazy').unveil(5);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        });

        var vglnk = { key: '{{ config('services.viglink.key') }}' };
        (function(d, t) {
            var s = d.createElement(t); s.type = 'text/javascript'; s.async = true;
            s.src = '//cdn.viglink.com/api/vglnk.js';
            var r = d.getElementsByTagName(t)[0]; r.parentNode.insertBefore(s, r);
        }(document, 'script'));
    </script>
    @stack('script')
</body>
</html>
