<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    // The Student User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Staff Relationships
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function visaConsultant()
    {
        return $this->belongsTo(User::class, 'visa_consultant_id');
    }

    public function travelAgent()
    {
        return $this->belongsTo(User::class, 'travel_agent_id');
    }
}
