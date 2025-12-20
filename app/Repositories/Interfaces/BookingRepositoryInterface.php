<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface BookingRepositoryInterface
{
    // Existing method for specific user (Student/Lecturer)
    public function getRecentBookings(User $user, int $perPage);

    // NEW method for Admin (View all system bookings)
    public function getAllSystemBookings(int $limit);

}