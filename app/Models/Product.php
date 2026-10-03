<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Wnikk\LaravelAccessRules\Traits\HasAccessScope;

/**
 * Example 18: tags of a product are polymorphic, categories carry them too.
 * The morph map lives in App\Providers\AppServiceProvider.
 */
class Product extends Model
{
    use HasAccessScope;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['restricted' => 'boolean'];

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
