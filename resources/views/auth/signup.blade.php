<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <title>Register</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">Register</h2>
        @if ($errors->any())
            <p class="text-red-500 text-sm mb-4">{{ $errors->first() }}</p>
        @endif
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
    <div>
            <label class="block text-sm font-medium text-gray-700" >Name</label>
            <input type="text" name="name" class="border p-2 w-full rounded mb-4" required>
        <div>
            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="text" name="phone" class="border p-2 w-full rounded mb-4" required>
        </div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" class="border p-2 w-full rounded mb-4" required>
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" class="border p-2 w-full rounded mb-4" required>
        </div>
            {{-- <label class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" class="border p-2 w-full rounded mb-4">
                <option value="farmer">Farmer</option>
                <option value="admin">Admin</option>
            </select> --}}
    
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
        </form>
        <div >
            <p class="text-center text-sm text-gray-600 mt-4 pt-4">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login instead</a>.
            </p>
        </div>
    </div>
</body>
</html>