<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoExhibitor extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'application_id',
        'co_exhibitor_name',
        'contact_person',
        'email',
        'phone',
        'status',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
