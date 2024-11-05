<?php

namespace App\Http\Controllers\API;


use App\Models\Favorite;
use App\Models\Plant;
use App\Models\UnclassifiedPlant;
use Cache;
use Illuminate\Http\Request;
use Storage;
use Str;

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
            case 'terfavorit':
                $tanaman->withCount('favorites')->orderBy('favorites_count', 'desc');
                break;
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


    /**
     * Get the authenticated user's favorite plants.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function myUnclassifiedPlants(Request $request)
    {
        $user = $request->user();
        $plants = $user->unclassifiedPlants()->get();
        if ($plants->isNotEmpty()) {
            return $this->sendResponse($plants, "Berhasil mengambil data tanaman yang belum terklasifikasi.");
        }
        return $this->sendResponse(null, "Anda belum memiliki tanaman yang belum terklasifikasi.");
    }

    /**
     * Send an unclassified plant.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function sendUnclassifiedPlant(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'nama' => 'required|string|max:255',
                'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'nama.required' => 'Nama tanaman harus diisi.',
                'nama.string' => 'Nama tanaman harus berupa teks.',
                'nama.max' => 'Nama tanaman tidak boleh lebih dari 255 karakter.',
                'file.required' => 'File harus diunggah.',
                'file.file' => 'File harus berupa file yang valid.',
                'file.mimes' => 'File harus berformat jpeg, png, jpg, atau gif.',
                'file.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            ]);
        } catch (\Throwable $th) {
            return $this->sendError('Validation Error.', $th->getMessage(), 400);
        }

        $user = $request->user();

        // Store the uploaded file
        $path = $request->file('file')->store('unclassified_plants', 'public');

        // Create a new unclassified plant record
        $unclassifiedPlant = UnclassifiedPlant::create([
            'user_id' => $user->id,
            'nama' => $request->input('nama'),
            'file' => $path,
        ]);

        return $this->sendResponse($unclassifiedPlant, "Berhasil mengirim tanaman yang belum terklasifikasi.");
    }

    public function deleteUnclassifiedPlant(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Find the unclassified plant by ID
            $unclassifiedPlant = UnclassifiedPlant::find($id);

            if($unclassifiedPlant == null){
                throw new \Exception("Data tanaman yang belum terklasifikasi tidak ditemukan.", 404);
            }

            // Check if the authenticated user is authorized to delete the unclassified plant
            if ($unclassifiedPlant->user_id !== $user->id) {
                return $this->sendError('Unauthorized.', 'Anda tidak berwenang untuk menghapus tanaman ini.', 403);
            }

            // Delete the associated file from storage
            if ($unclassifiedPlant->file) {
                Storage::disk('public')->delete($unclassifiedPlant->file);
            }

            // Delete the unclassified plant record
            $unclassifiedPlant->delete();

            return $this->sendResponse(null, "Berhasil menghapus tanaman yang belum terklasifikasi.");
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage(), $th->getCode());
        }
    }

    public function getListNameUnclassifiedPlant()
    {
        $unclassifiedPlantNames = UnclassifiedPlant::distinct()->pluck('nama');

        // Truncate similar names
        $truncatedNames = $unclassifiedPlantNames->map(function ($name) {
            return Str::limit($name, 20); // Truncate to 20 characters
        })->unique();

        if ($truncatedNames->count() > 0) {
            return $this->sendResponse($truncatedNames, "Berhasil mengambil data tanaman yang belum terklasifikasi.");
        } else {
            return $this->sendResponse([], "Data tanaman yang belum terklasifikasi tidak ditemukan.");
        }
    }
}
