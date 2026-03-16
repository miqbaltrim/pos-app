<x-layouts.admin>
    <x-slot:header>Laporan Penjualan</x-slot:header>
    <x-slot:title>Lap. Penjualan</x-slot:title>

    <div class="space-y-6">
        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <form class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Dari</label>
                    <input type="date" name="from" value="{{ $report['period']['from'] }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ $report['period']['to'] }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
                <a href="{{ route('reports.sales.pdf', ['from' => $report['period']['from'], 'to' => $report['period']['to']]) }}"
                   class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">Download PDF</a>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="text-2xl font-bold mt-1">{{ $report['summary']['total_transactions'] }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-gray-500">Total Penjualan</p>
                <p class="text-2xl font-bold mt-1">Rp {{ number_format($report['summary']['total_sales'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-gray-500">Total Diskon</p>
                <p class="text-2xl font-bold mt-1 text-red-600">Rp {{ number_format($report['summary']['total_discount'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl border p-5">
                <p class="text-sm text-gray-500">Rata-rata/Trx</p>
                <p class="text-2xl font-bold mt-1">Rp {{ number_format($report['summary']['average_per_transaction'], 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Daily Summary Table --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-5 border-b"><h3 class="font-semibold">Ringkasan Harian</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 bg-gray-50 border-b">
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-center">Transaksi</th>
                            <th class="px-5 py-3 text-right">Penjualan</th>
                            <th class="px-5 py-3 text-right">Diskon</th>
                            <th class="px-5 py-3 text-right">Pajak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['daily_summary'] as $day)
                        <tr class="border-b border-gray-50">
                            <td class="px-5 py-3 font-medium">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-center">{{ $day->total_trx }}</td>
                            <td class="px-5 py-3 text-right font-medium">Rp {{ number_format($day->total_sales, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right text-red-500">Rp {{ number_format($day->total_discount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right">Rp {{ number_format($day->total_tax, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>