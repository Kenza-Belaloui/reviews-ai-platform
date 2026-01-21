<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AnalyzerManager;
use Illuminate\Http\Request;

class AnalyzeController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'text' => ['required','string','min:3','max:2000'],
        ]);

        $ai = (new AnalyzerManager())->driver();

        return response()->json($ai->analyze($data['text']));
    }
}
