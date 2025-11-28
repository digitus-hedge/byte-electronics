<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Http\Request;

class FrontendProfileController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Profile/Account', [
            'user' => $request->user(),
            'addresses' => [
                ['type' => 'Work Address'],
                ['type' => 'Home Address'],
                ['type' => 'Secondary Address']
            ]
        ]);
    }
}
