<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArcadeGameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => (string) $this->_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'game_type'   => $this->game_type,
            'difficulty'  => $this->difficulty,
            'cover_image' => $this->cover_image,
            'screenshots' => $this->screenshots,
            'game_config' => $this->game_config,
            'controls'    => $this->controls,
            'stats'       => [
                'play_count'     => $this->stats['play_count'] ?? 0,
                'unique_players' => $this->stats['unique_players'] ?? 0,
                'avg_score'      => $this->stats['avg_score'] ?? 0,
                'highest_score'  => $this->stats['highest_score'] ?? 0,
            ],
            'rewards'     => $this->rewards,
            'is_featured' => $this->is_featured,
            'tags'        => $this->tags,
        ];
    }
}