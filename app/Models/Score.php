<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'qcm_name',
        'score',
        'total',
        'percentage',
        'duration_seconds',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
