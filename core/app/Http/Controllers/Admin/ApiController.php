<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Wallet;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function index()
    {
        $pageTitle = 'API Setting';
        $wallets = Wallet::all();
        return view('admin.setting.api_setting', compact('pageTitle', 'wallets'));
    }

    public function apiUpdate(Request $request)
    {

        $request->validate([
            'api' => 'sometimes|max:255',
            'wallet_limit' => 'required|integer|gt:0',
            'pin' => 'sometimes|max:255',
            'wallet' => 'sometimes|max:255',
            'fixed_charge' => 'required|gte:0|numeric',
            'percent_charge' => 'required|gte:0|numeric',
        ]);

        $general = GeneralSetting::first();
        $general->api = $request->api;
        $general->wallet_limit = $request->wallet_limit;
        $general->pin = $request->pin;
        $general->wallet = $request->wallet;
        $general->fixed_charge = $request->fixed_charge;
        $general->percent_charge = $request->percent_charge;
        $general->save();

        $notify[] = ['success', 'Api info updated successfully'];
        return back()->withNotify($notify);
    }

    public function addWalletAddress(Request $request)
    {
        // Validate the input
        $request->validate([
            'wallet_address' => [
                'required',
                'max:42',
                function ($attribute, $value, $fail) {
                    // Ethereum-like address (USDT on Ethereum)
                    if (preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
                        return;
                    }

                    // Bitcoin Base58Check or Bech32 address
                    if (preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,39}$/', $value) || preg_match('/^bc1[a-zA-HJ-NP-Z0-9]{25,39}$/', $value)) {
                        return;
                    }

                    $fail('The :attribute is not a valid wallet address.');
                },
            ],
            'coin_name' => 'required|max:50',
        ]);

        // Check if wallet address or coin name already exists
        $walletExists = Wallet::where('wallet_address', $request->wallet_address)
            ->orWhere('coin_name', $request->coin_name)
            ->exists();

        if ($walletExists) {
            // If either wallet address or coin name exists, show an error message
            $notify[] = ['error', 'Wallet address or coin name already exists'];
            return back()->withNotify($notify);
        }

        // If not exists, create a new wallet record
        $wallet = new Wallet();
        $wallet->wallet_address = $request->wallet_address;
        $wallet->coin_name = $request->coin_name;
        $wallet->save();

        // Success notification
        $notify[] = ['success', 'Wallet info added successfully'];
        return back()->withNotify($notify);
    }

    // Edits an existing wallet (fetches the data to pre-populate the form)
    public function editWallet($id)
    {
        $wallet = Wallet::findOrFail($id); // Fetch wallet by ID
        $pageTitle = 'Edit Wallet';
        return view('admin.setting.edit_wallet', compact('wallet', 'pageTitle'));
    }

    // Updates the wallet information after editing
    public function updateWallet(Request $request, $id)
    {
        $request->validate([
            'wallet_address' => 'required|max:255',
            'coin_name' => 'required|max:50',
        ]);

        $wallet = Wallet::findOrFail($id); // Fetch wallet by ID
        $wallet->address = $request->wallet_address;
        $wallet->coin_name = $request->coin_name;
        $wallet->save();

        $notify[] = ['success', 'Wallet info updated successfully'];
        return redirect()->route('admin.api.index')->withNotify($notify);
    }

    // Deletes a wallet
    public function deleteWallet($id)
    {
        $wallet = Wallet::findOrFail($id); // Fetch wallet by ID
        $wallet->delete();

        $notify[] = ['success', 'Wallet deleted successfully'];
        return back()->withNotify($notify);
    }

}

// adminwallet