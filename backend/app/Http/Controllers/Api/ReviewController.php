<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Review::with('user')->latest();

        // user voit seulement ses avis, admin voit tout
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // BONUS PRO: filtre sentiment ?sentiment=positive
        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->string('sentiment'));
        }

        // BONUS PRO: search ?q=mot
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where('content', 'like', "%{$q}%");
        }

        return ReviewResource::collection($query->paginate(10));
    }

    public function store(StoreReviewRequest $request)
    {
        $review = Review::create([
            'user_id' => $request->user()->id,
            'content' => $request->validated()['content'],
        ]);

        // Observer IA va remplir sentiment/score/topics après création
        $review->refresh()->load('user');

        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Review $review)
    {
        $this->authorize('view', $review);
        $review->load('user');
        return new ReviewResource($review);
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $review->update([
            'content' => $request->validated()['content'],
            // option: remettre à null pour signaler re-analyse (si tu ajoutes un observer updated)
            'sentiment' => null,
            'score' => null,
            'topics' => null,
        ]);

        $review->load('user');

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
