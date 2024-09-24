<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Send;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ManageUsersController extends Controller
{
    public function allUsers()
    {
        $pageTitle = 'Manage Users';
        $emptyMessage = 'No user found';
        $users = User::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Manage Active Users';
        $emptyMessage = 'No active user found';
        $users = User::active()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = 'Banned Users';
        $emptyMessage = 'No banned user found';
        $users = User::banned()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Users';
        $emptyMessage = 'No email unverified user found';
        $users = User::emailUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }
    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $emptyMessage = 'No email verified user found';
        $users = User::emailVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsUnverifiedUsers()
    {
        $pageTitle = 'SMS Unverified Users';
        $emptyMessage = 'No sms unverified user found';
        $users = User::smsUnverified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsVerifiedUsers()
    {
        $pageTitle = 'SMS Verified Users';
        $emptyMessage = 'No sms verified user found';
        $users = User::smsVerified()->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = 'Users with balance';
        $emptyMessage = 'No sms verified user found';

        $wallets = UserWallet::where('balance', '!=', 0)->groupBy('user_id')->pluck('user_id');
        $users = User::whereIn('id', $wallets)->paginate(getPaginate());

        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }



    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where(function ($user) use ($search) {
            $user->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });
        $pageTitle = '';
        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        }elseif($scope == 'banned'){
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        }elseif($scope == 'emailUnverified'){
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        }elseif($scope == 'smsUnverified'){
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        }elseif($scope == 'withBalance'){
            $pageTitle = 'With Balance ';
            $users = $users->where('balance','!=',0);
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'User Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }


    public function detail($id)
    {
        $pageTitle = 'User Detail';
        $user = User::findOrFail($id);

        $countTotalSend = Send::where('user_id', $id)->count();
        $countTotalReceive = Transaction::where('user_id', $id)->where('trx_type', '+')->count();

        $wallets = UserWallet::where('user_id', $user->id)->latest()->get();
        $btcBalance = UserWallet::where('user_id', $user->id)->sum('balance');
        $countWallet = UserWallet::where('user_id', $user->id)->count();

        $totalTransaction = Transaction::where('user_id',$user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('admin.users.detail', compact('pageTitle', 'user','totalTransaction','countries', 'countTotalSend', 'wallets', 'btcBalance', 'countWallet', 'countTotalReceive'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $request->validate([
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'email' => 'required|email|max:90|unique:users,email,' . $user->id,
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'country' => 'required',
        ]);
        $countryCode = $request->country;
        $user->mobile = $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
                            'address' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zip,
                            'country' => @$countryData->$countryCode->country,
                        ];
        $user->status = $request->status ? 1 : 0;
        $user->ev = $request->ev ? 1 : 0;
        $user->sv = $request->sv ? 1 : 0;
        $user->ts = $request->ts ? 1 : 0;
        $user->tv = $request->tv ? 1 : 0;
        $user->save();

        $notify[] = ['success', 'User detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'remark' => 'required|max:255',
            'wallet_address' => [
                Rule::exists('user_wallets')->where(function ($query) use ($request, $id) {
                    return $query->where('wallet_address', $request->wallet_address)
                    ->where('user_id', $id);
                }),
            ]
        ]);

        $user = User::findOrFail($id);
        $findWallet = UserWallet::where('user_id', $user->id)->where('wallet_address', $request->wallet_address)->first();

        $amount = $request->amount;
        $general = GeneralSetting::first(['cur_text','cur_sym']);
        $trx = getTrx();

        if ($request->act) {

            $findWallet->balance += $amount;
            $findWallet->save();

            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $user->username . '\'s balance'];

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->wallet_id = $findWallet->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $findWallet->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = $request->remark;
            $transaction->trx = $trx;
            $transaction->save();

            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($findWallet->balance),
                'wallet' => $findWallet->wallet_address,
                'wallet_name' => $findWallet->name ?? 'N/A',
            ]);

        } else {
            if ($amount > $findWallet->balance) {
                $notify[] = ['error', $user->username . '\'s has insufficient balance.'];
                return back()->withNotify($notify);
            }

            $findWallet->balance -= $amount;
            $findWallet->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->wallet_id = $findWallet->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $findWallet->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = $request->remark;
            $transaction->trx = $trx;
            $transaction->save();

            notify($user, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($findWallet->balance),
                'wallet' => $findWallet->wallet_address,
                'wallet_name' => $findWallet->name ?? 'N/A',
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $user->username . '\'s balance'];
        }
        return back()->withNotify($notify);
    }


    public function userLoginHistory($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Login History - ' . $user->username;
        $emptyMessage = 'No users login found.';
        $login_logs = $user->login_logs()->orderBy('id','desc')->with('user')->paginate(getPaginate());
        return view('admin.users.logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }



    public function showEmailSingleForm($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.users.email_single', compact('pageTitle', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = User::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function transactions(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Transactions : ' . $user->username;

            $transactions = $user->transactions()->whereHas('wallet', function($query) use ($search){
                $query->where('wallet_address', $search);
            })->orWhere('trx', $search)->with('user', 'wallet')->orderBy('id','desc')->paginate(getPaginate());

            $emptyMessage = 'No transactions';
            return view('admin.reports.transactions', compact('pageTitle', 'search', 'user', 'transactions', 'emptyMessage'));
        }

        $pageTitle = 'User Transactions : ' . $user->username;
        $transactions = $user->transactions()->with('user', 'wallet')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions';

        return view('admin.reports.transactions', compact('pageTitle', 'user', 'transactions', 'emptyMessage'));
    }

    public function deposits(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userId = $user->id;
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Deposits : ' . $user->username;
            $deposits = $user->deposits()->where('trx', $search)->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No deposits';
            return view('admin.deposit.log', compact('pageTitle', 'search', 'user', 'deposits', 'emptyMessage','userId'));
        }

        $pageTitle = 'User Deposit : ' . $user->username;
        $deposits = $user->deposits()->orderBy('id','desc')->with(['gateway','user'])->paginate(getPaginate());
        $successful = $user->deposits()->orderBy('id','desc')->where('status',1)->sum('amount');
        $pending = $user->deposits()->orderBy('id','desc')->where('status',2)->sum('amount');
        $rejected = $user->deposits()->orderBy('id','desc')->where('status',3)->sum('amount');
        $emptyMessage = 'No deposits';
        $scope = 'all';
        return view('admin.deposit.log', compact('pageTitle', 'user', 'deposits', 'emptyMessage','userId','scope','successful','pending','rejected'));
    }


    public function depViaMethod($method,$type = null,$userId){
        $method = Gateway::where('alias',$method)->firstOrFail();
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 1)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 3)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'successful'){
            $pageTitle = 'Successful Payment Via '.$method->name;
            $deposits = Deposit::where('status', 1)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }elseif($type == 'pending'){
            $pageTitle = 'Pending Payment Via '.$method->name;
            $deposits = Deposit::where('method_code','>=',1000)->where('user_id',$user->id)->where('method_code',$method->code)->where('status', 2)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }else{
            $pageTitle = 'Payment Via '.$method->name;
            $deposits = Deposit::where('status','!=',0)->where('user_id',$user->id)->where('method_code',$method->code)->orderBy('id','desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }
        $pageTitle = 'Deposit History: '.$user->username.' Via '.$method->name;
        $methodAlias = $method->alias;
        $emptyMessage = 'Deposit Log';
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits','methodAlias','userId'));
    }



    public function withdrawals(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', 'like',"%$search%")->orderBy('id','desc')->paginate(getPaginate());
            $emptyMessage = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'search', 'withdrawals', 'emptyMessage'));
        }
        $pageTitle = 'User Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawals';
        $userId = $user->id;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'withdrawals', 'emptyMessage','userId'));
    }

    public  function withdrawalsViaMethod($method,$type,$userId){
        $method = WithdrawMethod::findOrFail($method);
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawal of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 1)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }elseif($type == 'rejected'){
            $pageTitle = 'Rejected Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 3)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());

        }elseif($type == 'pending'){
            $pageTitle = 'Pending Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', 2)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $pageTitle = 'Withdrawals of '.$user->username.' Via '.$method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->where('user_id',$user->id)->with(['user','method'])->orderBy('id','desc')->paginate(getPaginate());
        }
        $emptyMessage = 'Withdraw Log Not Found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage','method'));
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Users';
        return view('admin.users.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (User::where('status', 1)->cursor() as $user) {
            sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All users will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id){
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect()->route('user.home');
    }

    public function emailLog($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Email log of '.$user->username;
        $logs = EmailLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.users.email_log', compact('pageTitle','logs','emptyMessage','user'));
    }

    public function emailDetails($id){
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.users.email_details', compact('pageTitle','email'));
    }

    public function sendHistory($id){
        $user = User::findOrFail($id);
        $logs = Send::where('user_id', $user->id)->latest()->with('wallet')->paginate(getPaginate());
        $pageTitle = 'Send Hitory of '.$user->username;
        $emptyMessage = 'Data Not Found';
        return view('admin.users.send_history', compact('pageTitle','logs', 'emptyMessage'));
    }

    public function receiveHistory($id){
        $user = User::findOrFail($id);
        $logs = Transaction::where('user_id', $user->id)->where('trx_type', '+')->latest()->with('wallet', 'user')->paginate(getPaginate());
        $pageTitle = 'Receive Hitory of '.$user->username;
        $emptyMessage = 'Data Not Found';
        return view('admin.users.receive_history', compact('pageTitle','logs', 'emptyMessage'));
    }

    public function userWallet($id){
        $user = User::findOrFail($id);
        $pageTitle = 'Wallet of '.$user->username;
        $wallets = UserWallet::where('user_id', $user->id)->latest()->paginate(getPaginate());
        $emptyMessage = 'Data Not Found';
        return view('admin.users.wallet', compact('pageTitle', 'wallets', 'emptyMessage'));
    }




}