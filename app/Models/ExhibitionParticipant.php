<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionParticipant extends Model
{
    //
    use HasFactory;

    protected $fillable = ['application_id', 'stall_manning_count', 'complimentary_delegate_count'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function stallManning()
    {
        return $this->hasMany(StallManning::class);
    }

    public function complimentaryDelegates()
    {
        return $this->hasMany(ComplimentaryDelegate::class);
    }

    public function exhibitionParticipantPasses()
    {
        return $this->hasMany(ExhibitionParticipantPass::class, 'participant_id');
    }
}
