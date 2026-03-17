<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArcadeGameResource;
use App\Http\Resources\GameScoreResource;
use App\Models\AnalyticsEvent;
use App\Models\ArcadeGame;
use App\Models\Coupon;
use App\Models\GameScore;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    use ApiResponse;

    /**
     * قائمة الألعاب
     * GET /api/public/games
     */
    public function index()
    {
        $games = ArcadeGame::active()
            ->orderBy('sort_order', 'asc')
            ->get();

        return $this->success(
            ArcadeGameResource::collection($games),
            'صالة الألعاب'
        );
    }

    /**
     * تفاصيل لعبة
     * GET /api/public/games/{slug}
     */
    public function show(string $slug)
    {
        $game = ArcadeGame::active()
            ->where('slug', $slug)
            ->first();

        if (!$game) {
            return $this->notFound('اللعبة غير موجودة');
        }

        // أعلى 10 نتائج
        $leaderboard = GameScore::where('game_id', (string) $game->_id)
            ->where('is_verified', true)
            ->orderBy('score', 'desc')
            ->limit(10)
            ->get();

        return $this->success([
            'game'        => new ArcadeGameResource($game),
            'leaderboard' => GameScoreResource::collection($leaderboard),
        ], 'تفاصيل اللعبة');
    }

    /**
     * بدء اللعب (تسجيل الزيارة)
     * POST /api/public/games/{slug}/play
     */
    public function play(string $slug)
    {
        $game = ArcadeGame::active()
            ->where('slug', $slug)
            ->first();

        if (!$game) {
            return $this->notFound('اللعبة غير موجودة');
        }

        $game->incrementPlayCount();

        // تسجيل في التحليلات
        AnalyticsEvent::create([
            'event_type'     => 'game_play',
            'event_category' => 'games',
            'target_type'    => 'game',
            'target_id'      => (string) $game->_id,
            'target_title'   => $game->name['ar'] ?? $game->name,
            'visitor'        => [
                'ip_hash'    => md5(request()->ip()),
                'session_id' => session()->getId(),
            ],
            'page_url' => request()->fullUrl(),
        ]);

        return $this->success(
            new ArcadeGameResource($game),
            'استمتع باللعب!'
        );
    }

    /**
     * حفظ النتيجة
     * POST /api/public/games/{slug}/score
     */
    public function submitScore(Request $request, string $slug)
    {
        $request->validate([
            'player_name'         => 'required|string|min:2|max:30',
            'score'               => 'required|integer|min:0',
            'level_reached'       => 'nullable|integer|min:0',
            'time_played_seconds' => 'nullable|integer|min:0',
            'game_data'           => 'nullable|array',
        ], [
            'player_name.required' => 'اسم اللاعب مطلوب',
            'score.required'       => 'النتيجة مطلوبة',
        ]);

        $game = ArcadeGame::active()
            ->where('slug', $slug)
            ->first();

        if (!$game) {
            return $this->notFound('اللعبة غير موجودة');
        }

        $user = $request->user();

        $gameScore = GameScore::create([
            'game_id'             => (string) $game->_id,
            'user_id'             => $user ? (string) $user->_id : null,
            'player_name'         => $request->player_name,
            'score'               => $request->score,
            'level_reached'       => $request->level_reached ?? 0,
            'time_played_seconds' => $request->time_played_seconds ?? 0,
            'game_data'           => $request->game_data ?? [],
            'device_info'         => [
                'type'    => $this->detectDevice(),
                'browser' => $request->header('User-Agent'),
            ],
            'ip_hash'     => md5($request->ip()),
            'is_verified' => true,
        ]);

        // تحديث أعلى نتيجة في اللعبة
        $currentHighest = $game->stats['highest_score'] ?? 0;
        if ($request->score > $currentHighest) {
            $stats = $game->stats ?? [];
            $stats['highest_score'] = $request->score;
            $game->update(['stats' => $stats]);
        }

        // فحص المكافآت
        $reward = null;
        if (($game->rewards['enable_rewards'] ?? false) &&
            $request->score >= ($game->rewards['min_score_for_reward'] ?? PHP_INT_MAX)) {
            $reward = [
                'message'     => $game->rewards['reward_description'] ?? 'مبروك!',
                'coupon_code' => $game->rewards['reward_value'] ?? null,
            ];
        }

        // ترتيب اللاعب
        $rank = GameScore::where('game_id', (string) $game->_id)
            ->where('score', '>', $request->score)
            ->count() + 1;

        return $this->created([
            'score'  => new GameScoreResource($gameScore),
            'rank'   => $rank,
            'reward' => $reward,
        ], "تم حفظ نتيجتك! ترتيبك: #{$rank}");
    }

    /**
     * لوحة المتصدرين
     * GET /api/public/games/{slug}/leaderboard
     */
    public function leaderboard(Request $request, string $slug)
    {
        $game = ArcadeGame::active()
            ->where('slug', $slug)
            ->first();

        if (!$game) {
            return $this->notFound('اللعبة غير موجودة');
        }

        $limit = min($request->get('limit', 20), 100);

        $scores = GameScore::where('game_id', (string) $game->_id)
            ->where('is_verified', true)
            ->orderBy('score', 'desc')
            ->limit($limit)
            ->get();

        return $this->success([
            'game_name'   => $game->name,
            'total_plays' => $game->stats['play_count'] ?? 0,
            'scores'      => GameScoreResource::collection($scores),
        ], 'لوحة المتصدرين');
    }

    private function detectDevice(): string
    {
        $agent = strtolower(request()->header('User-Agent', ''));
        if (str_contains($agent, 'mobile')) return 'mobile';
        if (str_contains($agent, 'tablet')) return 'tablet';
        return 'desktop';
    }
}