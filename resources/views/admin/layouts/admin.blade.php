<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            height: 100vh;
            background: #212529;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            transition: all 0.3s;
        }

        .sidebar a {
            color: #adb5bd;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar .active {
            background: #0d6efd;
            color: #fff;
        }

        .sidebar a.dropdown-toggle::after {
            content: "";
            border: solid currentColor;
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 3px;
            transform: rotate(45deg);
            float: right;
            margin-top: 8px;
            opacity: .7;
        }

        .sidebar a.dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(-135deg);
            margin-top: 12px;
        }

        .sidebar .collapse a {
            color: #d1d5db;
        }

        .sidebar .collapse a.active {
            color: #fff;
            background: rgba(13, 110, 253, .25);
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer {
            background: #fff;
            border-top: 1px solid #dee2e6;
            padding: 10px 20px;
            text-align: center;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')

    <div class="content">
        {{-- Header --}}
        @include('admin.layouts.header')

        {{-- Main Content --}}
        <main class="mt-4">


            <!-- Success Alert -->
            @if (session('success'))
                <div id="flash-success" class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Error Alert -->
            @if (session('error'))
                <div id="flash-error" class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')

        </main>

        {{-- Footer --}}
        @include('admin.layouts.footer')
    </div>
    {{-- @include('admin.components.alerts') --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.querySelector('#sidebarToggle')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
    @stack('styles')
    @stack('scripts')
    @include('admin.partials.universal-delete-modal')
</body>

</html>
<script>
    // Auto-hide success message
    setTimeout(() => {
        const flashSuccess = document.getElementById('flash-success');
        if (flashSuccess) {
            flashSuccess.classList.remove('show');
            flashSuccess.classList.add('fade');
            flashSuccess.style.display = 'none';
        }
    }, 3000); // 3 seconds

    // Auto-hide error message
    setTimeout(() => {
        const flashError = document.getElementById('flash-error');
        if (flashError) {
            flashError.classList.remove('show');
            flashError.classList.add('fade');
            flashError.style.display = 'none';
        }
    }, 3000); // 3 seconds
</script>
