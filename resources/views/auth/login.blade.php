@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold mb-4">Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mt-1 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mt-1 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mb-4">
            <label class="inline-flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="mr-2">
                Remember me
            </label>
            <a href="#" class="text-sm text-indigo-600 hover:underline">Forgot password?</a>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Login</button>
        </div>
    </form>
</div>
@endsection
