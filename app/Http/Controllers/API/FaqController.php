<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends BaseController
{
    public function index()
    {
        $faqs = Faq::all();
        return $this->sendResponse($faqs, 'Berhasil mengambil data.');
    }
}
