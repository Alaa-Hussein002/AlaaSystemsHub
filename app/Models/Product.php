<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'category_id',
        'product_type',
        'pricing',
        'media',
        'digital_asset',
        'physical_details',
        'attributes',
        'tags',
        'stats',
        'stock',
        'status',
        'is_featured',
        'is_published',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'pricing' => 'array',
        'media' => 'array',
        'digital_asset' => 'array',
        'physical_details' => 'array',
        'attributes' => 'array',
        'tags' => 'array',
        'stats' => 'array',
        'stock' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_published', true);
    }

    public function scopeDigital($query)
    {
        return $query->where('product_type', 'digital');
    }

    public function scopePhysical($query)
    {
        return $query->where('product_type', 'physical');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helpers
    public function getPriceAttribute()
    {
        return $this->pricing['price'] ?? 0;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !empty($this->pricing['compare_at_price']) &&
            $this->pricing['compare_at_price'] > $this->pricing['price'];
    }

    public function incrementViews()
    {
        $stats = is_array($this->stats) ? $this->stats : [];
        $stats['views_count'] = ($stats['views_count'] ?? 0) + 1;
        $this->update(['stats' => $stats]);
    }

    public function incrementSales(int $count = 1)
    {
        $stats = is_array($this->stats) ? $this->stats : [];
        $stats['sales_count'] = ($stats['sales_count'] ?? 0) + $count;
        $this->update(['stats' => $stats]);
    }
}