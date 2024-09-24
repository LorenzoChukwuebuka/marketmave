@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <tbody>
                                <tr>
                                    <td>@lang('User')</td>
                                    <td>{{ __(@$log->user->fullname ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Transaction')</td>
                                    <td>{{ $log->trx ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Amount')</td>
                                    <td>{{ isset($log->amount) ? showAmount($log->amount, 8) . ' ' . __($general->cur_text) : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('Charge')</td>
                                    <td>{{ isset($log->charge) ? showAmount($log->charge, 8) . ' ' . __($general->cur_text) : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('From Wallet')</td>
                                    <td>{{ $log->wallet->wallet_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('To Wallet')</td>
                                    <td>{{ $log->receive_wallet ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Status')</td>
                                    <td>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (isset($log->confirmation_image))
                <div class="card b-radius--10 mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">@lang('Confirmation Image')</h5>
                        <img src="{{ getImage(imagePath()['payments']['path']  . '/' . ($log->confirmation_image ), imagePath()['payments']['size'] ) }}" alt="Confirmation" class="img-fluid">
                    </div>
                </div>
            @endif

            @if ($log->status == 0)
                <div class="card b-radius--10 mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">@lang('Confirm Payment')</h5>
                        <form action="{{ route('admin.payments.confirm', $log->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn--primary w-100">@lang('Confirm Payment')</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
