<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function menu()
    {
        return view('pages.vetbot');
    }

    public function diagnostic()
    {
        return view('pages.diagnostic');
    }

    public function conseil()
    {
        return view('pages.conseil');
    }

}
