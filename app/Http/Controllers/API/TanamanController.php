<?php

namespace App\Http\Controllers\API;


use App\Models\TanamanToga;
use Illuminate\Http\Request;

class TanamanController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        if (!$this->checkPermission($user, ['user'], ['view_tanaman::toga'])) {
            return $this->sendError('Anda tidak memiliki akses.', [], 403);
        }
        $tanaman = TanamanToga::all();
        return $this->sendResponse($tanaman, 'Data tanaman berhasil diambil.');
    }
}
