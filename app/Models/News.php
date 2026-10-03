<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wnikk\LaravelAccessRules\Traits\HasAccessScope;

/**
 * Class News
 *
 * @property $id
 * @property $user_id
 * @property $name
 * @property $description
 * @property $body
 */
class News extends Model
{
    use HasFactory, SoftDeletes, HasAccessScope;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'body',
    ];
}
