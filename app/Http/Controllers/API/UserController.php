<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class UserController extends BaseController
{
    /**
     * Get the authenticated user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        return $this->sendResponse($request->user(), "Berhasil mengambil data user.");
    }

    /**
     * Update the authenticated user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $messages = [
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah terdaftar.',
        ];
        // Validate the request
        try {
            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'email' => 'email|max:255|unique:users,email,' . $user->id,
            ], $messages);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }

        // Update the user with validated data
        $user->update($validatedData);

        return $this->sendResponse($user, "Berhasil mengupdate data user.");
    }

    /**
     * Update the authenticated user's avatar.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update_avatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'avatar.required' => 'Avatar tidak boleh kosong.',
                'avatar.image' => 'Avatar harus berupa gambar.',
                'avatar.mimes' => 'Avatar harus berformat jpeg, png, jpg, atau gif.',
                'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 2MB.',
            ]);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), $th->getCode());
        }

        $user = $request->user();

        // Store the uploaded file
        $path = $request->file('avatar')->store('avatars', 'public');

        // Delete the old avatar if it exists
        if ($user->image_url) {
            Storage::disk('public')->delete($user->image_url);
        }

        // Update the user's avatar path
        $user->image_url = $path;
        $user->save();

        return $this->sendResponse($user, "Berhasil mengupdate avatar.");
    }

    /**
     * Get the authenticated user's favorites.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function favorites(Request $request)
    {
        $user = $request->user();

        // Retrieve favorite plants and articles with detailed data
        $favoritePlants = $user->favorites()
            ->where('favoritable_type', Plant::class)
            ->with('favoritable') // Eager load the related Plant model
            ->get()
            ->map(function ($favorite) {
                return $favorite->favoritable;
            });

        $favoriteArticles = $user->favorites()
            ->where('favoritable_type', Article::class)
            ->with('favoritable') // Eager load the related Article model
            ->get()
            ->map(function ($favorite) {
                return $favorite->favoritable;
            });

        // Combine the results
        $favorites = [
            'plants' => $favoritePlants,
            'articles' => $favoriteArticles,
        ];

        return $this->sendResponse($favorites, 'Berhasil mengambil data favorit.');
    }
}
