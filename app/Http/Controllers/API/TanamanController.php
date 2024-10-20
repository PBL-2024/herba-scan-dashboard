<?php

namespace App\Http\Controllers\API;


use App\Models\TanamanToga;
use Illuminate\Http\Request;

class TanamanController extends BaseController
{
    public function index()
    {
        $tanaman = TanamanToga::all();
        return $this->sendResponse($tanaman, 'Data tanaman berhasil diambil.');
    }
}
