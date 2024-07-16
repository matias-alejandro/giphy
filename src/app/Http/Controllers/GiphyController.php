<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GiphyService;
use App\Models\FavoriteGif;
use Illuminate\Support\Facades\Log;

class GiphyController extends Controller
{
    protected GiphyService $giphyService;

    public function __construct(GiphyService $giphyService) {
        $this->giphyService = $giphyService;
    }

    public function index(Request $request) {
        try {
            $validatedData = $request->validate([
                'query' => 'required|string',
                'limit' => 'nullable|numeric',
                'offset' => 'nullable|numeric',
            ]);

            $query = $validatedData['query'];
            $limit = $validatedData['limit'] ?? null;
            $offset = $validatedData['offset'] ?? null;

            $data = $this->giphyService->search($query, $limit, $offset);
            return response()->json($data);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
		}
    }

    public function show($id) {
        try {
            $data = $this->giphyService->searchById($id);
            return response()->json(['data' => $data]);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
		}
    }

    public function addFavorite(Request $request) {
        try {
            $request->validate([
                'gif_id' => 'required|alpha_num',
                'alias' => 'required|string',
                'user_id' => 'required|numeric',
            ]);

            $params = $request->only('gif_id', 'alias', 'user_id');

            FavoriteGif::create($params);

            return response()->noContent();
        }
        catch (\Exception $e) {
            Log::error('Error adding favorite gif: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
