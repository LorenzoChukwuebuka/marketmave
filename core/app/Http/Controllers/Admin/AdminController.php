<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Send;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserWallet;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {

        $pageTitle = 'Dashboard';

        // User Info
        $widget['total_users'] = User::count();
        $widget['verified_users'] = User::where('status', 1)->count();
        $widget['email_unverified_users'] = User::where('ev', 0)->count();
        $widget['sms_unverified_users'] = User::where('sv', 0)->count();

        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);

        $general = GeneralSetting::first('last_cron');
        $rateCron = Carbon::parse(json_decode(@$general->last_cron)->rate)->diffInSeconds() >= 900;
        $sendCron = Carbon::parse(json_decode(@$general->last_cron)->send)->diffInSeconds() >= 900;
        $receiveCron = Carbon::parse(json_decode(@$general->last_cron)->receive)->diffInSeconds() >= 900;

        $totalWallet = UserWallet::count();
        $totalReceive = Transaction::where('trx_type', '+')->sum('amount');
        $totalSend = Send::where('status', 1)->sum('amount');
        $totalTrx = Transaction::count();

        return view('admin.dashboard', compact('pageTitle', 'widget', 'chart', 'rateCron', 'sendCron', 'receiveCron', 'totalWallet', 'totalReceive', 'totalSend', 'totalTrx'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
        $user = Auth::guard('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['admin']['path'], imagePath()['profile']['admin']['size'], $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = Auth::guard('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password do not match !!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return redirect()->route('admin.password')->withNotify($notify);
    }

    public function notifications()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications'));
    }

    public function notificationRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->read_status = 1;
        $notification->save();
        return redirect($notification->click_url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $url = "https://license.viserlab.com/issue/get?" . http_build_query($arr);
        $response = json_decode(curlContent($url));
        if ($response->status == 'error') {
            return redirect()->route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports', compact('reports', 'pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bug,feature',
            'message' => 'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = json_decode(curlPostContent($url, $arr));
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success', $response->message];
        return back()->withNotify($notify);
    }

    public function systemInfo()
    {
        $laravelVersion = app()->version();
        $serverDetails = $_SERVER;
        $currentPHP = phpversion();
        $timeZone = config('app.timezone');
        $pageTitle = 'System Information';
        return view('admin.info', compact('pageTitle', 'currentPHP', 'laravelVersion', 'serverDetails', 'timeZone'));
    }

    public function readAll()
    {
        AdminNotification::where('read_status', 0)->update([
            'read_status' => 1,
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function receiveHistory()
    {
        $pageTitle = 'Receive History';
        $emptyMessage = 'Data Not Found';
        $logs = Transaction::where('trx_type', '+')->latest()->with('wallet', 'user')->paginate(getPaginate());
        return view('admin.history.receive_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function sendHistory()
    {
        $pageTitle = 'Send History';
        $emptyMessage = 'Data Not Found';
        $logs = Send::where('status', 1)->latest()->with('wallet', 'user')->paginate(getPaginate());
        return view('admin.history.send_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function sendPendingHistory()
    {
        $pageTitle = 'Send Pending History';
        $emptyMessage = 'Data Not Found';
        $logs = Send::where('status', 0)->latest()->with('wallet', 'user')->paginate(getPaginate());
        return view('admin.history.send_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function sendInFailedHistory()
    {
        $pageTitle = 'Send Failed History';
        $emptyMessage = 'Data Not Found';
        $logs = Send::where('status', 9)->latest()->with('wallet', 'user')->paginate(getPaginate());
        return view('admin.history.send_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function getPaymentDetails($id)
    {
        $pageTitle = 'Payment Details';
        $emptyMessage = 'Data Not Found';
        $log = Send::with('wallet', 'user')->findOrFail($id);
        return view('admin.history.payment_details', compact('pageTitle', 'emptyMessage', 'log'));
    }

    // public function confirmPayment($id)
    // {
    //     $log = Send::findOrFail($id);

    //     if ($log->status != 0) {
    //         $notify[] = ['error', 'This payment cannot be confirmed.'];
    //         return back()->withNotify($notify);
    //     }

    //     $log->status = 1; // Set status to completed
    //     $log->save();

    //     // Add any additional logic here (e.g., updating balances, creating a transaction record, etc.)
    //     // For example:
    //     // $log->user->balance += $log->amount;
    //     // $log->user->save();
    //     //
    //     // Transaction::create([
    //     //     'user_id' => $log->user_id,
    //     //     'amount' => $log->amount,
    //     //     'charge' => $log->charge,
    //     //     'trx_type' => '+',
    //     //     'details' => 'Payment confirmed',
    //     //     'trx' => $log->trx,
    //     //     'remark' => 'payment_confirmation'
    //     // ]);

    //     $notify[] = ['success', 'Payment confirmed successfully.'];
    //     return redirect()->route('admin.send.history')->withNotify($notify);
    // }

    public function confirmPayment($id)
    {
        // Find the payment log by its ID
        $log = Send::findOrFail($id);

        // Check if the payment has already been confirmed
        if ($log->status != 0) {
            $notify[] = ['error', 'This payment cannot be confirmed.'];
            return back()->withNotify($notify);
        }

        // Update the payment status to "confirmed" (status = 1)
        $log->status = 1;
        $log->save();

        // Fetch the USD conversion rate for BTC from GeneralSetting
        $general = GeneralSetting::first(['usd_rate']);

        // Convert the confirmed BTC amount to USD
        $btcAmount = $log->amount; // Assuming 'amount' is the BTC amount
        $usdEquivalent = $btcAmount * $general->usd_rate;

        // Find the user associated with the log and update their balance
        $user = $log->user; // Assuming the log is related to the user model
        $user->balance += $usdEquivalent;
        $user->save();

        $userWallet = UserWallet::where('user_id', $log->user_id)->first();

        if ($userWallet) {
            $userWallet->balance = $btcAmount;
            $userWallet->save();
        } else {
            // Handle the case where no wallet was found for the user
            $notify[] = ['error', 'User wallet not found.'];
            return back()->withNotify($notify);
        }

        // Notify the user about the confirmation and balance update
        notify($user, 'PAYMENT_CONFIRMED', [
            'trx' => $log->trx,
            'btc_amount' => showAmount($btcAmount),
            'usd_amount' => showAmount($usdEquivalent),
            'currency' => 'USD',
            'post_balance' => showAmount($user->balance),
        ]);

        // Notify admin or relevant parties about successful confirmation
        $notify[] = ['success', 'Payment confirmed successfully and USD equivalent added to the user balance.'];
        return redirect()->route('admin.send.history')->withNotify($notify);
    }

    public function totalWallet()
    {
        $pageTitle = 'Total Wallet';
        $emptyMessage = 'Data Not Found';
        $wallets = UserWallet::latest()->paginate(getPaginate());
        return view('admin.wallet', compact('pageTitle', 'emptyMessage', 'wallets'));
    }

}