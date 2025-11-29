@extends('layouts.app')

@section('title', 'Edit Surat')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <a href="{{ route('letters.index') }}" 
               class="text-gray-600 hover:text-gray-800 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Edit Surat</h2>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('letters.update', $letter) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Jenis Surat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Surat <span class="text-red-500">*</span>
                </label>
                <select name="type" 
                    required
                    @class([
                        'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                        'border-red-500' => $errors->has('type'),
                        'border-gray-300' => !$errors->has('type'),
                    ])>
                    <option value="">Pilih Jenis Surat</option>
                    <option value="in" {{ old('type', $letter->type) == 'in' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="out" {{ old('type', $letter->type) == 'out' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_id" 
                    required
                    @class([
                        'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                        'border-red-500' => $errors->has('category_id'),
                        'border-gray-300' => !$errors->has('category_id'),
                    ])>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ old('category_id', $letter->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Surat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat
                </label>
                  <input type="text" 
                       name="no_surat" 
                       value="{{ old('no_surat', $letter->no_surat) }}"
                       placeholder="Contoh: 001/SK/2024"
                       @class([
                           'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                           'border-red-500' => $errors->has('no_surat'),
                           'border-gray-300' => !$errors->has('no_surat'),
                       ])>
                @error('no_surat')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Surat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Surat <span class="text-red-500">*</span>
                </label>
                    <input type="date" 
                       name="tanggal" 
                       value="{{ old('tanggal', $letter->tanggal) }}"
                       required
                       @class([
                           'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                           'border-red-500' => $errors->has('tanggal'),
                           'border-gray-300' => !$errors->has('tanggal'),
                       ])>
                @error('tanggal')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pengirim -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pengirim
                </label>
                    <input type="text" 
                       name="pengirim" 
                       value="{{ old('pengirim', $letter->pengirim) }}"
                       placeholder="Nama pengirim surat"
                       @class([
                           'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                           'border-red-500' => $errors->has('pengirim'),
                           'border-gray-300' => !$errors->has('pengirim'),
                       ])>
                @error('pengirim')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penerima -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Penerima
                </label>
                    <input type="text" 
                       name="penerima" 
                       value="{{ old('penerima', $letter->penerima) }}"
                       placeholder="Nama penerima surat"
                       @class([
                           'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                           'border-red-500' => $errors->has('penerima'),
                           'border-gray-300' => !$errors->has('penerima'),
                       ])>
                @error('penerima')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Perihal -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Perihal <span class="text-red-500">*</span>
                </label>
                <textarea name="perihal" 
                          rows="3"
                          required
                          placeholder="Isi perihal surat..."
                          @class([
                              'w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                              'border-red-500' => $errors->has('perihal'),
                              'border-gray-300' => !$errors->has('perihal'),
                          ])>{{ old('perihal', $letter->perihal) }}</textarea>
                @error('perihal')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    File Surat (PDF, JPG, PNG - Max 2MB)
                </label>
                
                @if($letter->file_path)
                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-blue-600 mr-2">📎</span>
                            <span class="text-sm text-gray-700">File saat ini: {{ basename($letter->file_path) }}</span>
                        </div>
                        <a href="{{ route('letters.download', $letter) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800">
                            Download
                        </a>
                    </div>
                @endif

                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                <span>Upload file baru</span>
                                <input type="file" 
                                       name="file" 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="sr-only"
                                       onchange="displayFileName(this)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, JPG, PNG hingga 2MB</p>
                        <p class="text-xs text-gray-500">(Kosongkan jika tidak ingin mengubah file)</p>
                        <p id="file-name" class="text-sm text-indigo-600 font-medium mt-2"></p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('letters.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                Update Surat
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = '📎 ' + fileName;
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endpush
@endsection
