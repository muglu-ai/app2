<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelOrgDetail extends Model
{
    //
    use HasFactory;
    protected $table = 'bts_25_del_org_detail_tbl';

    protected $guarded = [];

    public function delegates()
    {
        return $this->hasMany(DelPersonalDetail::class, 'tin_no', 'tin_no');
    }
}
