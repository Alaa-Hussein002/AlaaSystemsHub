<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => (string) $this->_id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'icon'           => $this->icon,
            'image'          => $this->image,
            'product_type'   => $this->product_type,
            'products_count' => $this->products_count ?? 0,
            'sort_order'     => $this->sort_order,
            'children'       => ProductCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}