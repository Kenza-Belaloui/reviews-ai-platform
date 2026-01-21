<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\AI\AnalyzerManager;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $ai = (new AnalyzerManager())->driver();
        $result = $ai->analyze($review->content);

        $review->update([
            'sentiment' => $result['sentiment'],
            'score' => $result['score'],
            'topics' => $result['topics'],
        ]);
    }
}
