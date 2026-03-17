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
            'pricing'           => $this->pricing,
            'media'             => $this->media,
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
        ];
    }
}