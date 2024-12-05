<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends BaseController
{
    /**
     * Get all faqs
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $faqs = Faq::all();
        return $this->sendResponse($faqs, 'Berhasil mengambil data.');
    }
}
