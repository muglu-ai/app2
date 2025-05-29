<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    use HasFactory;

    protected $table = 'attendees';

    protected $fillable = [
        'unique_id',
        'status',
        'badge_category',
        'title',
        'first_name',
        'last_name',
        'designation',
        'company',
        'address',
        'country',
        'state',
        'city',
        'postal_code',
        'mobile',
        'email',
        'purpose',
        'products',
        'business_nature',
        'job_function',
        'consent',
        'qr_code_path',
        'source'
    ];

    protected $casts = [
        'purpose' => 'array',
        'products' => 'array',
        'consent' => 'boolean',
    ];
}
