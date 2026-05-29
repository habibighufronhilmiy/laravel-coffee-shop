@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center mx-auto mb-4 shadow-lg"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-3"></div>
            <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
            <p class="text-gray-500 mt-1">Login untuk memesan kopi favoritmu</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border p-8">
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 flex items-center gap-2">
                    <span>✕</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm border border-green-100 flex items-center gap-2">
                    <span>✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">📧</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">🔒</span>
                        <input type="password" name="password" required placeholder="Masukkan password"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md active:scale-[0.98]">
                    Login
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-400">atau</span>
                </div>
            </div>

            <a href="{{ route('google.login') }}"
                class="flex items-center justify-center gap-3 w-full bg-white border border-gray-200 text-gray-700 py-3 rounded-xl hover:bg-gray-50 transition font-medium active:scale-[0.98] shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Login dengan Google
            </a>

            <p class="mt-6 text-center text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium hover:underline">Daftar</a>
            </p>
        </div>
    </div>
</div>
@endsection