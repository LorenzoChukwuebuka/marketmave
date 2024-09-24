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
                                    <th>@lang('User')</th>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Wallet Address')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Charge')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Confirmation')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td data-label="@lang('User')">
                                            <span class="font-weight-bold">{{ __(@$log->user->fullname ?? 'N/A') }}</span>
                                            @if (@$log->user)
                                                <br>
                                                <span class="small">
                                                    <a
                                                        href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ __($log->user->username) }}</a>
                                                </span>
                                            @endif
                                        </td>

                                        <td data-label="@lang('Trx')">
                                            <strong>{{ $log->trx ?? 'N/A' }}</strong>
                                        </td>

                                        <td data-label="@lang('Date')">
                                            @if ($log->created_at)
                                                {{ showDateTime($log->created_at) }} <br>
                                                {{ diffForHumans($log->created_at) }}
                                            @else
                                                N/A
                                            @endif
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
                                                @if (isset($log->amount) && isset($general->cur_text))
                                                    {{ showAmount($log->amount, 8) }} {{ $general->cur_text }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </td>

                                        <td data-label="@lang('Charge')" class="budget">
                                            <span class="font-weight-bold text--danger">
                                                @if (isset($log->charge) && isset($general->cur_text))
                                                    {{ showAmount($log->charge, 8) }} {{ $general->cur_text }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </td>

                                        <td data-label="@lang('Status')" class="budget">
                                            @if (isset($log->status))
                                                @if ($log->status == 0)
                                                    <span class="badge badge--warning">@lang('Pending')</span>
                                                @elseif($log->status == 1)
                                                    <span class="badge badge--success">@lang('Completed')</span>
                                                @elseif($log->status == 9)
                                                    <span class="badge badge--danger">@lang('Incomplete')</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td data-label="@lang('Confirmation')">
                                            @if (isset($log->confirmation_image))
                                                <a href="{{ getImage(imagePath()['payments']['path'] . '/' . $log->confirmation_image, imagePath()['payments']['size']) }}"
                                                    target="_blank">
                                                    <img src="{{ getImage(imagePath()['payments']['path'] . '/' . $log->confirmation_image, imagePath()['payments']['size']) }}"
                                                        alt="Confirmation" class="img-thumbnail" style="max-width: 100px;">
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td data-label="@lang('Action')">
                                            <a href="{{ route('admin.payments.details', $log->id) }}" class="icon-btn"
                                                data-toggle="tooltip" title=""
                                                data-original-title="@lang('Details')">
                                                <i class="las la-desktop text--shadow"></i>
                                            </a>
                                            @if ($log->status == 0)
                                                <a href="{{ route('admin.payments.confirm', $log->id) }}"
                                                    class="icon-btn btn--success ml-1 confirmationBtn" data-toggle="tooltip"
                                                    title="" data-original-title="@lang('Confirm')">
                                                    <i class="las la-check text--shadow"></i>
                                                </a>
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
                    @if (isset($logs))
                        {{ paginateLinks($logs) }}
                    @endif
                </div>
            </div><!-- card end -->
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <form action="" method="GET" class="form-inline">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('TRX')"
                    value="{{ request()->search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.confirmationBtn').on('click', function(e) {
                e.preventDefault();
                var modal = $('#confirmationModal');
                modal.find('form').attr('action', $(this).attr('href'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('modal')
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirm Payment')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to confirm this payment?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
