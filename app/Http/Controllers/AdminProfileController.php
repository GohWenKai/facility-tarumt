<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\BookingRepositoryInterface; // Import Repository
use App\Http\Requests\UpdateProfileRequest; // Import Form Request

class AdminProfileController extends Controller
{
    protected $bookingRepo;

    // 1. Dependency Injection (Repository Pattern)
    public function __construct(BookingRepositoryInterface $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }

    // Show the Profile View (Dashboard)
    public function show()
    {
        $user = Auth::user();
        
        // 2. Use Repository to get GLOBAL system bookings
        $recentBookings = $this->bookingRepo->getAllSystemBookings(5);

        return view('admin.profile.show', compact('user', 'recentBookings'));
    }

    public function edit()
    {
        $user = Auth::user();
        
        // 3. Use Repository to get ADMIN'S personal bookings
        $recentBookings = $this->bookingRepo->getRecentBookings($user, 5);

        return view('admin.profile.update', compact('user', 'recentBookings'));
    }

    // Handle the Update (Using Form Request Validation)
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        
        // The $request is already validated here due to the FormRequest class.
        
        // Update Basic Info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->tel = $request->tel;

        // Password Logic
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}