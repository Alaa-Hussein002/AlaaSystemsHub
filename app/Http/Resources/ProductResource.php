<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => (string) $this->_id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'short_description' => $this->short_description,
            'product_type'      => $this->product_type,
            'status'            => $this->status, // 🟢 مهم للـ React لمعرفة حالة المنتج (مسودة/منشور/مؤرشف)
            'pricing'           => $this->pricing,
            'media'             => $this->media,
            // 🟢 في الداتا بيس اسمها digital_asset لكن الـ React ينتظرها باسم digital_file
            'digital_file'      => $this->digital_asset, 
            // 🟢 في الداتا بيس اسمها physical_details لكن الـ React ينتظرها باسم physical_info
            'physical_info'     => [
                'weight'         => $this->physical_details['weight'] ?? '',
                'dimensions'     => $this->physical_details['dimensions'] ?? '',
                // 🟢 دمج الكمية هنا ليفهمها الـ React
                'stock_quantity' => $this->stock['quantity'] ?? 0, 
            ],
            'attributes'        => $this->attributes,
            'tags'              => $this->tags,
            'stats'             => [
                'views_count'    => $this->stats['views_count'] ?? 0,
                'sales_count'    => $this->stats['sales_count'] ?? 0,
                'rating_average' => $this->stats['rating_average'] ?? 0,
                'rating_count'   => $this->stats['rating_count'] ?? 0,
            ],
            'is_featured'       => $this->is_featured,
            'is_on_sale'        => $this->getIsOnSaleAttribute(),
            'category'          => new ProductCategoryResource($this->whenLoaded('category')),
            'created_at'        => $this->created_at?->toDateTimeString(),
            
            // 🟢 هنا تم توحيد الاسم ليصبح is_published كما ينتظره الـ React
            'is_published'      => $this->is_published, 
        ];
    }
}