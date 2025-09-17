@extends('layouts.masterLayout')

@section('title', 'Register')

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

        <h2 class="text-2xl font-bold text-pink-600 mb-4">Create Account</h2>

        <form method="POST" action="{{ route('store-customer') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Mobile -->
            <div class="mb-4">
                <label for="mobile" class="block text-gray-700 font-medium">Mobile</label>
                <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('mobile')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Hidden User Type -->
            <input type="hidden" name="usertype" value="customer">

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Register
                </button>
            </div>
        </form>

        <!-- Already have an account -->
        <div class="mt-4 text-center text-gray-600">
            Already have an account?
            <a href="{{ url('customer-login') }}" class="text-pink-600 hover:text-pink-700 font-medium">Login</a>
            or
            <a href="{{ url('/') }}" class="text-pink-600 hover:text-pink-700 font-medium">Go Home</a>
        </div>
    </div>
@endsection
