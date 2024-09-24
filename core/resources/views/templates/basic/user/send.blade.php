@extends($activeTemplate.'layouts.master')
@section('content')

<div class="col-xl-9">
    <div class="widget__ticket">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h6 class="widget__ticket-title mb-4 me-2">
                <span>@lang('Send') {{ __($general->cur_text) }}</span>
            </h6>
            <a href="{{ route('user.send.history') }}" class="btn btn--primary mb-4">@lang('Send History')</a>
        </div>
        <div class="message__chatbox__body">
            <form class="message__chatbox__form row" action="{{ route('user.send') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form--group col-sm-12">
                    <label for="send_from_wallet" class="form--label">@lang('Send From Wallet')</label>
                    <select name="wallet_address" class="form-control form--control" required>
                        <option value="">@lang('Please Select One')</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->wallet_address }}">
                                {{ __($wallet->name) }} {{ $wallet->name ? '-' : null }} {{ __($wallet->wallet_address) }} ({{ $general->cur_sym }} {{ showAmount($wallet->balance, 8) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form--group col-sm-12">
                    <label for="send_to_wallet" class="form--label">@lang('Send To Wallet')</label>
                    <div class="input-group">
                        <select name="send_to_wallet" class="form-control form--control" required id="send_to_wallet">
                            <option value="">@lang('Please Select One')</option>
                            @foreach($adminWallets as $adminWallet)
                                <option value="{{ $adminWallet->wallet_address }}">
                                    {{ __($adminWallet->coin_name) }} {{ $adminWallet->coin_name ? '-' : null }} {{ __($adminWallet->wallet_address) }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn--primary" type="button" id="copyAddress">Copy</button>
                    </div>
                </div>
                <div class="form--group col-sm-12">
                    <label for="btc_amount" class="form--label">@lang('Amount to Send')</label>
                    <div class="input-group">
                        <input type="number" step="any" id="btc_amount" name="btc_amount" class="form-control form--control bg--body" required>
                        <span class="input-group-text bg--primary">
                            {{ __($general->cur_text) }}
                        </span>
                    </div>
                </div>
                <div class="form--group col-sm-12">
                    <label for="confirmation_image" class="form--label">@lang('Confirmation Image')</label>
                    <input type="file" id="confirmation_image" name="confirmation_image" class="form-control form--control" required accept="image/*">
                </div>
                @if(Auth::user()->ts)
                    <div class="form--group col-sm-12">
                        <label for="authenticator_code" class="form--label">@lang('2FA Verification Code')</label>
                        <input type="text" id="authenticator_code" name="authenticator_code" class="form-control form--control bg--body" placeholder="@lang('Verification Code')" required>
                    </div>
                @endif
                <div class="form--group col-sm-12 mb-0 justify-content-between d-flex flex-wrap">
                    <div class="cal" style="margin: auto 0"></div>
                    <button type="submit" class="cmn--btn">@lang('Send Balance')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    (function ($) {
        "use strict";

        $('#copyAddress').on('click', function() {
            var wallet = $('#send_to_wallet').val();
            if (wallet) {
                navigator.clipboard.writeText(wallet).then(function() {
                    alert('Address copied to clipboard!');
                }, function(err) {
                    console.error('Could not copy text: ', err);
                });
            } else {
                alert('Please select a wallet first.');
            }
        });

    })(jQuery);
</script>
@endpush