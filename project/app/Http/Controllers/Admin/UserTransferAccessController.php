<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserTransferAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(50);

        return view('admin.user.transfer-access', compact('users'));
    }

    public function toggle(Request $request, User $user)
    {
        $user->transfer_access = $request->has('transfer_access') ? 1 : 0;
        $user->save();

        return redirect()->route('user.transfer.access')->with('message', 'Transfer access updated successfully.');
    }
}
