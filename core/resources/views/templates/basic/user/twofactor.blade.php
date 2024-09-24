@extends($activeTemplate.'layouts.master')
@section('content')

<div class="col-xl-9">
    <div class="row g-4">
        <div class="col-lg-6">
            @if(Auth::user()->ts)
                <div class="card custom--card h-100">
                    <div class="card-header">
                        <h5 class="card-title text-white">@lang('Two Factor Authenticator')</h5>
                    </div>
                    <div class="card-body">
                        <div class="two-factor-content">
                            <div class="input-group">
                                <input type="text" value="{{$secret}}" class="form-control h--50px form--control bg--section" required="" readonly="">
                                <button class="input-group-text form--control" type="button">
                                    <i class="lar la-copy"></i>
                                </button>
                            </div>
                            <div class="text-center mt-4">
                                <a href="#0" data-bs-toggle="modal" data-bs-target="#disableModal" class="cmn--btn">@lang('Disable Two Factor Authenticator')</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card custom--card h-100">
                    <div class="card-header">
                        <h5 class="card-title text-white">@lang('Two Factor Authenticator')</h5>
                    </div>
                    <div class="card-body">
                        <div class="two-factor-content">
                            <div class="input-group">
                                <input type="text" value="{{$secret}}" class="form-control h--50px form--control bg--section" required="" readonly="" name="key" id="referralURL">
                                <button class="input-group-text form--control copytext" type="button" id="copyBoard">
                                    <i class="lar la-copy"></i>
                                </button>
                            </div>
                            <div class="two-factor-scan text-center my-4">
                                <img src="{{$qrCodeUrl}}" alt="images">
                            </div>
                            <div class="text-center">
                                <a href="#0" data-bs-toggle="modal" data-bs-target="#enableModal" class="cmn--btn">@lang('Enable Two Factor Authenticator')</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            <div class="card custom--card h-100">
                <div class="card-header">
                    <h5 class="card-title text-white">@lang('Google Authenticator')</h5>
                </div>
                <div class="card-body">
                    <div class="two-factor-content">
                        <h6 class="subtitle--bordered">@lang('USE GOOGLE AUTHENTICATOR TO SCAN THE QR CODE OR USE THE CODE')</h6>
                        <p class="two__fact__text">
                            @lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.')
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank" class="cmn--btn">@lang('DOWNLOAD APP')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--Enable Modal -->
<div id="enableModal" class="modal fade" role="dialog">
    <div class="modal-dialog ">
        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header">
                <h6 class="modal-title">@lang('Verify Your Otp')</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('user.twofactor.enable')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="key" value="{{$secret}}">
                        <input type="text" class="form-control form--control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger cmn--btn btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--success cmn--btn btn--sm">@lang('Verify')</button>
                </div>
            </form>
        </div>

    </div>
</div>

<!--Disable Modal -->
<div id="disableModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">@lang('Verify Your Otp Disable')</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('user.twofactor.disable')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control form--control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger cmn--btn btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--success cmn--btn btn--sm">@lang('Verify')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        (function($){
            "use strict";

            $('.copytext').on('click',function(){
                var copyText = document.getElementById("referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({message: "Copied: " + copyText.value, position: "topRight"});
            });
        })(jQuery);
    </script>
@endpush


