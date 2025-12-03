<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'image_path'];

    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}