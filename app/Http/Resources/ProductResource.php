<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pricing = $this->pricing ?? [];

        // 🟢 فحص تواريخ العرض (بداية ونهاية) مباشرة في الـ Resource
        if (isset($pricing['offer_type']) && $pricing['offer_type'] !== 'none') {
            $now = time();
            $shouldHideOffer = false;

            // 1. هل العرض لم يبدأ بعد؟
            if (!empty($pricing['offer_start']) && strtotime($pricing['offer_start']) > $now) {
                $shouldHideOffer = true;
            }

            // 2. هل العرض قد انتهى؟
            if (!empty($pricing['offer_end']) && strtotime($pricing['offer_end']) <= $now) {
                $shouldHideOffer = true;
            }

            // إذا كان العرض لم يبدأ أو انتهى، نخفي الخصم عن العميل
            if ($shouldHideOffer) {
                $pricing['offer_type'] = 'none';
                $pricing['discount_value'] = 0;
                $pricing['is_free'] = false;
            }
        }
        
        return [
            'id'                => (string) $this->_id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'short_description' => $this->short_description,
            'product_type'      => $this->product_type,
            'status'            => $this->status,
            'pricing'           => $this->pricing,
            'media'             => $this->media,
            'digital_file'      => $this->digital_asset, 
            'physical_info'     => [
                'weight'         => $this->physical_details['weight'] ?? '',
                'dimensions'     => $this->physical_details['dimensions'] ?? '',
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
            'is_published'      => $this->is_published, 
        ];
    }
}