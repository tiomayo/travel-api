<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Trip extends Model
{
    use HasFactory, Traits\WithUuid;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'origin',
        'destination',
        'start',
        'end',
        'type',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [

    ];

    protected static function booted()
    {
        static::created(function ($model) {
            if (Cache::has($model->user_id)) {
                Cache::forget($model->user_id);
            }
        });
        static::updated(function ($model) {
            if (Cache::has($model->user_id)) {
                Cache::forget($model->user_id);
            }
        });
        static::deleted(function ($model) {
            if (Cache::has($model->user_id)) {
                Cache::forget($model->user_id);
            }
        });
    }
}
