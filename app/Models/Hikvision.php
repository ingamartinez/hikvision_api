<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hikvision extends Model
{
    protected $table = 'hikvision';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['userId', 'title', 'completed','date'];

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'completed' => 'boolean',
        'date' => 'datetime:Y-m-d',
    ];
}
