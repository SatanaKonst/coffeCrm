<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('crm.dashboard', [
            'client' => $request->attributes->get('client'),
            'isAdmin' => $request->attributes->get('isAdmin'),
        ]);
    }
}
