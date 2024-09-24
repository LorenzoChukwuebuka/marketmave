@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Wallet Address')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td data-label="@lang('Trx')">
                                            <strong>{{ $log->trx }}</strong>
                                        </td>

                                        <td data-label="@lang('Date')">
                                            {{ showDateTime($log->created_at) }} <br> {{ diffForHumans($log->created_at) }}
                                        </td>

                                        <td data-label="@lang('Wallet Address')">
                                            <span class="font-weight-bold text-danger" data-toggle="tooltip"
                                                data-original-title="@lang('From Wallet')">
                                                - {{ $log->wallet ? $log->wallet->wallet_address : 'N/A' }}
                                            </span>
                                            <br>
                                            <span class="font-weight-bold text-success" data-toggle="tooltip"
                                                data-original-title="@lang('To Address')">
                                                + {{ $log->receive_wallet ?? 'N/A' }}
                                            </span>
                                        </td>


                                        <td data-label="@lang('Amount')" class="budget">
                                            <span class="font-weight-bold">
                                                {{ showAmount($log->amount, 8) }} {{ $general->cur_text }}
                                            </span>
                                        </td>

                                        <td data-label="@lang('Charge')" class="budget">
                                            <span class="font-weight-bold text--danger">
                                                {{ showAmount($log->charge, 8) }} {{ $general->cur_text }}
                                            </span>
                                        </td>

                                        <td data-label="@lang('Status')">
                                            @if ($log->status == 1)
                                                <span class="badge badge--success">@lang('Completed')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($logs) }}
                </div>
            </div><!-- card end -->
        </div>
    </div>
@endsection
