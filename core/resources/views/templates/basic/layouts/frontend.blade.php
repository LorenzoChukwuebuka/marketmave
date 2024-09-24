<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $general->sitename(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/animate.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/owl.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/main.css')}}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/color.php?color='.$general->base_color.'&secondColor='.$general->secondary_color) }}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/custom.css')}}">

    @stack('style-lib')
    @stack('style')

</head>

<body>

    @stack('fbComment')

    <div class="overlay"></div>
    <a href="#0" class="scrollToTop"><i class="las la-angle-up"></i></a>

    <div class="preloader">
        <div id="preloader">
            <img class="icon" src="{{ asset($activeTemplateTrue .'images/loader.png') }}" alt="images">
        </div>
    </div>

    @include($activeTemplate.'partials.header')

    @include($activeTemplate.'partials.banner')

    @yield('content')

    @include($activeTemplate.'partials.footer')

    <script src="{{asset($activeTemplateTrue.'js/jquery-3.6.0.min.js')}}"></script>

    @stack('script-lib')

    <script src="{{asset($activeTemplateTrue.'js/bootstrap.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/magnific-popup.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/rafcounter.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/owl.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/nice-select.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/main.js')}}"></script>

    @include('partials.plugins')
    @include('partials.notify')

    @php
        $cookie = App\Models\Frontend::where('data_keys','cookie.data')->first();
        $favicon = getImage(imagePath()['logoIcon']['path'] .'/favicon.png');
    @endphp

    @if(@$cookie->data_values->status && !session('cookie_accepted'))
        <section class="cookie-policy bg-dark">
            <div class="container">
            <div class="cookie-wrapper">
                    <div class="cookie-cont text-white">
                        <span>
                            @php echo @$cookie->data_values->description @endphp
                        </span>
                        <a href="{{ @$cookie->data_values->link }}" class="text--base" target="_blank">@lang('Read Policy')</a>
                    </div>
                <a href="javascript:void(0)" class="btn--base cookie-btn cookie-close btn-sm">@lang('Accept Policy')</a>
            </div>
            </div>
        </section>
    @endif

    <!-- Particles Js -->
    <script src="{{asset($activeTemplateTrue.'js/particles.min.js')}}"></script>
    <script src="{{asset($activeTemplateTrue.'js/particles.php?favicon='.$favicon) }}"></script>
    <!-- Particles Js -->

    <script>
        (function($){
            "use strict";
            let navLink = $('.menu a');
            let currentRoute = '{{ url()->current() }}';

            $.each(navLink, function(index, value) {
                if(value.href == currentRoute){
                    $(value).addClass('active');
                }
            });

            $('.cookie-btn').on('click', function(){
                $.ajax({
                    method:'get',
                    url:'{{ route("cookie.accept") }}',
                    success:function(response){
                        if(response.success){
                            $('.cookie-policy').remove();
                            notify('success', response.message);
                        }
                    }
                });
            });

        })(jQuery);
    </script>

    @stack('script')

</body>
</html>
