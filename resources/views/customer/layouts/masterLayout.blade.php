<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CuddlyDuddly')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="flex flex-col min-h-screen">

    <body class="flex flex-col min-h-screen">

        {{-- Header --}}
        @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
            @include('customer/layouts.header')
        @endif

        {{-- Page Content --}}
        <main class="flex-1 container mx-auto px-6 py-4">
            @yield('content')
        </main>

        {{-- Footer --}}
        @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
            @include('customer/layouts.footer')
        @endif

    </body>

</body>

</html>