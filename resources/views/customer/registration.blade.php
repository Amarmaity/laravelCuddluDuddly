@extends('customer/layouts.masterLayout')

@section('title', 'Register')

@php
    $hideHeaderFooter = true;
@endphp

@section('content')
    <div class="mx-auto bg-white shadow-md flex flex-col justify-center items-center   rounded-lg p-6 mt-36">

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

        <form method="POST" class="grid w-[800px]  grid-cols-2 gap-2.5" action="{{ route('store-customer') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="first_name" class="block text-gray-700 font-medium">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('first_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="last_name" class="block text-gray-700 font-medium">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('last_name')
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
                <input id="mobile" type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('phone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="dob" class="block text-gray-700 font-medium">Date Of Birth</label>
                <input id="dob" type="date" name="dob" value="{{ old('dob') }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                @error('dob')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            

            <!-- Hidden User Type -->
            {{-- <input type="hidden" name="usertype" value="customer"> --}}

            <div class="mb-4">
                <label for="gender" class="block text-gray-700 font-medium">Gender</label>
                <select id="gender" name="gender" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">-- Select Gender --</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
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

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <!-- Submit Button -->
            <div class="col-span-2 flex justify-center mt-4">
                <button type="submit"
                    class="bg-pink-600 w-[250px] hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition">
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