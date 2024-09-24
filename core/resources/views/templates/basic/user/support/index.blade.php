@extends($activeTemplate.'layouts.master')

@section('content')
<div class="col-xl-9">

    <div class="mb-4 text-end">
        <a href="{{ route('ticket.open') }}"
        class="btn btn--primary h-auto">
            @lang('Create New')
        </a>
    </div>

    <div class="transaction__warpper">
        <table class="table cmn--table transaction--table">
            <thead>
                <tr>
                    <th>@lang('Subject')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Priority')</th>
                    <th>@lang('Last Reply')</th>
                    <th>@lang('Action')</th>
                </tr>
                <tr class="d-block"><td class="d-none"></td></tr>
            </thead>
            <tbody>
                @forelse($supports as $key => $support)
                <tr>
                    <td data-label="@lang('Subject')"> <a href="{{ route('ticket.view', $support->ticket) }}" class="font-weight-bold"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                    <td data-label="@lang('Status')">
                        @if($support->status == 0)
                            <span class="badge badge--success py-2 px-3">@lang('Open')</span>
                        @elseif($support->status == 1)
                            <span class="badge badge--primary py-2 px-3">@lang('Answered')</span>
                        @elseif($support->status == 2)
                            <span class="badge badge--warning py-2 px-3">@lang('Customer Reply')</span>
                        @elseif($support->status == 3)
                            <span class="badge badge--dark py-2 px-3">@lang('Closed')</span>
                        @endif
                    </td>
                    <td data-label="@lang('Priority')">
                        @if($support->priority == 1)
                            <span class="badge badge--dark py-2 px-3">@lang('Low')</span>
                        @elseif($support->priority == 2)
                            <span class="badge badge--success py-2 px-3">@lang('Medium')</span>
                        @elseif($support->priority == 3)
                            <span class="badge badge--primary py-2 px-3">@lang('High')</span>
                        @endif
                    </td>
                    <td data-label="@lang('Last Reply')">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }} </td>

                    <td data-label="@lang('Action')">
                        <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn-sm btn--primary">
                            <i class="las la-desktop"></i>
                        </a>
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

        {{$supports->links()}}

    </div>
</div>
@endsection
