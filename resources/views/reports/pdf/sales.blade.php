<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header p { margin: 2px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f5f5f5; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; }
        .summary td { border: none; padding: 4px 8px; }
        .summary .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\StoreSetting::getValue('store_name', 'TOKO') }}</h1>
        <p>{{ \App\Models\StoreSetting::getValue('store_address', '') }}</p>
        <p>Telp: {{ \App\Models\StoreSetting::getValue('store_phone', '') }}</p>
        <hr>
        <h2>LAPORAN PENJUALAN</h2>
        <p>Periode: {{ $report['period']['from'] }} s/d {{ $report['period']['to'] }}</p>
    </div>

    <table class="summary">
        <tr><td class="label">Total Transaksi:</td><td>{{ $report['summary']['total_transactions'] }}</td></tr>
        <tr><td class="label">Total Penjualan:</td><td>Rp {{ number_format($report['summary']['total_sales'], 0, ',', '.') }}</td></tr>
        <tr><td class="label">Total Diskon:</td><td>Rp {{ number_format($report['summary']['total_discount'], 0, ',', '.') }}</td></tr>
        <tr><td class="label">Total Pajak:</td><td>Rp {{ number_format($report['summary']['total_tax'], 0, ',', '.') }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-center">Transaksi</th>
                <th class="text-right">Penjualan</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Pajak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['daily_summary'] as $day)
            <tr>
                <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $day->total_trx }}</td>
                <td class="text-right">Rp {{ number_format($day->total_sales, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($day->total_discount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($day->total_tax, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:30px; text-align:right; color:#999; font-size:9px;">
        Dicetak: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </p>
</body>
</html>