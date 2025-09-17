@extends('layouts.masterLayout')

@section('title', 'Login')

@php
    $hideHeaderFooter = true;
@endphp

@section('content')
<div class="max-w-md mx-auto bg-white shadow-md rounded-lg p-6 mt-36">

    {{-- SweetAlert Messages --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
            });
        </script>
    @endif

    <h2 class="text-2xl font-bold text-pink-600 mb-4">Login</h2>

    <form method="POST" action="{{route ('login')}}">
        @csrf

        <!-- Email or Mobile -->
        <div class="mb-4">
            <label for="login" class="block text-gray-700 font-medium">Email or Mobile</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            @error('login')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-medium">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition">
                Login
            </button>
        </div>
    </form>

    <!-- Forgot / Register -->
    <div class="mt-4 text-center text-gray-600">
        Don't have an account?
        <a href="{{ route('customer-register') }}" class="text-pink-600 hover:text-pink-700 font-medium">Register</a>
        or
        <a href="{{ url('/') }}" class="text-pink-600 hover:text-pink-700 font-medium">Go Home</a>
    </div>
</div>
@endsection
