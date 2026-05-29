@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center mx-auto mb-4 shadow-lg"><img src="/img/logo_tens2.jpg" alt="Tens Coffee" class="w-full h-full object-contain p-3"></div>
            <h2 class="text-3xl font-bold text-gray-800">Buat Akun</h2>
            <p class="text-gray-500 mt-1">Daftar untuk mulai memesan kopi</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border p-8">
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Nama</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">👤</span>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">📧</span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">🔒</span>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">🔒</span>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                            class="w-full pl-10 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition font-medium shadow-md active:scale-[0.98]">
                    Daftar
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium hover:underline">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection