<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Penandatangan Aktif</div>
                    <div class="text-3xl font-semibold text-gray-800 mt-1">{{ $stats['active_signers'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['inactive_signers'] }} nonaktif</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Akun Staf Aktif</div>
                    <div class="text-3xl font-semibold text-gray-800 mt-1">{{ $stats['active_staff'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['inactive_staff'] }} nonaktif</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">QR Digenerate (30 Hari)</div>
                    <div class="text-3xl font-semibold text-gray-800 mt-1">{{ $stats['qr_last_30_days'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total QR Digenerate</div>
                    <div class="text-3xl font-semibold text-gray-800 mt-1">{{ $stats['qr_total'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Akses Cepat</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('signers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Kelola Penandatangan
                    </a>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Kelola Akun Staf
                    </a>
                    <a href="{{ route('logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Log Audit
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Generate QR Terbaru</h3>
                @if ($recentLogs->isEmpty())
                    <p class="text-gray-500 text-sm">Belum ada data generate QR.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Signer</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Perihal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Digenerate Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($recentLogs as $log)
                                    <tr>
                                        <td class="px-4 py-2">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                                        <td class="px-4 py-2">{{ $log->signer->name }}</td>
                                        <td class="px-4 py-2">{{ $log->perihal ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $log->generator->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('logs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Lihat semua log →</a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
