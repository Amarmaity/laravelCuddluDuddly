<header class="bg-white shadow-md px-6 py-4 flex items-center justify-between">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
        <a href="{{ url('/') }}"
            class="text-2xl font-extrabold text-pink-600 tracking-wide hover:text-pink-700 transition">
            CuddlyDuddly
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="hidden md:flex items-center space-x-8 font-medium">
        <a href="#" class="text-gray-700 hover:text-pink-600 transition">About</a>
        <a href="#" class="text-gray-700 hover:text-pink-600 transition">Contact Us</a>
    </nav>

    <!-- Search Box -->
    <div class="flex-1 max-w-md mx-6 hidden sm:block">
        <form action="#" method="GET"
            class="flex items-center border border-gray-300 rounded-lg overflow-hidden shadow-sm">
            <input type="text" name="q" placeholder="Search..."
                class="w-full px-4 py-2 outline-none text-gray-700 focus:ring-2 focus:ring-pink-500">
            <button type="submit" class="px-5 py-2 bg-pink-600 hover:bg-pink-700 text-white transition">
                Search
            </button>
        </form>
    </div>

    <!-- Icons + Profile/Logout -->
    <div class="flex items-center space-x-6">
        <!-- Profile with Dropdown -->
        <div class="relative group">
            <a href="#" class="text-gray-600 hover:text-pink-600 transition">
                <i class="fas fa-user text-2xl"></i>
            </a>

            <!-- Dropdown -->
            <div
                class="absolute right-0 w-40 bg-white border rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200">
                @guest
                    <a href="{{ route('customer-register') }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600">Sign Up</a>
                    <a href="{{ route('login-customer') }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600">Login</a>
                @endguest

                @auth
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600">Profile</a>

                    <!-- ✅ Logout with confirmation -->
                    <form id="logout-form" action="{{ route('logout-web') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogout()"
                            class="w-full text-left px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        <!-- Cart -->
        <a href="#" class="relative text-gray-600 hover:text-pink-600 transition">
            <i class="fas fa-shopping-cart text-2xl"></i>
            <span class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-bold rounded-full px-2">
                {{ session('cart_count', 0) }}
            </span>
        </a>
    </div>
</header>

<!-- ✅ Logout Confirmation Script -->
<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            document.getElementById('logout-form').submit();
        }
    }
</script>