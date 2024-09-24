@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-xl-9">

    <div class="mb-4">
       <div class="row g-4 justify-content-end">
           <div class="col-md-6">
            <select name="wallet" id="wallet" class="form--control select">
                <option value="">@lang('Short by Wallet')</option>
                @foreach($wallets as $singleWallet)
                    <option value="{{ $singleWallet->id }}" {{ $walletId == $singleWallet->id ? 'selected' : null }}>
                        {{ $singleWallet->name }} {{ $singleWallet->name ? '-' : null }} {{ $singleWallet->wallet_address }}
                    </option>
                @endforeach
            </select>
       </div>
       </div>
    </div>

    <div class="transaction__warpper">
        <table class="table cmn--table transaction--table">
            <thead>
                <tr>
                    <th>@lang('Date')</th>
                    <th>@lang('Trx')</th>
                    <th>@lang('Wallet')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Post Balance')</th>
                    <th>@lang('Details')</th>
                </tr>
                <tr class="d-block"><td class="d-none"></td></tr>
            </thead>
            <tbody>
                @forelse($logs as $data)
                    <tr>
                        <tr>
                            <td data-label="@lang('Date')">
                                {{ showDateTime($data->created_at) }}
                            </td>
                            <td data-label="@lang('Trx')">{{ $data->trx }}</td>
                            <td data-label="@lang('Wallet')" class="text-break see-more-less">
                                <span>{{ $data->wallet->wallet_address }}</span>
                            </td>
                            <td data-label="@lang('Amount')">
                                <strong>
                                    {{ $data->trx_type }}
                                    {{ showAmount($data->amount, 8) }}
                                    {{ __($general->cur_text) }}
                                </strong>
                            </td>
                            <td data-label="@lang('Post Balance')">
                                <strong>
                                    {{ showAmount($data->post_balance, 8) }}
                                    {{ __($general->cur_text) }}
                                </strong>
                            </td>
                            <td data-label="@lang('Details')" class="text-break">{{ __($data->details) }}</td>
                        </tr>
                    <tr class="d-block"><td class="d-none"></td></tr>
                @empty
                    <tr>
                        <td colspan="100%">@lang('Data Not Found')!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $logs->appends(request()->all())->links() }}

    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function(){
        "use strict";

        $("#wallet").on("change", function() {
            if(!$(this).val()){
                return;
            }
            window.location.href = '?wallet='+$(this).val();
        });

        $('.see-more-less').on('click', function(){
            $(this).toggleClass('active')
        });

    });
</script>
@endpush


