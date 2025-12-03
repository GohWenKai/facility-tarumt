<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\BookingRepositoryInterface; // Import Interface

class UserProfileController extends Controller
{
    protected $bookingRepo;

    // 1. Dependency Injection (Repository Pattern)
    public function __construct(BookingRepositoryInterface $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }

    public function show()
    {
        $user = Auth::user();
        
        // Use Repository to get data
        $bookings = $this->bookingRepo->getRecentBookings($user, 5);
        
        return view('users.profile.show', compact('user', 'bookings'));
    }

    public function edit()
    {
        $user = Auth::user();
        
        // REUSE the same Repository logic (No code duplication!)
        $recentBookings = $this->bookingRepo->getRecentBookings($user, 5);

        return view('users.profile.update', compact('user', 'recentBookings'));
    }

    // Handle the Update
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validation (Cleaned up logic)
        $request->validate([
            'address' => 'required|string|max:2000',
            'tel' => ['required', 'string', 'regex:/^\+60\d{9,10}$/'],
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // 2. Business Logic
        // (You could move this to a Service, but for this module, keeping it here is acceptable 
        // since we are focusing on the Repository Pattern for the read operations).
        
        $user->address = $request->address;
        $user->tel = $request->tel;

        if ($request->filled('new_password')) {
            // Check Old Password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}