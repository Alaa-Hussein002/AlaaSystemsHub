<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name',              // { ar, en }
        'slug',
        'description',       // { ar, en }
        'short_description',
        'category_id',
        'product_type',      // digital | physical
        'pricing',           // embedded { price, compare_at_price, currency, is_free, discount_percentage }
        'media',             // embedded { thumbnail, gallery[], demo_video, preview_url }
        'digital_asset',     // embedded { file_url, file_size, file_format, version, download_limit }
        'physical_details',  // embedded (for future) { weight, dimensions, sku }
        'attributes',        // dynamic embedded document
        'tags',
        'stats',             // embedded { views_count, sales_count, rating_average, etc. }
        'stock',             // embedded { track_inventory, quantity, allow_backorder }
        'status',            // draft | published | archived
        'is_featured',
        'is_active',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'name'             => 'array',
        'description'      => 'array',
        'pricing'          => 'array',
        'media'            => 'array',
        'digital_asset'    => 'array',
        'physical_details' => 'array',
        'attributes'       => 'array',
        'tags'             => 'array',
        'stats'            => 'array',
        'stock'            => 'array',
        'is_featured'      => 'boolean',
        'is_active'        => 'boolean',
        'sort_order'       => 'integer',
        'published_at'     => 'datetime',
    ];

    // ====================================
    // العلاقات
    // ====================================

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // ====================================
    // Scopes
    // ====================================

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
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

    // ====================================
    // Accessors
    // ====================================

    public function getPriceAttribute()
    {
        return $this->pricing['price'] ?? 0;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !empty($this->pricing['compare_at_price']) &&
               $this->pricing['compare_at_price'] > $this->pricing['price'];
    }

    // ====================================
    // Helpers
    // ====================================

    public function incrementViews()
    {
        $this->increment('stats.views_count');
    }

    public function incrementSales(int $count = 1)
    {
        $this->increment('stats.sales_count', $count);
    }
}