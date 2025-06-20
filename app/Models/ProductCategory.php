<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    //
    use HasFactory;

    protected $fillable = ['name'];

    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_products');
    }
}
