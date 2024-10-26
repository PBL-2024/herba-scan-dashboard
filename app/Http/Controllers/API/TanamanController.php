<?php

namespace App\Http\Controllers\API;


use App\Models\Favorite;
use App\Models\Plant;
use Cache;
use Illuminate\Http\Request;

class TanamanController extends BaseController
{
    /**
     * Get all plants.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $tanaman = Plant::query();
        $filter = request("filter", 'terbaru');
        switch ($filter) {
            case 'populer':
                $tanaman->orderBy('total_view', 'desc');
                break;
            case 'terlama':
                $tanaman->orderBy('created_at', 'asc');
                break;
            case 'terbaru':
            default:
                $tanaman->orderBy('created_at', 'desc');
        }
        $search = request('search', '');
        if ($search) {
            $tanaman->where('name', 'like', '%' . $search . '%');
        }

        $data = $tanaman->get();

        if ($data->count() > 0) {
            return $this->sendResponse($data, "Berhasil mengambil data tanaman.");
        } else {
            return $this->sendResponse([], "Data tanaman tidak ditemukan.");
        }
    }

    /**
     * Add or remove the plant from the authenticated user's favorites.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function setFavorite(Request $request)
    {
        try {
            $request->validate([
                "plant_id" => "integer|required",
            ], [
                "plant_id.required" => "ID tanaman harus diisi.",
                "plant_id.integer" => "ID tanaman harus berupa angka.",
            ]);

            $user = $request->user();
            $plantId = $request->input('plant_id');

            // Check if the plant exists
            $plant = Plant::findOrFail($plantId);

            // Check if the plant is already favorited by the user
            $favorite = Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $plantId)
                ->where('favoritable_type', Plant::class)
                ->first();

            if ($favorite) {
                // If already favorited, remove the favorite
                $favorite->delete();
                return $this->sendResponse(null, "Tanaman berhasil dihapus dari favorit.");
            } else {
                // If not favorited, add to favorites
                Favorite::create([
                    'user_id' => $user->id,
                    'favoritable_id' => $plantId,
                    'favoritable_type' => Plant::class,
                ]);
                return $this->sendResponse(null, "Tanaman berhasil ditambahkan ke favorit.");
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Check if the plant is favorited by the authenticated user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function isFavorite(Request $request)
    {
        try {
            $request->validate([
                "plant_id" => "integer|required",
            ], [
                "plant_id.required" => "ID tanaman harus diisi.",
                "plant_id.integer" => "ID tanaman harus berupa angka.",
            ]);

            $user = $request->user();
            $plantId = $request->input('plant_id');

            // Check if the plant exists
            $plant = Plant::findOrFail($plantId);

            // Check if the plant is already favorited by the user
            $favorite = Favorite::where('user_id', $user->id)
                ->where('favoritable_id', $plantId)
                ->where('favoritable_type', Plant::class)
                ->first();

            if ($favorite) {
                return $this->sendResponse(true, "Tanaman sudah difavoritkan.");
            } else {
                return $this->sendResponse(false, "Tanaman belum difavoritkan.");
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Get the plant by ID.
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $tanaman = Plant::find($id);
        if ($tanaman) {
            $userId = auth()->id(); // Get the authenticated user's ID
            $cacheKey = "plant_view_{$id}_user_{$userId}";

            // Check if the user has viewed this plant in the last 5 minutes
            if (!Cache::has($cacheKey)) {
                // Increment the total_view count
                $tanaman->increment('total_view');

                // Store a cache entry to prevent multiple views within 5 minutes
                Cache::put($cacheKey, true, now()->addMinutes(5));
            }
            return $this->sendResponse($tanaman, "Berhasil mengambil data tanaman.");
        } else {
            return $this->sendError("Data tanaman tidak ditemukan.", 404);
        }
    }

}
