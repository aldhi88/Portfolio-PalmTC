<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TcSampleFile extends Model
{
    protected $guarded = [];

    public function tc_samples()
    {
        return $this->belongsTo(TcSample::class, 'tc_sample_id', 'id');
    }
}
