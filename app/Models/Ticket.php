<?php

// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'bts_25_del_ticket_tbl'; // Use your FormConstants if needed

    protected $fillable = [
        'ticket_type',
        'nationality',
        'early_bird_date',
        'early_bird_price',
        'normal_price',
        'status',
    ];
}
