<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $base = Review::query();
        if ($user->role !== 'admin') {
            $base->where('user_id', $user->id);
        }

        $total = (clone $base)->count();

        $bySentiment = (clone $base)
            ->select('sentiment', DB::raw('COUNT(*) as c'))
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->pluck('c', 'sentiment')
            ->toArray();

        $avgScore = (clone $base)->whereNotNull('score')->avg('score');
        $avgScore = $avgScore ? (int) round($avgScore) : null;

        // Derniers avis
        $recent = (clone $base)
            ->with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'content' => $r->content,
                'sentiment' => $r->sentiment,
                'score' => $r->score,
                'topics' => $r->topics ?? [],
                'created_at' => $r->created_at?->toISOString(),
                'user' => [
                    'id' => $r->user?->id,
                    'name' => $r->user?->name,
                ],
            ]);

        // Top topics (SQLite: on “explode” côté PHP)
        $topicsCount = [];
        foreach ((clone $base)->whereNotNull('topics')->pluck('topics') as $topics) {
            $arr = is_array($topics) ? $topics : (json_decode($topics, true) ?: []);
            foreach ($arr as $t) {
                $topicsCount[$t] = ($topicsCount[$t] ?? 0) + 1;
            }
        }
        arsort($topicsCount);
        $topTopics = array_slice(array_map(fn($k,$v)=>['topic'=>$k,'count'=>$v], array_keys($topicsCount), $topicsCount), 0, 5);

        return response()->json([
            'scope' => $user->role === 'admin' ? 'all' : 'mine',
            'total_reviews' => $total,
            'avg_score' => $avgScore,
            'sentiments' => [
                'positive' => (int)($bySentiment['positive'] ?? 0),
                'neutral'  => (int)($bySentiment['neutral'] ?? 0),
                'negative' => (int)($bySentiment['negative'] ?? 0),
            ],
            'top_topics' => $topTopics,
            'recent_reviews' => $recent,
        ]);
    }
}
