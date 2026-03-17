<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArcadeGameResource;
use App\Models\ActivityLog;
use App\Models\ArcadeGame;
use App\Models\GameScore;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $games = ArcadeGame::orderBy('sort_order', 'asc')->get();
        return $this->success(ArcadeGameResource::collection($games));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'slug' => 'nullable|string|unique:arcade_games,slug',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']['en'] ?? $data['name']['ar']);
        }
        $data['stats'] = ['play_count' => 0, 'unique_players' => 0, 'avg_score' => 0, 'highest_score' => 0];

        $game = ArcadeGame::create($data);
        ActivityLog::log('create', 'games', "أضاف لعبة: " . ($data['name']['ar'] ?? ''));
        return $this->created(new ArcadeGameResource($game));
    }

    public function show(string $id)
    {
        $game = ArcadeGame::find($id);
        if (!$game) return $this->notFound('اللعبة غير موجودة');

        $topScores = GameScore::where('game_id', $id)
            ->orderBy('score', 'desc')
            ->limit(20)
            ->get();

        return $this->success([
            'game'   => new ArcadeGameResource($game),
            'scores' => $topScores,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $game = ArcadeGame::find($id);
        if (!$game) return $this->notFound('اللعبة غير موجودة');
        $game->update($request->all());
        ActivityLog::log('update', 'games', 'عدّل لعبة', 'game', $id);
        return $this->success(new ArcadeGameResource($game), 'تم التحديث');
    }

    public function destroy(string $id)
    {
        $game = ArcadeGame::find($id);
        if (!$game) return $this->notFound('اللعبة غير موجودة');
        GameScore::where('game_id', $id)->delete();
        $game->delete();
        ActivityLog::log('delete', 'games', 'حذف لعبة', 'game', $id);
        return $this->success(null, 'تم الحذف');
    }
}