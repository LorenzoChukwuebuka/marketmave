@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="col-xl-9">
        <div class="widget__ticket">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h6 class="widget__ticket-title mb-4 me-2">
                    <span>@lang('Create New')</span>
                </h6>
                <a href="{{ route('ticket') }}" class="btn btn--primary mb-4">@lang('My Ticket')</a>
            </div>
            <div class="message__chatbox__body">
                <form class="message__chatbox__form row" action="{{ route('ticket.store') }}" method="post"
                    enctype="multipart/form-data" onsubmit="return submitUserForm();">
                    @csrf
                    <div class="form--group col-sm-6">
                        <label for="name" class="form--label">@lang('Name')</label>
                        <input type="text" id="name" name="name"
                            value="{{ @$user->firstname . ' ' . @$user->lastname }}" class="form-control form--control"
                            readonly>
                    </div>
                    <div class="form--group col-sm-6">
                        <label for="email" class="form--label">@lang('Email')</label>
                        <input type="email" id="email" name="email" value="{{ @$user->email }}"
                            class="form-control form--control" readonly>
                    </div>
                    <div class="form--group col-sm-6">
                        <label for="subject" class="form--label">@lang('Subject')</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                            class="form-control form--control" placeholder="@lang('Subject')">
                    </div>
                    <div class="form--group col-sm-6">
                        <label for="subject" class="form--label">@lang('Priority')</label>
                        <select name="priority" class="form-control form--control">
                            <option value="3">@lang('High')</option>
                            <option value="2">@lang('Medium')</option>
                            <option value="1">@lang('Low')</option>
                        </select>
                    </div>
                    <div class="form--group col-sm-12">
                        <label for="message" class="form--label">@lang('Message')</label>
                        <textarea id="message" class="form-control form--control bg--body" name="message">{{ old('message') }}</textarea>
                    </div>
                    <div class="form--group col-sm-12">
                        <div class="d-flex">
                            <div class="left-group col p-0">
                                <label for="file2" class="form--label text--title">@lang('Attachments')</label>
                                <input type="file" class="overflow-hidden form-control form--control bg--body mb-2"
                                    name="attachments[]">
                                <div id="fileUploadsContainer"></div>
                                <span class="info fs--14">@lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'),
                                    .@lang('png'), .@lang('pdf'), .@lang('doc'),
                                    .@lang('docx')</span>
                            </div>
                            <div class="add-area">
                                <label class="form--label text--title d-block">&nbsp;</label>
                                <button class="cmn--btn btn--sm bg--primary ms-2 ms-md-4 form--control addFile"
                                    type="button"><i class="las la-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form--group col-sm-12 mb-0">
                        <button type="submit" class="cmn--btn">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.delete-message').on('click', function(e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click', function() {
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
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.append').remove();
            });
        })(jQuery);
    </script>
@endpush
