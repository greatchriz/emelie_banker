<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\BankPlan;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        try {
            $data = BankPlan::all();

            return response()->json(['status' => true, 'data' => PackageResource::collection($data), 'error' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
        }
    }

    public function store(Request $request)
    {

        $user = User::findorFail($request->user_id);
        if ($user) {
            $bankplan = BankPlan::whereId($request->bank_plan_id)->first();
            $user->bank_plan_id = $bankplan->id;
            $user->plan_end_date = now()->addDays($bankplan->days);
            $user->update();
            $data['get'] = json_encode(['status' => true, 'data' => "Bank plan updated successfully", 'error' => []]);
            return view('frontend.api_payment', $data);
        }

        $data['get'] = json_encode(['status' => false, 'data' => "User not found", 'error' => []]);
        return view('frontend.api_payment', $data);
    }

    public function subscription($id, $user_id)
    {
        if (!$user_id) {
            auth()->logout();
            return response()->json(['status' => false, 'data' => [], 'error' => 'Login First!']);
        }

        $data['user'] = User::findOrFail($user_id);
        $data['api_currency'] = Currency::findOrFail($data['user']->currency_id);

        $data['user_id'] = $user_id;
        $data['data'] = BankPlan::findOrFail($id);
        $data['availableGatways'] = ['flutterwave', 'authorize.net', 'razorpay', 'mollie', 'paytm', 'instamojo', 'stripe', 'paypal'];
        $data['gateways'] = PaymentGateway::OrderBy('id', 'desc')->whereStatus(1)->get();

        return view('user.package.api_subscription', $data);
    }
}
