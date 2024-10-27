<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold">Welcome, {{ Auth::user()->name }}</span>
                </div>
                <div class="flex items-center">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                    <button onclick="document.getElementById('logout-form').submit()" 
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Profile Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Profile Information</h2>
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Joined:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                    </div>

                    <!-- Activity Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Recent Activity</h2>
                        <p>Last login: {{ Auth::user()->last_login ?? 'Never' }}</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Quick Actions</h2>
                        <div class="space-y-2">
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Edit Profile
                            </button>
                            <button class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- For API logout, you can use this JavaScript -->
    <script>
        async function apiLogout() {
            try {
                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.status) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                }
            } catch (error) {
                console.error('Logout failed:', error);
            }
        }
    </script>
</body>
</html>