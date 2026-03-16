<x-guest-layout>
    <div class="text-center py-16">
        <p class="text-8xl font-bold text-gray-200">500</p>
        <h2 class="mt-4 text-xl font-semibold text-gray-700">Kesalahan Sistem</h2>
        <p class="mt-2 text-gray-500">{{ $message ?? 'Terjadi kesalahan, silakan coba lagi.' }}</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">&larr; Kembali</a>
    </div>
</x-guest-layout>