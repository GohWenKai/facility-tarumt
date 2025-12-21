<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model {
    protected $fillable = ['facility_id', 'name', 'type', 'serial_number', 'condition', 'maintenance_note'];
    
    public function facility() {
        return $this->belongsTo(Facility::class);
    }
}