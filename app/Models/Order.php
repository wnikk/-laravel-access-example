<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Wnikk\LaravelAccessRules\Traits\HasAccessScope;

/**
 * The record that permissions with conditions are about.
 *
 * HasAccessScope adds allowedTo(), the filter of lists. Relations declare their return
 * type: that is how the package tells a relation from any other method without calling it.
 */
class Order extends Model
{
    use HasAccessScope;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['locked' => 'boolean', 'created_at' => 'datetime'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function products(): BelongsToMany
    {
        // Conditions read "pivot.quantity", so the relation has to load the column.
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
