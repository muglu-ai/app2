<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StallManning extends Model
{
    //
    use HasFactory;

    protected $fillable = ['exhibition_participant_id', 'first_name', 'last_name', 'email', 'mobile', 'job_title', 'organisation_name'];

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class);
    }
}
