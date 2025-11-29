@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('letters.index') }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Detail Surat</h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('letters.edit', $letter) }}" 
                   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('letters.destroy', $letter) }}" 
                      method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Jenis Surat Badge -->
        <div class="mb-6">
            @if($letter->type == 'in')
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    📥 Surat Masuk
                </span>
            @else
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                    📤 Surat Keluar
                </span>
            @endif
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nomor Surat -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Surat</label>
                <p class="text-lg text-gray-900">{{ $letter->no_surat ?? '-' }}</p>
            </div>

            <!-- Tanggal -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Surat</label>
                <p class="text-lg text-gray-900">
                    {{ \Carbon\Carbon::parse($letter->tanggal)->format('d F Y') }}
                </p>
            </div>

            <!-- Kategori -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                <p class="text-lg text-gray-900">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                        {{ $letter->category->name ?? '-' }}
                    </span>
                </p>
            </div>

            <!-- Dibuat Oleh -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Dibuat Oleh</label>
                <p class="text-lg text-gray-900">{{ $letter->creator->name ?? '-' }}</p>
            </div>

            <!-- Pengirim -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Pengirim</label>
                <p class="text-lg text-gray-900">{{ $letter->pengirim ?? '-' }}</p>
            </div>

            <!-- Penerima -->
            <div class="border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Penerima</label>
                <p class="text-lg text-gray-900">{{ $letter->penerima ?? '-' }}</p>
            </div>

            <!-- Perihal (Full Width) -->
            <div class="md:col-span-2 border-b border-gray-200 pb-4">
                <label class="block text-sm font-medium text-gray-500 mb-2">Perihal</label>
                <p class="text-base text-gray-900 leading-relaxed">{{ $letter->perihal }}</p>
            </div>

            <!-- File -->
            @if($letter->file_path)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-3">File Lampiran</label>
                <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0">
                                @php
                                    $extension = pathinfo($letter->file_path, PATHINFO_EXTENSION);
                                @endphp
                                @if($extension == 'pdf')
                                    <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                    </svg>
                                @else
                                    <svg class="w-10 h-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ basename($letter->file_path) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ strtoupper($extension) }} File
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('letters.download', $letter) }}" 
                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Metadata (Full Width) -->
            <div class="md:col-span-2 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                    <div>
                        <span class="font-medium">Dibuat:</span> 
                        {{ $letter->created_at->format('d F Y, H:i') }} WIB
                    </div>
                    <div>
                        <span class="font-medium">Terakhir diupdate:</span> 
                        {{ $letter->updated_at->format('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
        <a href="{{ route('letters.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition duration-200">
            Kembali ke Daftar
        </a>
        <div class="flex space-x-2">
            @if($letter->file_path)
            <a href="{{ route('letters.download', $letter) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                Download File
            </a>
            @endif
            <a href="{{ route('letters.edit', $letter) }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                Edit Surat
            </a>
        </div>
    </div>
</div>
@endsection