@extends($activeTemplate.'layouts.master')

@section('content')
<div class="col-xl-9">
    <div class="widget__ticket">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h6 class="widget__ticket-title mb-4 me-2">

                @if($my_ticket->status == 0)
                    <span class="badge badge--success mb-2">@lang('Open')</span>
                @elseif($my_ticket->status == 1)
                    <span class="badge badge--primary mb-2">@lang('Answered')</span>
                @elseif($my_ticket->status == 2)
                    <span class="badge badge--warning mb-2">@lang('Replied')</span>
                @elseif($my_ticket->status == 3)
                    <span class="badge badge--dark mb-2">@lang('Closed')</span>
                @endif
                <span>@lang('Ticket Id')</span>
                <span>#{{ $my_ticket->ticket }}</span>
            </h6>
            @if($my_ticket->status != 3)
                <a href="#0" class="cmn--btn mb-4">
                    <b class="close-button" title="@lang('Close Ticket')" data-bs-toggle="modal" data-bs-target="#DelModal">
                        <i class="las la-times"></i>
                    </b>
                </a>
            @endif
        </div>
        <div class="message__chatbox__body">
            @if($my_ticket->status != 4)
                <form class="message__chatbox__form row" method="post" action="{{ route('ticket.reply', $my_ticket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="replayTicket" value="1">
                    <div class="form--group col-sm-12">
                        <textarea id="inputMessage" class="form-control form--control bg--body" name="message"></textarea>
                    </div>
                    <div class="form--group col-sm-12">
                        <div class="d-flex">
                            <div class="left-group col p-0">
                                <label for="file2" class="form--label text--title">@lang('Attachments')</label>
                                <input type="file" class="overflow-hidden form-control form--control bg--body mb-2" name="attachments[]">
                                <div id="fileUploadsContainer"></div>
                                <span class="info fs--14">@lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx')</span>
                            </div>
                            <div class="add-area">
                                <label class="form--label text--title d-block">&nbsp;</label>
                                <button class="cmn--btn btn--sm bg--primary ms-2 ms-md-4 form--control addFile" type="button"><i class="las la-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form--group col-sm-12 mb-0">
                        <button type="submit" class="cmn--btn">@lang('Reply')</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    <div class="widget__ticket">
        <div class="message__chatbox__body">
            <ul class="reply-message-area">
                @foreach($messages as $message)
                    <li>
                        @if($message->admin_id == 0)
                            <div class="reply-item">
                                <div class="name-area">
                                    <h6 class="title">{{ $message->ticket->name }}</h6>
                                </div>
                                <div class="content-area">
                                    <span class="meta-date">
                                        @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                    </span>
                                    <p>{{$message->message}}</p>
                                    @if($message->attachments()->count() > 0)
                                        <div class="mt-2">
                                            @foreach($message->attachments as $k=> $image)
                                                <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="las la-file"></i>  @lang('Attachment') {{++$k}} </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <ul>
                                <li>
                                    <div class="reply-item">
                                        <div class="name-area">
                                            <h6 class="title">{{ $message->admin->name }}</h6>
                                        </div>
                                        <div class="content-area">
                                            <span class="meta-date">
                                                @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                            </span>
                                            <p>{{$message->message}}</p>
                                            @if($message->attachments()->count() > 0)
                                                <div class="mt-2">
                                                    @foreach($message->attachments as $k=> $image)
                                                        <a href="{{route('ticket.download',encrypt($image->id))}}" class="mr-3"><i class="las la-file"></i>  @lang('Attachment') {{++$k}} </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('ticket.reply', $my_ticket->id) }}">
                @csrf
                <input type="hidden" name="replayTicket" value="2">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Confirmation')!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <strong class="text-dark">@lang('Are you sure you want to close this support ticket')?</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger cmn--btn btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--success cmn--btn btn--sm">@lang("Confirm")
                    </button>
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
            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click',function(){
                $("#fileUploadsContainer").append(
                    `<div class="d-flex append">
                        <div class="left-group col p-0">
                            <label for="file2" class="form--label text--title">@lang('Attachments')</label>
                            <input type="file" class="overflow-hidden form-control form--control bg--body mb-2" name="attachments[]">
                            <div id="fileUploadsContainer"></div>
                        </div>
                        <div class="add-area">
                            <label class="form--label text--title d-block">&nbsp;</label>
                            <button class="cmn--btn btn--sm bg--primary ms-2 ms-md-4 form--control remove-btn" type="button"><i class="las la-times"></i></button>
                        </div>
                    </div>`
                )
            });
            $(document).on('click','.remove-btn',function(){
                $(this).closest('.append').remove();
            });
        })(jQuery);

    </script>
@endpush
