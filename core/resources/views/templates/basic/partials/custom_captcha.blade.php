@php
	$captcha = loadCustomCaptcha();
@endphp
@if($captcha)
<div class="col-md-12 mt-4">
    <div class="captha">
        @php echo $captcha @endphp
    </div>
</div>
<div class="col-md-12 mt-4">
    <input type="text" name="captcha" placeholder="@lang('Enter Code')" class="form--control-2">
</div>
@endif


@push('style')
<style>
    .captha div{
        width: 100% !important;
    }
</style>
@endpush

