<?php
// app/Models/ProductCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'parent_id',
        'product_type',
        'sort_order',
        'is_active',
        'products_count',
        'meta',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'products_count' => 'integer',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function updateProductsCount()
    {
        $this->update([
            'products_count' => $this->products()->where('is_published', true)->count()
        ]);
    }
}