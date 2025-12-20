<?php
namespace App\Repositories;

use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Models\User;
use App\Models\Booking;

class BookingRepository implements BookingRepositoryInterface
{
    // Used by UserProfileController & AdminProfileController (edit)
    public function getRecentBookings(User $user, int $perPage = 5)
    {
        return $user->bookings()
                    ->with('facility')
                    ->latest()
                    ->paginate($perPage);
    }

    // Used by AdminProfileController (show)
    public function getAllSystemBookings(int $limit = 5)
    {
        return Booking::with(['user', 'facility']) // Eager load user too
                    ->latest()
                    ->take($limit)
                    ->get();
    }
}