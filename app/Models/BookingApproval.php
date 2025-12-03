<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingApproval extends Model {
    protected $fillable = ['booking_id', 'approver_id', 'comments', 'action'];

    public function booking() {
        return $this->belongsTo(Booking::class);
    }
}