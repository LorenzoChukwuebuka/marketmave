@extends($activeTemplate.'layouts.auth_master')

@php
    $bgImage = getContent('auth_background_iamge.content', true);
@endphp

@section('content')
<div class="account-section bg_img" data-background="{{ getImage('assets/images/frontend/auth_background_iamge/' .@$bgImage->data_values->image, '1920x1080') }}">
    <div class="account__section-wrapper">
        <div class="account__section-thumb">
            <div class="logo d-none d-lg-block">
                <a href="{{ route('home') }}">
                    <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" alt="@lang('logo')">
                </a>
            </div>
        </div>
        <div class="account__section-content bg--title">
            <div class="w-100">
                <div class="logo mb-5 d-lg-none">
                    <a href="{{ route('home') }}">
                     <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" alt="@lang('logo')">
                    </a>
                </div>
                <div class="section__header text--white">
                    <h4 class="section__title mb-0">@lang('Account Recovery')</h4>
                </div>
                <form class="account--form row g-4" action="{{ route('user.password.verify.code') }}" method="POST">
                    <input type="hidden" name="email" value="{{ $email }}">
                    @csrf
                    <div class="col-sm-12">
                        <label for="username" class="form--label-2">@lang('Verification Code')</label>
                        <input type="text" name="code" id="code" class="form--control-2">
                    </div>

                    <div class="col-sm-12">
                        <button type="submit" class="cmn--btn w-100">@lang('Verify Code')</button>
                    </div>
                </form>
                <div class="mt-5 text--white">
                    @lang('Please check including your Junk/Spam Folder. if not found, you can')
                    <a href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        $('#code').on('input change', function () {
          var xx = document.getElementById('code').value;
          $(this).val(function (index, value) {
             value = value.substr(0,7);
              return value.replace(/\W/gi, '').replace(/(.{3})/g, '$1 ');
          });
      });
    })(jQuery)
</script>
@endpush
