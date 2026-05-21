<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    public $timestamps = false;

    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'qcm_name',
        'chapter_completed',
        'total_chapters',
        'score_so_far',
        'total_so_far',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
