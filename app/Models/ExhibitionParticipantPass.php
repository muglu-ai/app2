<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ExhibitionParticipantPass extends Model
{

    use HasFactory;
    // table name as exhibition_participant_passes
    protected $table = 'exhibition_participant_passes';
    //

    protected $fillable = [
        'participant_id',
        'ticket_category_id',
        'badge_count'
    ];

    public function participant()
    {
        return $this->belongsTo(ExhibitionParticipant::class, 'participant_id');
    }

    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }
}
