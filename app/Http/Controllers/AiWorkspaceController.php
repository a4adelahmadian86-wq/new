<?php

namespace App\Http\Controllers;

use App\Services\CapabilityService;
use Illuminate\View\View;

class AiWorkspaceController extends Controller
{
    public function history(CapabilityService $capabilities): View
    {
        $user = auth()->user();

        return view('ai.history', [
            'capabilities' => $capabilities->forUser($user),
            'title' => 'تاریخچه AI',
        ]);
    }

    public function quota(CapabilityService $capabilities): View
    {
        $user = auth()->user();

        return view('ai.quota', [
            'capabilities' => $capabilities->forUser($user),
            'title' => 'سهمیه AI',
        ]);
    }
}
