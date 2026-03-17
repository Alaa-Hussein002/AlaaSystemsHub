<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => (string) $this->_id,
            'player_name'         => $this->player_name,
            'score'               => $this->score,
            'level_reached'       => $this->level_reached,
            'time_played_seconds' => $this->time_played_seconds,
            'game_data'           => $this->game_data,
            'created_at'          => $this->created_at?->toDateTimeString(),
        ];
    }
}