<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function reviewsCsv(Request $request): StreamedResponse
    {
        $user = $request->user();

        $q = Review::query()->with('user')->latest();
        if ($user->role !== 'admin') $q->where('user_id', $user->id);

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','user','content','sentiment','score','topics','created_at']);

            $q->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->id,
                        $r->user?->name,
                        $r->content,
                        $r->sentiment,
                        $r->score,
                        json_encode($r->topics ?? []),
                        $r->created_at?->toISOString(),
                    ]);
                }
            });

            fclose($out);
        }, 'reviews.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
