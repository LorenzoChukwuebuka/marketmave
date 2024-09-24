@extends($activeTemplate . 'layouts.auth_master')

@php
    $bgImage = getContent('auth_background_iamge.content', true);
    $policyPages = getContent('policy_pages.element');
@endphp

@section('content')

    <div class="account-section bg_img"
        data-background="{{ getImage('assets/images/frontend/auth_background_iamge/' . @$bgImage->data_values->image, '1920x1080') }}">
        <div class="account__section-wrapper">
            <div class="account__section-thumb">
                <div class="logo d-none d-lg-block">
                    <a href="{{ route('home') }}">
                        <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="@lang('logo')">
                    </a>
                </div>
            </div>
            <div class="account__section-content bg--title account__section-content-reg">
                <div class="w-100">
                    <div class="logo mb-5 d-lg-none">
                        <a href="{{ route('home') }}">
                            <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="@lang('logo')">
                        </a>
                    </div>
                    <div class="section__header text--white">
                        <h4 class="section__title mb-0">@lang('Sign Up')</h4>
                    </div>
                    <form class="account--form row g-4" action="{{ route('user.register') }}" method="POST"
                        onsubmit="return submitUserForm();">
                        @csrf

                        @if (session()->get('reference') != null)
                            <div class="col-sm-12">
                                <label for="referenceBy" class="form--label-2">@lang('Reference By')</label>
                                <input type="text" name="referBy" id="referenceBy" class="form--control-2"
                                    value="{{ session()->get('reference') }}" readonly>
                            </div>
                        @endif

                        <div class="col-sm-6">
                            <label for="firstname" class="form--label-2">@lang('First Name')</label>
                            <input id="firstname" type="text" class="form--control-2" name="firstname"
                                value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="lastname" class="form--label-2">@lang('Last Name')</label>
                            <input id="lastname" type="text" class="form--control-2" name="lastname"
                                value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form--label-2">@lang('Country')</label>
                            <select name="country" id="country" class="form--control-2">
                                @foreach ($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}"
                                        data-code="{{ $key }}">{{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form--label-2">@lang('Mobile')</label>
                            <div class="form-group">
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text mobile-code">
                                    </span>
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                                        class="form--control-2 checkUser">
                                </div>
                                <small class="text-danger mobileExist"></small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="username" class="form--label-2">@lang('Username')</label>
                            <input id="username" type="text" class="form--control-2 checkUser" name="username"
                                value="{{ old('username') }}" required>
                            <small class="text-danger usernameExist"></small>
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form--label-2">@lang('E-Mail Address')</label>
                            <input id="email" type="email" class="form--control-2 checkUser" name="email"
                                value="{{ old('email') }}" required>
                        </div>
                        <div class="col-sm-6 hover-input-popup">
                            <label for="password" class="form--label-2">@lang('Password')</label>
                            <input id="password" type="password" class="form--control-2" name="password" required>
                            @if ($general->secure_password)
                                <div class="input-popup">
                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                    <p class="error number">@lang('1 number minimum')</p>
                                    <p class="error special">@lang('1 special character minimum')</p>
                                    <p class="error minimum">@lang('6 character password')</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <label for="password-confirm" class="form--label-2">@lang('Confirm Password')</label>
                            <input id="password-confirm" type="password" class="form--control-2"
                                name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <div class="col-sm-12">
                            @php echo loadReCaptcha() @endphp
                        </div>
                        @include($activeTemplate . 'partials.custom_captcha')

                        @if ($general->agree)
                            <div class="col-sm-12">
                                <input class="form-check-input" type="checkbox" name="agree" id="agree">
                                <label for="agree" class="text--white">
                                    @lang('I agree with ')
                                    @foreach ($policyPages as $singlePolicy)
                                        <a href="{{ route('policy.details', ['policy' => slug($singlePolicy->data_values->title), 'id' => $singlePolicy->id]) }}"
                                            target="_blank">
                                            {{ __($singlePolicy->data_values->title) }}
                                        </a>
                                        {{ $loop->last ? null : ', ' }}
                                    @endforeach
                                </label>
                            </div>
                        @endif
                        <div class="col-sm-12">
                            <button type="submit" class="cmn--btn w-100">@lang('Sign Up')</button>
                        </div>
                    </form>
                    <div class="mt-5 text-center text--white">
                        @lang('Already have an Account') ? <a href="{{ route('user.login') }}"
                            class="text--base">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="existModalCenter" tabindex="-1" aria-labelledby="existModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center mt-2">@lang('You already have an account please Sign in')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cmn--btn btn--sm btn--danger"
                        data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--success">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('style')
    <style>
        .country-code .input-group-prepend .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }

        .hover-input-popup {
            position: relative;
        }

        .hover-input-popup:hover .input-popup {
            opacity: 1;
            visibility: visible;
        }

        .input-popup {
            position: absolute;
            bottom: 130%;
            left: 50%;
            width: 280px;
            background-color: #1a1a1a;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            opacity: 0;
            visibility: hidden;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
        }

        .input-popup::after {
            position: absolute;
            content: '';
            bottom: -19px;
            left: 50%;
            margin-left: -5px;
            border-width: 10px 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #1a1a1a transparent;
            -webkit-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .input-popup p {
            padding-left: 20px;
            position: relative;
        }

        .input-popup p::before {
            position: absolute;
            content: '';
            font-family: 'Line Awesome Free';
            font-weight: 900;
            left: 0;
            top: 4px;
            line-height: 1;
            font-size: 18px;
        }

        .input-popup p.error {
            text-decoration: line-through;
        }

        .input-popup p.error::before {
            content: "\f057";
            color: #ea5455;
        }

        .input-popup p.success::before {
            content: "\f058";
            color: #28c76f;
        }
    </style>
@endpush
@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML =
                    '<span class="text-danger">@lang('Captcha field is required.')</span>';
                return false;
            }
            return true;
        }
        (function($) {
            @if ($mobile_code)
                $(`option[data-code={{ $mobile_code }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            @if ($general->secure_password)
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response['data'] && response['type'] == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response['data'] != null) {
                        $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
                    } else {
                        $(`.${response['type']}Exist`).text('');
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
