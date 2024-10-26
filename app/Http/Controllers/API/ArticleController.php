<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use App\Models\Favorite;
use Cache;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    public function index()
    {
        $article = Article::query();
        $filter = request("filter", 'terbaru');
        switch ($filter) {
            case 'populer':
                $article->orderBy('total_view', 'desc');
                break;
            case 'terlama':
                $article->orderBy('created_at', 'asc');
                break;
            case 'terbaru':
            default:
                $article->orderBy('created_at', 'desc');
        }
        $search = request('search', '');
        if ($search) {
            $article->where('name', 'like', '%' . $search . '%');
        }

        $data = $article->get();

        if ($data->count() > 0) {
            return $this->sendResponse($data, "Berhasil mengambil data artikel.");
        } else {
            return $this->sendResponse([], "Data artikel tidak ditemukan.");
        }
    }

    /**
     * Add or remove the article from the authenticated user's favorites.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function setFavorite(Request $request)
    {
        try {
            $request->validate([
                "article_id" => "integer|required",
            ], [
                "article_id.required" => "ID Artikel harus diisi.",
                "article_id.integer" => "ID Artikel harus berupa angka.",
            ]);

            $user = $request->user();
            $articleId = $request->input('article_id');

            // Check if the article exists
            $article = Article::findOrFail($articleId);

            // Check if the article is already favorited by the user
            $favorite = Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $articleId)
                ->where('favoritable_type', Article::class)
                ->first();

            if ($favorite) {
                // If already favorited, remove the favorite
                $favorite->delete();
                return $this->sendResponse(null, "Artikel berhasil dihapus dari favorit.");
            } else {
                // If not favorited, add to favorites
                Favorite::create([
                    'user_id' => $user->id,
                    'favoritable_id' => $articleId,
                    'favoritable_type' => Article::class,
                ]);
                return $this->sendResponse(null, "Artikel berhasil ditambahkan ke favorit.");
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Check if the article is favorited by the authenticated user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function isFavorite(Request $request)
    {
        try {
            $request->validate([
                "article_id" => "integer|required",
            ], [
                "article_id.required" => "ID Artikel harus diisi.",
                "article_id.integer" => "ID Artikel harus berupa angka.",
            ]);

            $user = $request->user();
            $articleId = $request->input('article_id');

            // Check if the article exists
            $article = Article::findOrFail($articleId);

            // Check if the article is already favorited by the user
            $favorite = Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $articleId)
                ->where('favoritable_type', Article::class)
                ->first();

            if ($favorite) {
                return $this->sendResponse(true, "Artikel sudah difavoritkan.");
            } else {
                return $this->sendResponse(false, "Artikel belum difavoritkan.");
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }
    }

    public function show($id)
    {
        $article = Article::find($id);
        if ($article) {
            $userId = auth()->id(); // Get the authenticated user's ID
            $cacheKey = "article_view_{$id}_user_{$userId}";

            // Check if the user has viewed this article in the last 5 minutes
            if (!Cache::has($cacheKey)) {
                // Increment the total_view count
                $article->increment('total_view');

                // Store a cache entry to prevent multiple views within 5 minutes
                Cache::put($cacheKey, true, now()->addMinutes(5));
            }
            return $this->sendResponse($article, "Berhasil mengambil data artikel.");
        } else {
            return $this->sendError("Data artikel tidak ditemukan.", 404);
        }
    }
}
