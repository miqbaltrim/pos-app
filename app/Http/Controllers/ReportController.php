<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function sales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();

        $report = $this->reportService->salesReport($from, $to);
        return view('reports.sales', compact('report'));
    }

    public function salesPdf(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();
        $report = $this->reportService->salesReport($from, $to);

        $pdf = Pdf::loadView('reports.pdf.sales', compact('report'));
        return $pdf->download("laporan-penjualan-{$from}-{$to}.pdf");
    }

    public function profit(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();

        $report = $this->reportService->profitReport($from, $to);
        return view('reports.profit', compact('report'));
    }

    public function stock()
    {
        $report = $this->reportService->stockReport();
        return view('reports.stock', compact('report'));
    }

    public function purchases(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();

        $report = $this->reportService->purchaseReport($from, $to);
        return view('reports.purchases', compact('report'));
    }
}