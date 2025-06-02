<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelPersonalDetail extends Model
{
    //
    protected $table = 'bts_25_del_personal_detail_tbl';
    public function organization()
    {
        return $this->belongsTo(DelOrgDetail::class, 'tin_no', 'tin_no');
    }

}
