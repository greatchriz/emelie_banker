<?php

namespace App\Http\Controllers\User;

use App\Classes\GoogleAuthenticator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\BalanceTransfer;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use App\Traits\Payout;
use Auth;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\WalletService;
use PDF;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(WalletService $wallet)
    {
        $data['user'] = Auth::user();  
        $data['activeAccounts'] = $wallet->activeAccounts($data['user']);
        $data['accounts'] = $data['user']->accounts()
            ->with('plan')
            ->withCount([
                'deposits',
                'withdraws',
                'transactions',
                'outgoingTransfers as transfers_count',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
        $data['accounts']->each(function ($account) {
            $account->setRelation('latestTransactions', $account->transactions()->latest()->limit(5)->get());
        });
        $data['transactions'] = Transaction::whereUserId(auth()->id())->orderBy('id','desc')->limit(5)->get();
        $data['recentTransfers'] = BalanceTransfer::with(['receiver', 'beneficiary.bank', 'bank'])
            ->whereUserId(auth()->id())
            ->orderBy('id','desc')
            ->limit(5)
            ->get();
        return view('user.dashboard',$data);
    }

    public function transaction(Request $request, WalletService $wallet)
    {
        $user = Auth::user();
        $accounts = $user->accounts()->orderByDesc('created_at')->orderByDesc('id')->get();
        $selectedAccount = $wallet->accountFromRequest($user, $request->query('account_id'), false);
        $transactions = Transaction::whereUserId(auth()->id())
            ->when($selectedAccount, function ($query) use ($selectedAccount) {
                $query->where('account_id', $selectedAccount->id);
            })
            ->orderBy('id','desc')
            ->paginate(20)
            ->appends($request->only('account_id'));
        return view('user.transactions',compact('user','transactions','accounts','selectedAccount'));
    }

    public function profile()
    {
        $user = Auth::user();  
        return view('user.profile',compact('user'));
    }

    public function profileupdate(Request $request)
    {
        $request->validate([
            'photo' => 'nullable|mimes:jpeg,jpg,png,svg',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.Auth::user()->id,
            'phone' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $input = $request->only(['name', 'email', 'phone', 'zip', 'city', 'fax', 'address']);
        $data = Auth::user();        
        if ($file = $request->file('photo')) 
        {              
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images/',$name);
            @unlink('assets/images/'.$data->photo);
        
            $input['photo'] = $name;

            $input['is_provider'] = 0;
        }
         
        $data->update($input);
        $msg = 'Successfully updated your profile';
        return redirect()->back()->with('success',$msg);
    }

    public function changePasswordForm()
    {
        return view('user.changepassword');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if ($request->cpass){
            if (Hash::check($request->cpass, $user->password)){
                if ($request->newpass == $request->renewpass){
                    $input['password'] = Hash::make($request->newpass);
                }else{
                    return redirect()->back()->with('unsuccess','Confirm password does not match.');
                }
            }else{
                return redirect()->back()->with('unsuccess','Current password Does not match.'); 
            }
        }
        $user->update($input);
        return redirect()->back()->with('success','Password Successfully Changed.'); 
    }

    public function showTwoFactorForm()
    {
        $gnl = Generalsetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->name . '@' . $gnl->title, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->name . '@' . $gnl->title, $prevcode);

        return view('user.twofactor.index', compact('secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function createTwoFactor(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        if ($oneCode == $request->code) {
            $user->go = $request->key;
            $user->twofa = 1;
            $user->save();
            
            return redirect()->back()->with('success','Two factor authentication activated');
        } else {
            return redirect()->back()->with('error','Something went wrong!');
        }
    }


    public function disableTwoFactor(Request $request)
    {

        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->go;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->go = null;
            $user->twofa = 0;

            $user->save();

            return redirect()->back()->with('success','Two factor authentication disabled');
        } else {
            return redirect()->back()->with('error','Something went wrong!');
        }
    }

    public function username($number){
       if($account = UserAccount::where('account_number',$number)->where('status','active')->with('user')->first()){
           return $account->user->name;
       }elseif($data = User::where('account_number',$number)->first()){
           return $data->name;
       }else{
           return false;
       }
    }

    public function generatePDF()
    {
        $data = [
            'title' => 'Welcome to geniusbank',
            'date' => date('m/d/Y')
        ];
          
        $pdf = PDF::loadView('frontend.myPDF', $data);
    
        return $pdf->download('transaction.pdf');
    }

    public function affilate_code()
    {
        $user = Auth::guard('web')->user();
        return view('user.affilate_code',compact('user'));
    }


}
