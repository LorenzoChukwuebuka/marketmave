@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Edit Wallet')</h5>
                    <form action="{{ route('admin.wallet.update', $wallet->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Wallet Address')</label>
                                    <input class="form-control form-control-lg" type="text" name="wallet_address"
                                        value="{{ old('wallet_address', $wallet->address) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Coin Name')</label>
                                    <input class="form-control form-control-lg" type="text" name="coin_name"
                                        value="{{ old('coin_name', $wallet->coin_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Update Wallet')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection