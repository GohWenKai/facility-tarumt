<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Announcement;
use App\Models\Notification;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard with stats and upcoming bookings.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get booking statistics
        $stats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'pending' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Booking::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Booking::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];
        
        // Get upcoming bookings (next 7 days, approved only)
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_time', '>=', Carbon::now())
            ->where('start_time', '<=', Carbon::now()->addDays(7))
            ->with('facility')
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get();
        
        // Get pending bookings for notification
        $pendingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('facility')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Get active announcements
        $announcements = Announcement::active()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('users.dashboard', compact('user', 'stats', 'upcomingBookings', 'pendingBookings', 'announcements'));
    }
}
