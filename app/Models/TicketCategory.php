<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TicketCategory extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'ticket_type',
        'nationality',
    ];

    // Relationships
    public function passes()
    {
        return $this->hasMany(ExhibitionParticipantPass::class, 'ticket_category_id');
    }
}
