<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulePageController extends Controller
{
    public function show(Request $request, string $slug)
    {
        return view('modules.empty', [
            'slug' => $slug,
            'title' => 'ماژول: '.$slug,
        ]);
    }
}
