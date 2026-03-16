<?php

namespace App\Http\Controllers;

use App\Services\ReportService;

class DashboardController extends Controller
{
    public function index(ReportService $reportService)
    {
        $summary = $reportService->dashboardSummary();
        return view('dashboard', compact('summary'));
    }
}