<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class TransferLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $data['logs'] = BalanceTransfer::with(['receiver', 'beneficiary.bank', 'bank'])
            ->whereUserId(auth()->id())
            ->orderBy('id','desc')
            ->paginate(10);
        return view('user.transfer.index',$data);
    }

    public function show($id)
    {
        $data['log'] = $this->findUserTransfer($id);
        return view('user.transfer.show', $data);
    }

    public function download($id)
    {
        $log = $this->findUserTransfer($id);

        try {
            $pdf = PDF::loadView('user.transfer.receipt-pdf', compact('log'));
            return $pdf->download('transfer-receipt-'.$log->transaction_no.'.pdf');
        } catch (\Exception $e) {
            Log::error('Transfer receipt download failed: '.$e->getMessage(), [
                'transfer_id' => $log->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('transfer.logs.show', $log->id)
                ->with('warning', 'Receipt download is temporarily unavailable. Please try again later.');
        }
    }

    private function findUserTransfer($id)
    {
        return BalanceTransfer::with(['user', 'receiver', 'beneficiary.bank', 'bank'])
            ->whereUserId(auth()->id())
            ->whereId($id)
            ->firstOrFail();
    }
}
