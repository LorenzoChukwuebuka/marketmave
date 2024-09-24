@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-xl-9">
    <div class="transaction__warpper">
        <table class="table cmn--table transaction--table">
            <thead>
                <tr>
                    <th>@lang('Date')</th>
                    <th>@lang('Trx')</th>
                    <th>@lang('From Wallet')</th>
                    <th>@lang('To Wallet')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Charge')</th>
                    <th>@lang('Status')</th>
                </tr>
                <tr class="d-block"><td class="d-none"></td></tr>
            </thead>
            <tbody>
                @forelse($logs as $data)
                    <tr>
                        <td data-label="@lang('Date')">
                            {{ $data->created_at ? showDateTime($data->created_at) : 'N/A' }}
                        </td>
                        <td data-label="@lang('Trx')">{{ $data->trx ?? 'N/A' }}</td>

                        <td data-label="@lang('From Wallet')" class="see-more-less">
                            <span>{{ $data->wallet->wallet_address ?? 'N/A' }}</span>
                        </td>
                        <td data-label="@lang('To Wallet')" class="see-more-less">
                            <span>{{ $data->receive_wallet ?? 'N/A' }}</span>
                        </td>

                        <td data-label="@lang('Amount')">
                            <strong>
                                @if(isset($data->amount) && isset($general->cur_text))
                                    {{ showAmount($data->amount, 8) ?? 'N/A' }}
                                    {{ __($general->cur_text) ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </strong>
                        </td>
                        <td data-label="@lang('Charge')">
                            <strong>
                                @if(isset($data->charge) && isset($general->cur_text))
                                    {{ showAmount($data->charge, 8) }}
                                    {{ __($general->cur_text) }}
                                @else
                                    N/A
                                @endif
                            </strong>
                        </td>
                        <td data-label="@lang('Status')">
                            @if(isset($data->status))
                                @if($data->status == 1)
                                    <span class="badge badge--success">@lang('Completed')</span>
                                @else
                                    <span class="badge badge--warning">@lang('Pending')</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr class="d-block"><td class="d-none"></td></tr>
                @empty
                    <tr>
                        <td colspan="100%">@lang('Data Not Found')!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(isset($logs))
            {{$logs->links()}}
        @endif

    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        $('.see-more-less').on('click', function(){
            $(this).toggleClass('active')
        });
    })(jQuery);
</script>
@endpush