@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        <div class="text-sm text-gray-500">Ringkasan singkat sistem</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
            <div class="text-sm text-indigo-700 font-semibold">Surat Masuk</div>
            <div class="text-2xl font-bold text-indigo-900">{{ $incomingCount }}</div>
        </div>
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
            <div class="text-sm text-blue-700 font-semibold">Surat Keluar</div>
            <div class="text-2xl font-bold text-blue-900">{{ $outgoingCount }}</div>
        </div>
        <div class="p-4 bg-green-50 rounded-lg border border-green-100">
            <div class="text-sm text-green-700 font-semibold">Kategori</div>
            <div class="text-2xl font-bold text-green-900">{{ $categoryCount }}</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
            <div class="text-sm text-gray-700 font-semibold">Pengguna</div>
            <div class="text-2xl font-bold text-gray-900">{{ $userCount }}</div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Surat</h3>
        @if($recent->isEmpty())
            <p class="text-sm text-gray-500">Belum ada surat.</p>
        @else
            <div class="overflow-x-auto bg-white border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">No</th>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">Jenis</th>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">No. Surat</th>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">Perihal</th>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">Kategori</th>
                            <th class="px-4 py-2 text-left text-sm text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recent as $idx => $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($item->type === 'in')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Masuk</span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Keluar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->no_surat ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ Str::limit($item->perihal, 50) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
