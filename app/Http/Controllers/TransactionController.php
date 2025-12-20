<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Eager load order by latest
        $transactions = $user->transactions()
            ->paginate(15);
            
        return view('users.transactions.index', compact('transactions'));
    }
}
