<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Menampilkan halaman bantuan
     */
    public function help()
    {
        return view('dashboard::pages.help');
    }
    
    /**
     * Menampilkan halaman syarat dan ketentuan
     */
    public function terms()
    {
        return view('dashboard::pages.terms');
    }
    
    /**
     * Menampilkan halaman kebijakan privasi
     */
    public function privacy()
    {
        return view('dashboard::pages.privacy');
    }
}
