<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Deposit\AuthorizeController as AppAuthorizeController;
use App\Http\Controllers\Api\Deposit\FlutterwaveController as AppFlutterwaveController;
use App\Http\Controllers\Api\Deposit\InstamojoController as AppInstamojoController;
use App\Http\Controllers\Api\Deposit\ManualController;
use App\Http\Controllers\Api\Deposit\MollieController as AppMollieController;
use App\Http\Controllers\Api\Deposit\PaypalController as AppPaypalController;
use App\Http\Controllers\Api\Deposit\PaystackController;
use App\Http\Controllers\Api\Deposit\PaytmController as AppPaytmController;
use App\Http\Controllers\Api\Deposit\RazorpayController as AppRazorpayController;
use App\Http\Controllers\Api\Deposit\StripeController as AppStripeController;
use App\Http\Controllers\Api\Front\BlogController;
use App\Http\Controllers\Api\Front\FrontendController;
use App\Http\Controllers\Api\Subscription\AuthorizeController;
use App\Http\Controllers\Api\Subscription\FlutterwaveController;
use App\Http\Controllers\Api\Subscription\InstamojoController;
use App\Http\Controllers\Api\Subscription\MollieController;
use App\Http\Controllers\Api\Subscription\PaypalController;
use App\Http\Controllers\Api\Subscription\PaytmController;
use App\Http\Controllers\Api\Subscription\RazorpayController;
use App\Http\Controllers\Api\Subscription\StripeController;
use App\Http\Controllers\Api\User\BeneficiaryController;
use App\Http\Controllers\Api\User\DashboardController;
use App\Http\Controllers\Api\User\DepositController;
use App\Http\Controllers\Api\User\DpsController;
use App\Http\Controllers\Api\User\FdrController;
use App\Http\Controllers\Api\User\KYCController;
use App\Http\Controllers\Api\User\LoanController;
use App\Http\Controllers\Api\User\ReferralController;
use App\Http\Controllers\Api\User\RequestController;
use App\Http\Controllers\Api\User\SendController;
use App\Http\Controllers\Api\User\SubscriptionPlanController;
use App\Http\Controllers\Api\User\TicketController;
use App\Http\Controllers\Api\User\TransferLogController;
use App\Http\Controllers\Api\User\TwoFactorController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\UserOtherBankController;
use App\Http\Controllers\Api\User\WireTransferController;
use App\Http\Controllers\Api\User\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'user'], function () {
    Route::post('registration', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::get('logout', [AuthController::class,'logout']);
    Route::post('reset/password', [AuthController::class,'reset_password']);
    Route::post('change/password', [AuthController::class,'change_password']);
    Route::post('social/login', [AuthController::class,'social_login']);
    Route::post('refresh/token', [AuthController::class,'refresh']);
    Route::get('details', [AuthController::class,'details']);

    Route::group(['middleware' => 'auth:api'], function (){
        Route::get('dashboard',[DashboardController::class,'dashboard']);
        Route::get('transactions',[DashboardController::class,'transactions']);

        Route::post('loan-apply-amount', [LoanController::class,'loanApply'])->name('api.user.loan.amount');

        Route::get('loan-plans', [LoanController::class,'loanPlans']);
        Route::post('apply-for-loan', [LoanController::class,'loanAmount']);
        Route::post('loan-request', [LoanController::class,'loanRequest']);

        Route::get('all-loans', [LoanController::class,'loans']);
        Route::get('pending-loans', [LoanController::class,'pendingLoans']);
        Route::get('running-loans', [LoanController::class,'runningLoans']);
        Route::get('paid-loans', [LoanController::class,'paidLoans']);
        Route::get('rejected-loans', [LoanController::class,'rejectedLoans']);
        Route::get('loan-logs/{id}', [LoanController::class,'logs'])->name('api.user.loan.logs');

        Route::get('dps-plans', [DpsController::class,'dpsPlans']);
        Route::get('dps-apply/{id}', [DpsController::class,'dpsApply'])->name('api.user.dps.apply');
        Route::post('dps-submit', [DpsController::class,'dpsSubmit']);
        Route::get('all-dps', [DpsController::class,'index']);
        Route::get('running-dps', [DpsController::class,'running']);
        Route::get('matured-dps', [DpsController::class,'matured']);
        Route::get('dps-logs/{id}', [DpsController::class,'logs'])->name('api.user.dps.logs');

        Route::get('fdr-plans', [FdrController::class,'fdrPlans']);
        Route::post('apply-for-fdr', [FdrController::class,'fdrAmount']);
        Route::post('fdr-apply', [FdrController::class,'fdrRequest']);

        Route::get('all-fdr', [FdrController::class,'index']);
        Route::get('running-fdr', [FdrController::class,'running']);
        Route::get('closed-fdr', [FdrController::class,'closed']);

        Route::get('request-money/history',[RequestController::class,'requestHistory']);
        Route::post('request-money',[RequestController::class,'store']);
        Route::get('money-send/{id}',[RequestController::class,'send']);
        Route::get('request-details/{id}',[RequestController::class,'details']);
        Route::get('receive-request-money/history',[RequestController::class,'receiveHistory']);

        Route::get('/username/{accno}',[SendController::class,'accno'])->name('api.save.user.info');
        Route::post('/send-money',[SendController::class,'store']);
        Route::get('/send/money/cancel',[SendController::class,'cancel'])->name('api.user.send.money.cancel');
        Route::post('/save-account',[SendController::class,'saveAccount'])->name('api.user.save.account');
        Route::get('/save-account/list',[SendController::class,'list'])->name('api.user.save.account.list');

        Route::get('wire-transfer/banks',[WireTransferController::class,'banks']);
        Route::get('wire-transfer',[WireTransferController::class,'index']);
        Route::post('wire-transfer',[WireTransferController::class,'store']);
        Route::get('wire-transfer/{id}', [WireTransferController::class,'details'])->name('api.user.wire.transfer');

        Route::get('/other-banks', [BeneficiaryController::class,'otherBanks']);
        Route::get('/other-banks/{id}', [BeneficiaryController::class,'transfer'])->name('api.user.other.bank.transfer');
        Route::post('/other-bank/send', [BeneficiaryController::class,'sendMoney']);
        Route::get('/other-bank-transfer', [BeneficiaryController::class,'otherBanksTransfer']);
        Route::get('/beneficiaries', [BeneficiaryController::class,'index']);
        Route::post('/beneficiary/store', [BeneficiaryController::class,'store']);
        Route::get('/beneficiary/{id}', [BeneficiaryController::class,'show'])->name('api.user.beneficiary.show');

        Route::get('/other-bank',[UserOtherBankController::class,'index'])->name('api.user.other.bank');
        Route::get('/other-bank/{id}',[UserOtherBankController::class,'othersend']);
        Route::post('/other-bank/store', [UserOtherBankController::class,'store']);

        Route::get('/transfer-logs', [TransferLogController::class,'index']);

        Route::get('deposit/history',[DepositController::class,'history']);
        Route::post('/deposit',[DepositController::class,'deposit']);

        Route::get('/withdraw-methods', [WithdrawController::class,'methods']);
        Route::get('/withdraws', [WithdrawController::class,'index']);
        Route::post('/withdraw/store', [WithdrawController::class,'store']);
        Route::get('/withdraw/{id}', [WithdrawController::class,'details'])->name('api.user.withdraw.details');

        Route::get('/tickets', [TicketController::class,'index']);
        Route::post('/ticket/store', [TicketController::class,'store']);
        Route::get('ticket/{id}', [TicketController::class,'show'])->name('user.ticket.show');
        Route::post('ticket/reply', [TicketController::class,'reply']);
        Route::get('ticket/delete/{id}', [TicketController::class,'delete'])->name('user.ticket.delete');

        Route::get('referrers',[ReferralController::class,'referred']);
        Route::get('referrer-commissions',[ReferralController::class,'commissions']);

        Route::get('/subscription-plans',[SubscriptionPlanController::class,'index']);

        Route::get('/kyc-form',[KYCController::class,'kycform']);
        Route::post('/kyc-form/submit',[KYCController::class,'store']);

        Route::post('currency/update',[UserController::class,'updateCurrency']);
        Route::post('profile/update', [UserController::class,'update']);
        Route::post('password/update', [UserController::class,'updatePassword']);

        Route::get('/two-factor', [TwoFactorController::class,'showTwoFactorForm']);
        Route::post('/createTwoFactor', [TwoFactorController::class,'createTwoFactor']);
        Route::post('/disableTwoFactor', [TwoFactorController::class,'disableTwoFactor']);
        Route::post('/otp-submit', [TwoFactorController::class,'otp']);
    });

    Route::get('subscription-plan/{id}/{user_id}',[SubscriptionPlanController::class,'subscription'])->name('api.user.subscription.plan');

    Route::post('/api/subscription/stripe-submit', [StripeController::class,'store'])->name('api.subscription.stripe.submit');
    Route::get('/api/subscription/stripe/success', [StripeController::class,'notify'])->name('api.subscription.stripe.success');
    Route::post('/api/subscription/free', [SubscriptionPlanController::class,'store'])->name('api.subscription.free.submit');

    Route::post('/api/subscription/paypal-submit', [PaypalController::class,'store'])->name('api.subscription.paypal.submit');
    Route::get('/api/subscription/paypal/deposit/notify/{id}/{user_id}', [PaypalController::class,'notify'])->name('api.subscription.paypal.notify');
    Route::get('/api/subscription/paypal/deposit/cancel/{id}/{user_id}', [PaypalController::class,'cancel'])->name('api.subscription.paypal.cancel');

    Route::post('/api/subscription/instamojo-submit',[InstamojoController::class,'store'])->name('api.subscription.instamojo.submit');
    Route::get('/api/subscription/instamojo-notify',[InstamojoController::class,'notify'])->name('api.subscription.instamojo.notify');

    Route::post('/api/subscription/paytm-submit', [PaytmController::class,'store'])->name('api.subscription.paytm.submit');
    Route::post('/api/subscription/paytm-callback', [PaytmController::class,'paytmCallback'])->name('api.subscription.paytm.notify');

    Route::post('/api/subscription/razorpay-submit', [RazorpayController::class,'store'])->name('api.subscription.razorpay.submit');
    Route::post('/api/subscription/razorpay-notify', [RazorpayController::class,'notify'])->name('api.subscription.razorpay.notify');
    Route::get('/api/subscription/razorpay-notify/cancel/{id}', [RazorpayController::class,'cancel'])->name('api.subscription.razorpay.cancel');

    Route::post('/api/subscription/molly-submit', [MollieController::class,'store'])->name('api.subscription.molly.submit');
    Route::get('/api/subscription/molly-notify', [MollieController::class,'notify'])->name('api.subscription.molly.notify');

    Route::post('/api/subscription/flutter/submit', [FlutterwaveController::class,'store'])->name('api.subscription.flutter.submit');
    Route::post('/api/subscription/flutter/notify', [FlutterwaveController::class,'notify'])->name('api.subscription.flutter.notify');

    Route::post('/api/subscription/authorize-submit', [AuthorizeController::class,'store'])->name('api.subscription.authorize.submit');

    Route::get('/deposit/confirm/{id}/{userId}',[DepositController::class,'confirm_deposit'])->name('api.user.deposit.confirm');

    Route::post('/deposit/stripe-submit', [AppStripeController::class,'store'])->name('api.deposit.stripe.submit');
    Route::get('/deposit/stripe/success', [AppStripeController::class,'success'])->name('api.deposit.stripe.success');

    Route::post('/deposit/paystack/submit', [PaystackController::class,'store'])->name('api.deposit.paystack.submit');

    Route::post('/deposit/paypal-submit', [AppPaypalController::class,'store'])->name('api.deposit.paypal.submit');
    Route::get('/deposit/paypal/deposit/notify', [AppPaypalController::class,'notify'])->name('api.deposit.paypal.notify');
    Route::get('/deposit/paypal/deposit/cancel', [AppPaypalController::class,'cancel'])->name('api.deposit.paypal.cancel');

    Route::post('/deposit/instamojo-submit',[AppInstamojoController::class,'store'])->name('api.deposit.instamojo.submit');
    Route::get('/deposit/instamojo-notify',[AppInstamojoController::class,'notify'])->name('api.deposit.instamojo.notify');

    Route::post('/deposit/paytm-submit', [AppPaytmController::class,'store'])->name('api.deposit.paytm.submit');
    Route::post('/deposit/paytm-callback', [AppPaytmController::class,'paytmCallback'])->name('api.deposit.paytm.notify');

    Route::post('/deposit/razorpay-submit', [AppRazorpayController::class,'store'])->name('api.deposit.razorpay.submit');
    Route::post('/deposit/razorpay-notify', [AppRazorpayController::class,'notify'])->name('api.deposit.razorpay.notify');

    Route::post('/deposit/molly-submit', [AppMollieController::class,'store'])->name('api.deposit.molly.submit');
    Route::get('/deposit/molly-notify', [AppMollieController::class,'notify'])->name('api.deposit.molly.notify');

    Route::post('/deposit/flutter/submit', [AppFlutterwaveController::class,'store'])->name('api.deposit.flutter.submit');
    Route::post('/deposit/flutter/notify', [AppFlutterwaveController::class,'notify'])->name('api.deposit.flutter.notify');

    Route::post('/deposit/authorize-submit', [AppAuthorizeController::class,'store'])->name('api.deposit.authorize.submit');
    Route::post('/deposit/manual-submit', [ManualController::class,'store'])->name('api.deposit.manual.submit');

});

Route::get('banner',[FrontendController::class,'banner']);
Route::get('feature',[FrontendController::class,'feature']);
Route::get('about',[FrontendController::class,'about']);
Route::get('works',[FrontendController::class,'works']);
Route::get('plans',[FrontendController::class,'plans']);
Route::get('apps',[FrontendController::class,'apps']);
Route::get('testimonials',[FrontendController::class,'testimonials']);
Route::get('counters',[FrontendController::class,'counters']);
Route::get('cta',[FrontendController::class,'cta']);

Route::get('services',[FrontendController::class,'services']);
Route::get('faqs',[FrontendController::class,'faqs']);

Route::get('pages',[FrontendController::class,'pages']);
Route::get('page/{slug}',[FrontendController::class,'page_details'])->name('page.details');

Route::get('all-blogs',[BlogController::class,'blogs']);
Route::get('recent-blogs',[BlogController::class,'recentBlogs']);
Route::get('blog-category',[BlogController::class,'blogCategory']);
Route::get('category-blogs/{slug}',[BlogController::class,'categoryBlogs']);
Route::get('blogs/{slug}',[BlogController::class,'blogDetails']);

Route::post('contact',[FrontendController::class,'contact']);
Route::get('contact-info',[FrontendController::class,'info']);

Route::get('languages',[FrontendController::class,'languages']);
Route::get('default-language',[FrontendController::class,'defaultLanguage']);
Route::get('language/{id}',[FrontendController::class,'language']);

Route::get('currencies',[FrontendController::class,'currencies']);
Route::get('default-currency',[FrontendController::class,'defaultCurrency']);
Route::get('currency/{id}',[FrontendController::class,'currency']);
