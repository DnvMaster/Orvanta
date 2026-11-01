<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
        //
    }

    public function index(Request $request)
    {
        $this->authorize('dashboard.view');
        $statistics = $this->dashboardService->getOverview($request->user());
        return view('orvanta.dashboard', $statistics);
    }
}